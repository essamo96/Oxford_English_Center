<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\GroupStudents;
use App\Models\GroupStudentsFees;
use App\Models\Groups;
use App\Models\SalaryCloseLog;
use App\Models\Students;
use App\Models\TeacherSalaryForm;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Aggregates every figure shown on the Financial Center dashboard straight from
 * the live tables (fees / payroll / close-logs). Nothing here is dummy data.
 *
 * The whole payload is cached for 60s so the 60s auto-refresh on the front-end
 * stays snappy and never hammers the per-ledger dues computation.
 */
class FinancialDashboardService
{
    protected FinancialService $financial;

    /** Memoised per-request so overview() and tables() share one dues pass. */
    private ?array $duesCache = null;

    private array $monthsAr = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];

    public function __construct(FinancialService $financial)
    {
        $this->financial = $financial;
    }

    public function payload(): array
    {
        return Cache::remember('fin_dashboard_payload', 60, function () {
            return [
                'overview'     => $this->overview(),
                'charts'       => $this->charts(),
                'payroll'      => $this->payroll(),
                'months'       => $this->monthsStatus(),
                'activity'     => $this->activityFeed(),
                'tables'       => $this->tables(),
                'generated_at' => now()->format('Y-m-d H:i'),
            ];
        });
    }

    public function forget(): void
    {
        Cache::forget('fin_dashboard_payload');
    }

    // ---------------------------------------------------------------- SECTION 1
    private function overview(): array
    {
        $now  = now();
        $dues = $this->dues();

        $payments = (float) GroupStudentsFees::confirmed()->whereNull('deleted_at')
            ->where('transaction_type', 'payment')->sum('transaction_amount');
        $refunds  = (float) GroupStudentsFees::confirmed()->whereNull('deleted_at')
            ->where('transaction_type', 'refund')->sum(DB::raw('ABS(transaction_amount)'));

        $monthPayments = (float) GroupStudentsFees::confirmed()->whereNull('deleted_at')
            ->where('transaction_type', 'payment')
            ->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)
            ->sum('transaction_amount');
        $monthRefunds = (float) GroupStudentsFees::confirmed()->whereNull('deleted_at')
            ->where('transaction_type', 'refund')
            ->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)
            ->sum(DB::raw('ABS(transaction_amount)'));

        $credit = (float) GroupStudentsFees::confirmed()->whereNull('deleted_at')
            ->where('transaction_type', 'credit')->sum('transaction_amount');

        $txThisMonth = (int) GroupStudentsFees::whereNull('deleted_at')
            ->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count();

        // Expenses = general operating expenses + teacher payroll for the month
        $generalThisMonth = (float) Expense::whereNull('deleted_at')
            ->whereYear('expense_date', $now->year)->whereMonth('expense_date', $now->month)->sum('amount');
        $payrollThisMonth = (float) TeacherSalaryForm::whereNull('deleted_at')
            ->where('year', $now->year)->where('month', $now->month)->sum('net_amount');
        $expensesThisMonth = $generalThisMonth + $payrollThisMonth;
        $collectedThisMonth = $monthPayments - $monthRefunds;

        return [
            'total_revenue'           => round($payments - $refunds, 2),
            'outstanding'             => $dues['total'],
            'collected_this_month'    => round($collectedThisMonth, 2),
            'credit_balance'          => round($credit, 2),
            'active_with_dues'        => $dues['count'],
            'transactions_this_month' => $txThisMonth,
            'expenses_this_month'     => round($expensesThisMonth, 2),
            'net_this_month'          => round($collectedThisMonth - $expensesThisMonth, 2),
        ];
    }

    // ---------------------------------------------------------------- SECTION 2
    private function charts(): array
    {
        $now    = now();
        $start  = $now->copy()->startOfMonth()->subMonths(11);
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $m = $start->copy()->addMonths($i);
            $months[$m->format('Y-m')] = $this->monthsAr[(int) $m->format('n')] . ' ' . $m->format('y');
        }
        $keys = array_keys($months);

        // Net revenue (payment - refund) by month — verified only
        $rev = GroupStudentsFees::confirmed()->whereNull('deleted_at')
            ->where('created_at', '>=', $start->copy()->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%Y-%m') ym,
                SUM(CASE WHEN transaction_type='payment' THEN transaction_amount
                         WHEN transaction_type='refund'  THEN -ABS(transaction_amount) ELSE 0 END) net,
                SUM(CASE WHEN transaction_type='payment' THEN transaction_amount ELSE 0 END) collected")
            ->groupBy('ym')->get()->keyBy('ym');

        // Submitted-but-unverified (outstanding/pending) by month
        $pend = GroupStudentsFees::whereNull('deleted_at')->where('audit_status', 'pending')
            ->where('created_at', '>=', $start->copy()->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%Y-%m') ym,
                SUM(GREATEST(COALESCE(remaining_amount,0),COALESCE(student_fee_paid,0))) amt")
            ->groupBy('ym')->pluck('amt', 'ym');

        // Payroll by period
        $pay = TeacherSalaryForm::whereNull('deleted_at')
            ->selectRaw('year, month, SUM(net_amount) amt')->groupBy('year', 'month')->get()
            ->keyBy(fn ($r) => sprintf('%04d-%02d', $r->year, $r->month));

        // General operating expenses by month
        $exp = Expense::whereNull('deleted_at')
            ->where('expense_date', '>=', $start->copy()->startOfMonth())
            ->selectRaw("DATE_FORMAT(expense_date,'%Y-%m') ym, SUM(amount) amt")
            ->groupBy('ym')->pluck('amt', 'ym');

        $revenue = $collected = $outstanding = $expenses = $payrollTrend = [];
        foreach ($keys as $k) {
            $revenue[]     = round((float) optional($rev->get($k))->net, 2);
            $collected[]   = round((float) optional($rev->get($k))->collected, 2);
            $outstanding[] = round((float) ($pend[$k] ?? 0), 2);
            $payroll       = round((float) optional($pay->get($k))->amt, 2);
            $general       = round((float) ($exp[$k] ?? 0), 2);
            $payrollTrend[] = $payroll;
            $expenses[]    = round($payroll + $general, 2); // total outflow = payroll + general
        }

        // Daily cash flow for the current month
        $flow = GroupStudentsFees::confirmed()->whereNull('deleted_at')
            ->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)
            ->selectRaw("DATE(created_at) d,
                SUM(CASE WHEN transaction_type='payment' THEN transaction_amount
                         WHEN transaction_type='refund'  THEN -ABS(transaction_amount) ELSE 0 END) net")
            ->groupBy('d')->pluck('net', 'd');
        $flowLabels = $flowData = [];
        $daysInMonth = (int) $now->format('t');
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = $now->copy()->startOfMonth()->addDays($d - 1)->format('Y-m-d');
            $flowLabels[] = (string) $d;
            $flowData[]   = round((float) ($flow[$date] ?? 0), 2);
        }

        return [
            'labels'           => array_values($months),
            'revenue'          => $revenue,
            'collected'        => $collected,
            'outstanding'      => $outstanding,
            'expenses'         => $expenses,
            'payroll_trend'    => $payrollTrend,
            'cashflow_labels'  => $flowLabels,
            'cashflow'         => $flowData,
        ];
    }

    // ---------------------------------------------------------------- SECTION 3
    private function payroll(): array
    {
        $now = now();
        return [
            'paid'        => round((float) TeacherSalaryForm::whereNull('deleted_at')->where('is_received', 1)->sum('net_amount'), 2),
            'pending'     => round((float) TeacherSalaryForm::whereNull('deleted_at')->where('is_received', 0)->sum('net_amount'), 2),
            'awaiting'    => (int) TeacherSalaryForm::whereNull('deleted_at')->where('is_received', 0)->distinct('teacher_id')->count('teacher_id'),
            'this_month'  => round((float) TeacherSalaryForm::whereNull('deleted_at')->where('year', $now->year)->where('month', $now->month)->sum('net_amount'), 2),
        ];
    }

    // ---------------------------------------------------------------- SECTION 4
    private function monthsStatus(): array
    {
        $now       = now();
        $closed    = (int) SalaryCloseLog::count();
        $closedSet = SalaryCloseLog::select('year', 'month')->get()->map(fn ($r) => $r->year . '-' . $r->month)->all();
        $periods   = TeacherSalaryForm::whereNull('deleted_at')->select('year', 'month')->distinct()->get();
        $open      = $periods->filter(fn ($p) => !in_array($p->year . '-' . $p->month, $closedSet, true))->count();
        $last      = SalaryCloseLog::orderByDesc('created_at')->first();

        return [
            'closed'         => $closed,
            'open'           => $open,
            'current_period' => ($this->monthsAr[(int) $now->format('n')] ?? '') . ' ' . $now->format('Y'),
            'last_closing'   => $last && $last->created_at ? $last->created_at->format('Y-m-d') : '—',
        ];
    }

    // ---------------------------------------------------------------- SECTION 5
    private function activityFeed(): array
    {
        $items = [];

        $fees = GroupStudentsFees::with('student')->whereNull('deleted_at')
            ->orderByDesc('created_at')->limit(18)->get();
        foreach ($fees as $f) {
            $name   = optional($f->student)->name ?? ('طالب #' . $f->student_id);
            $amount = abs((float) $f->transaction_amount);
            $pending = $f->audit_status !== 'verified';
            [$title, $icon, $color] = match ($f->transaction_type) {
                'refund'     => ['استرداد مبلغ', 'ki-arrow-down-left', 'danger'],
                'credit'     => ['رصيد دائن للطالب', 'ki-bank', 'info'],
                'adjustment' => ['تسوية مالية', 'ki-pencil', 'warning'],
                default      => $pending ? ['طلب دفع جديد', 'ki-file-added', 'warning'] : ['دفعة طالب مؤكدة', 'ki-dollar', 'success'],
            };
            $items[] = [
                'title' => $title, 'desc' => $name, 'icon' => $icon, 'color' => $color,
                'amount' => round($amount, 2), 'time' => $f->created_at?->format('Y-m-d H:i'),
                'ts' => $f->created_at?->timestamp ?? 0,
            ];
        }

        $sal = TeacherSalaryForm::with('teacher')->whereNull('deleted_at')
            ->whereNotNull('received_at')->orderByDesc('received_at')->limit(8)->get();
        foreach ($sal as $s) {
            $items[] = [
                'title' => 'صرف راتب معلّم', 'desc' => optional($s->teacher)->name ?? ('معلّم #' . $s->teacher_id),
                'icon' => 'ki-wallet', 'color' => 'primary', 'amount' => round((float) $s->net_amount, 2),
                'time' => $s->received_at?->format('Y-m-d H:i'), 'ts' => $s->received_at?->timestamp ?? 0,
            ];
        }

        foreach (SalaryCloseLog::orderByDesc('created_at')->limit(5)->get() as $c) {
            $items[] = [
                'title' => 'إغلاق شهر مالي', 'desc' => ($this->monthsAr[$c->month] ?? $c->month) . ' ' . $c->year,
                'icon' => 'ki-lock-2', 'color' => 'dark', 'amount' => round((float) $c->total_amount, 2),
                'time' => $c->created_at?->format('Y-m-d H:i'), 'ts' => $c->created_at?->timestamp ?? 0,
            ];
        }

        foreach (Expense::with('createdBy')->whereNull('deleted_at')->orderByDesc('expense_date')->orderByDesc('id')->limit(10)->get() as $e) {
            $items[] = [
                'title' => 'مصروف: ' . $e->statement, 'desc' => optional($e->createdBy)->name ?? 'مصروفات',
                'icon' => 'ki-minus-circle', 'color' => 'danger', 'amount' => round((float) $e->amount, 2),
                'time' => $e->created_at?->format('Y-m-d H:i'), 'ts' => $e->created_at?->timestamp ?? 0,
            ];
        }

        usort($items, fn ($a, $b) => $b['ts'] <=> $a['ts']);
        return array_slice($items, 0, 12);
    }

    // ---------------------------------------------------------------- SECTION 6
    private function tables(): array
    {
        $dues = $this->dues();
        $duesRows = array_map(function ($r) {
            $r['status'] = $r['paid'] > 0 ? 'مدفوع جزئياً' : 'غير مدفوع';
            return $r;
        }, $dues['rows']);

        // Credit-balance students (verified credit, positive net)
        $students = Students::pluck('name', 'id');
        $creditRows = GroupStudentsFees::confirmed()->whereNull('deleted_at')
            ->where('transaction_type', 'credit')
            ->selectRaw('student_id, SUM(transaction_amount) bal, MAX(created_at) last_at')
            ->groupBy('student_id')->havingRaw('SUM(transaction_amount) > 0')
            ->orderByDesc('bal')->limit(15)->get()
            ->map(fn ($r) => [
                'student' => $students[$r->student_id] ?? ('طالب #' . $r->student_id),
                'credit'  => round((float) $r->bal, 2),
                'last'    => $r->last_at ? Carbon::parse($r->last_at)->format('Y-m-d') : '—',
                'status'  => 'رصيد متاح',
            ])->all();

        // Teachers awaiting payment
        $awaiting = TeacherSalaryForm::with('teacher')->whereNull('deleted_at')
            ->where('is_received', 0)->orderByDesc('net_amount')->limit(15)->get()
            ->map(fn ($s) => [
                'teacher' => optional($s->teacher)->name ?? ('معلّم #' . $s->teacher_id),
                'salary'  => round((float) $s->net_amount, 2),
                'month'   => ($this->monthsAr[$s->month] ?? $s->month) . ' ' . $s->year,
                'status'  => $s->status === 'closed' ? 'بانتظار الصرف' : 'قيد الإعداد',
            ])->all();

        return [
            'dues'    => $duesRows,
            'credit'  => $creditRows,
            'payroll' => $awaiting,
        ];
    }

    // ---------------------------------------------------------------- helpers
    /** Per-ledger outstanding (group + pre-group), mirroring FinancialService semantics. */
    private function dues(): array
    {
        if ($this->duesCache !== null) {
            return $this->duesCache;
        }

        $students = Students::pluck('name', 'id');
        $groups   = Groups::with('program')->get()->keyBy('id');
        $rows     = [];
        $total    = 0.0;

        $pairs = GroupStudents::whereNull('deleted_at')->get(['student_id', 'group_id'])
            ->unique(fn ($r) => $r->student_id . '-' . $r->group_id);
        foreach ($pairs as $p) {
            $l = $this->financial->getStudentLedger($p->student_id, $p->group_id);
            if (!$l) continue;
            $rem = (float) $l['remaining_balance'];
            if ($rem <= 0) continue;
            $g = $groups->get($p->group_id);
            $rows[] = [
                'student'   => $students[$p->student_id] ?? ('طالب #' . $p->student_id),
                'program'   => $g && $g->program ? $g->program->title : '—',
                'invoice'   => round((float) $l['total_fee'], 2),
                'paid'      => round((float) $l['total_paid'], 2),
                'remaining' => round($rem, 2),
            ];
            $total += $rem;
        }

        $pre = GroupStudentsFees::whereNull('group_id')->whereNull('deleted_at')->distinct()->pluck('student_id');
        foreach ($pre as $sid) {
            $l = $this->financial->getStudentLedger($sid, null);
            if (!$l) continue;
            $rem = (float) $l['remaining_balance'];
            if ($rem <= 0) continue;
            $rows[] = [
                'student'   => $students[$sid] ?? ('طالب #' . $sid),
                'program'   => 'رسوم خارج المجموعة',
                'invoice'   => round((float) $l['total_fee'], 2),
                'paid'      => round((float) $l['total_paid'], 2),
                'remaining' => round($rem, 2),
            ];
            $total += $rem;
        }

        usort($rows, fn ($a, $b) => $b['remaining'] <=> $a['remaining']);

        return $this->duesCache = [
            'total' => round($total, 2),
            'count' => count($rows),
            'rows'  => array_slice($rows, 0, 15),
        ];
    }
}
