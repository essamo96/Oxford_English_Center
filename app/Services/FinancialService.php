<?php

namespace App\Services;

use App\Models\GroupStudents;
use App\Models\GroupStudentsFees;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    /**
     * Get the student's financial ledger.
     * Handles BOTH:
     *   - group-based ledger (course enrollment in a group)
     *   - non-group ledger (placement test, or course paid before group assignment)
     *
     * Pass $groupId=null to read the non-group ledger.
     */
    public function getStudentLedger($studentId, $groupId = null)
    {
        if (!$studentId) {
            return null;
        }

        // ---------- Group-based ledger ----------
        if ($groupId) {
            $groupStudent = GroupStudents::where('student_id', $studentId)
                ->where('group_id', $groupId)
                ->first();

            $transactions = GroupStudentsFees::with('paymentMethod')
                ->where('student_id', $studentId)
                ->where('group_id', $groupId)
                ->orderBy('created_at', 'asc')
                ->get();

            $totalFee = $groupStudent ? (float) $groupStudent->student_fee_total : 0.0;
            // Fallback: if group_students row missing, use largest total_due on the rows
            if ($totalFee <= 0) {
                $totalFee = (float) ($transactions->max('total_due_amount') ?: 0);
            }
            // Only rows explicitly typed as 'payment' (or untyped legacy rows = NULL) count as payments.
            // 'credit' and 'refund' are handled separately.
            $confirmedPaid = (float) $transactions->where('audit_status', 'verified')
                ->filter(fn ($t) => is_null($t->transaction_type) || $t->transaction_type === 'payment')
                ->sum('transaction_amount');
            $refunded      = (float) $transactions->where('audit_status', 'verified')
                                                  ->where('transaction_type', 'refund')
                                                  ->sum(fn ($t) => abs((float) $t->transaction_amount));
            $credit        = (float) $transactions->where('audit_status', 'verified')
                                                  ->where('transaction_type', 'credit')
                                                  ->sum('transaction_amount');
            $netPaid          = $confirmedPaid - $refunded;
            $remainingBalance = $totalFee <= 0 ? 0.0 : max(0, $totalFee - $netPaid); // no fee ⇒ never "owed" (ignore standalone refunds/credits)

            return [
                'group_student'     => $groupStudent,
                'transactions'      => $transactions,
                'total_fee'         => $totalFee,
                'total_paid'        => $netPaid,
                'credit_balance'    => $credit,
                'remaining_balance' => $remainingBalance,
                'context'           => 'group',
            ];
        }

        // ---------- Non-group ledger (placement test / pre-assignment) ----------
        $transactions = GroupStudentsFees::with('paymentMethod')
            ->where('student_id', $studentId)
            ->whereNull('group_id')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($transactions->isEmpty()) {
            return null;
        }

        // The total comes from the first (registration) row. Fallback to max.
        $first    = $transactions->first();
        $totalFee = (float) ($first->total_due_amount ?: $transactions->max('total_due_amount') ?: 0);

        $confirmedPaid = (float) $transactions->where('audit_status', 'verified')
            ->filter(fn ($t) => is_null($t->transaction_type) || $t->transaction_type === 'payment')
            ->sum('transaction_amount');
        $refunded      = (float) $transactions->where('audit_status', 'verified')
                                              ->where('transaction_type', 'refund')
                                              ->sum(fn ($t) => abs((float) $t->transaction_amount));
        $credit        = (float) $transactions->where('audit_status', 'verified')
                                              ->where('transaction_type', 'credit')
                                              ->sum('transaction_amount');
        $netPaid       = $confirmedPaid - $refunded;
        $remainingBalance = $totalFee <= 0 ? 0.0 : max(0, $totalFee - $netPaid); // no fee ⇒ never "owed" (ignore standalone refunds/credits)

        return [
            'group_student'     => null,
            'transactions'      => $transactions,
            'total_fee'         => $totalFee,
            'total_paid'        => $netPaid,
            'credit_balance'    => $credit,
            'remaining_balance' => $remainingBalance,
            'context'           => 'placement_or_pre_group',
        ];
    }

    /**
     * Aggregate ALL of a student's invoices across every ledger (groups + non-group).
     * Used by the per-student "all invoices" modal.
     */
    public function getStudentAllInvoices($studentId)
    {
        if (!$studentId) return null;

        $rows = GroupStudentsFees::with(['paymentMethod', 'group.program'])
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Per-context summaries (group_id NULL counts as one bucket)
        $buckets = $rows->groupBy(fn($r) => $r->group_id ?: 'pre_group');
        $summary = [];
        foreach ($buckets as $key => $bucketRows) {
            $groupId   = $key === 'pre_group' ? null : (int) $key;
            $first     = $bucketRows->first();
            $totalFee  = (float) ($first->total_due_amount ?: $bucketRows->max('total_due_amount') ?: 0);
            if ($groupId) {
                $gs = GroupStudents::where('student_id', $studentId)->where('group_id', $groupId)->first();
                if ($gs && $gs->student_fee_total > 0) $totalFee = (float) $gs->student_fee_total;
            }
            // Only real payments (or untyped legacy rows) count toward the fee. Credit and
            // refund rows are tracked separately so over-payments are not mistaken for fees paid.
            $paid = (float) $bucketRows->where('audit_status', 'verified')
                                       ->filter(fn ($r) => is_null($r->transaction_type) || $r->transaction_type === 'payment')
                                       ->sum('transaction_amount');
            $refunded = (float) $bucketRows->where('audit_status', 'verified')
                                           ->where('transaction_type', 'refund')
                                           ->sum(fn ($r) => abs((float) $r->transaction_amount));
            $credit = (float) $bucketRows->where('audit_status', 'verified')
                                         ->where('transaction_type', 'credit')
                                         ->sum('transaction_amount');
            $net = $paid - $refunded;
            $summary[] = [
                'group_id'   => $groupId,
                'label'      => $groupId
                                ? (($first->group && $first->group->program ? $first->group->program->title . ' — ' : '') . ($first->group ? $first->group->name : 'Group'))
                                : ($first->student_paid_type ?: 'رسوم خارج المجموعة'),
                'total_fee'  => $totalFee,
                'paid'       => $net,
                'credit'     => $credit,
                'remaining'  => $totalFee <= 0 ? 0.0 : max(0, $totalFee - $net),
                'rows'       => $bucketRows,
            ];
        }

        return [
            'rows'      => $rows,
            'summary'   => $summary,
            'grand_total_due'    => array_sum(array_column($summary, 'total_fee')),
            'grand_total_paid'   => array_sum(array_column($summary, 'paid')),
            'grand_total_left'   => array_sum(array_column($summary, 'remaining')),
            'grand_total_credit' => array_sum(array_column($summary, 'credit')),
        ];
    }

    /**
     * Record a new financial transaction (payment, refund, adjustment).
     */
    public function recordTransaction($data)
    {
        return GroupStudentsFees::create([
            'student_id' => $data['student_id'],
            'group_id' => $data['group_id'],
            'program_id' => $data['program_id'] ?? null,
            'payment_method_id' => $data['payment_method_id'] ?? null,
            'transaction_type' => $data['type'] ?? 'payment',
            'transaction_amount' => $data['amount'],
            'admin_verified_amount' => $data['verified_amount'] ?? 0,
            'audit_status' => $data['audit_status'] ?? 'pending',
            'verified_by' => $data['verified_by'] ?? null,
            'notes' => $data['notes'] ?? null,
            'student_paid_type' => $data['paid_type'] ?? 'partial',
            'payment_receipt' => $data['receipt'] ?? null,
        ]);
    }

    /**
     * Calculate dynamic stats for dashboard.
     */
    public function getGlobalStats()
    {
        // 1. Total Collected (Verified payments - verified refunds)
        $totalCollected = GroupStudentsFees::confirmed()
            ->where('transaction_type', 'payment')
            ->sum('transaction_amount')
            - GroupStudentsFees::confirmed()
            ->where('transaction_type', 'refund')
            ->sum(DB::raw('ABS(transaction_amount)'));

        // 2. Pending Amount (submitted but not yet verified) — use the row's remaining when set
        // (enrollment pending orders carry student_fee_paid=0 but a real remaining_amount).
        $pendingAmount = (float) GroupStudentsFees::where('audit_status', 'pending')
            ->whereNull('deleted_at')
            ->sum(DB::raw('GREATEST(COALESCE(remaining_amount,0), COALESCE(student_fee_paid,0))'));

        // 3. Total Remaining (what students still owe) — summed PER LEDGER so an over-payer
        // never cancels out another student's outstanding (the old global net hid real dues).
        $totalRemaining = 0.0;

        // Group ledgers (one per distinct student+group membership)
        $pairs = GroupStudents::whereNull('deleted_at')
            ->get(['student_id', 'group_id'])
            ->unique(fn ($r) => $r->student_id . '-' . $r->group_id);
        foreach ($pairs as $p) {
            $ledger = $this->getStudentLedger($p->student_id, $p->group_id);
            if ($ledger) $totalRemaining += max(0, (float) $ledger['remaining_balance']);
        }

        // Pre-group ledgers (placement tests / fees not tied to a group)
        $preStudents = GroupStudentsFees::whereNull('group_id')
            ->whereNull('deleted_at')
            ->distinct()->pluck('student_id');
        foreach ($preStudents as $sid) {
            $ledger = $this->getStudentLedger($sid, null);
            if ($ledger) $totalRemaining += max(0, (float) $ledger['remaining_balance']);
        }

        return [
            'total_collected' => $totalCollected,
            'total_remaining' => round(max(0, $totalRemaining), 2),
            'pending_amount'  => round((float) $pendingAmount, 2),
        ];
    }
}
