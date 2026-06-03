<?php

namespace App\Http\Controllers\Admin;

use App\Models\Groups;
use App\Models\SalaryCloseLog;
use App\Models\Teachers;
use App\Models\TeacherSalaryForm;
use App\Services\TeacherSalaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherSalaryController extends AdminController
{
    protected $service;

    public function __construct(TeacherSalaryService $service)
    {
        parent::__construct();
        parent::$data['active_menu'] = 'teacher_salaries';
        $this->service = $service;
    }

    private function adminId(): ?int
    {
        return optional(Auth::guard('admin')->user())->id;
    }

    public function getIndex(Request $request)
    {
        $year    = (int) $request->get('year', now()->year);
        $month   = (int) $request->get('month', now()->month);
        $search  = trim((string) $request->get('search', ''));
        $groupId = (int) $request->get('group_id', 0);
        $formNo  = trim((string) $request->get('form_no', ''));
        $from    = trim((string) $request->get('from', ''));
        $to      = trim((string) $request->get('to', ''));

        $rows = $this->service->preview($year, $month);

        // Distinct groups present this period → for the "sort/filter by group" dropdown
        $groupsForFilter = [];
        foreach ($rows as $r) {
            foreach ($r['groups'] as $g) { $groupsForFilter[$g['id']] = $g['name']; }
        }
        asort($groupsForFilter);

        if ($search !== '') {
            $rows = array_values(array_filter($rows, fn ($r) => mb_stripos($r['teacher_name'], $search) !== false));
        }
        if ($groupId) {
            // Keep only teachers who delivered lectures in the chosen group
            $rows = array_values(array_filter($rows, fn ($r) => in_array($groupId, $r['group_ids'], true)));
        }
        if ($formNo !== '') {
            // Match the stored form number (or any part of it, e.g. the trailing id)
            $needle = mb_strtolower($formNo);
            $rows = array_values(array_filter($rows, fn ($r) => $r['form_no'] && mb_stripos($r['form_no'], $needle) !== false));
        }
        if ($from !== '' || $to !== '') {
            // Narrow to teachers active within the date window (inclusive)
            $f = $from !== '' ? $from : '1970-01-01';
            $t = $to   !== '' ? $to   : '2999-12-31';
            $activeIds = $this->service->teacherIdsActiveBetween($f, $t);
            $rows = array_values(array_filter($rows, fn ($r) => in_array((int) $r['teacher_id'], $activeIds, true)));
        }

        // Sort by the teacher's first group name so same-group teachers cluster together
        usort($rows, fn ($a, $b) => strcmp($a['groups'][0]['name'] ?? '', $b['groups'][0]['name'] ?? ''));

        parent::$data['year']             = $year;
        parent::$data['month']            = $month;
        parent::$data['search']           = $search;
        parent::$data['group_id']         = $groupId;
        parent::$data['form_no']          = $formNo;
        parent::$data['from']             = $from;
        parent::$data['to']               = $to;
        parent::$data['groups_for_filter'] = $groupsForFilter;
        parent::$data['rows']             = $rows;
        parent::$data['is_closed']        = $this->service->isPeriodClosed($year, $month);
        parent::$data['logs']      = SalaryCloseLog::with('closedBy')->orderByDesc('year')->orderByDesc('month')->limit(24)->get();

        return view('admin.salaries.index', parent::$data);
    }

    /** Create/refresh draft (open) forms for the period from current attendance. */
    public function postGenerate(Request $request)
    {
        $request->validate(['year' => 'required|integer', 'month' => 'required|integer|min:1|max:12']);

        if ($this->service->isPeriodClosed($request->year, $request->month)) {
            return back()->with('error', 'هذا الشهر مغلق بالفعل — لا يمكن إعادة احتسابه.');
        }
        $n = $this->service->generateOrRefresh((int) $request->year, (int) $request->month);
        return back()->with('success', "تم احتساب/تحديث استمارات الرواتب ({$n} مدرس).");
    }

    /** Edit bonus / deduction / notes on an OPEN form (AJAX). */
    public function postUpdateForm(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'year'       => 'required|integer',
            'month'      => 'required|integer|min:1|max:12',
            'bonus'      => 'nullable|numeric|min:0',
            'deduction'  => 'nullable|numeric|min:0',
            'notes'      => 'nullable|string|max:1000',
        ]);

        if ($this->service->isPeriodClosed($request->year, $request->month)) {
            return response()->json(['status' => 'error', 'message' => 'الشهر مغلق ولا يمكن التعديل.'], 422);
        }

        // Ensure a form exists (build it from attendance if still a draft)
        $form = TeacherSalaryForm::where('teacher_id', $request->teacher_id)
            ->where('year', $request->year)->where('month', $request->month)->first();
        if (!$form) {
            $this->service->generateOrRefresh((int) $request->year, (int) $request->month);
            $form = TeacherSalaryForm::where('teacher_id', $request->teacher_id)
                ->where('year', $request->year)->where('month', $request->month)->first();
        }
        if (!$form) {
            return response()->json(['status' => 'error', 'message' => 'لا توجد محاضرات لهذا المدرس في هذا الشهر.'], 422);
        }
        if ($form->isClosed()) {
            return response()->json(['status' => 'error', 'message' => 'الاستمارة مغلقة.'], 422);
        }

        $form->bonus     = (float) $request->bonus;
        $form->deduction = (float) $request->deduction;
        $form->notes     = $request->notes;
        $form->recalcNet();
        $form->save();

        return response()->json([
            'status'     => 'success',
            'message'    => 'تم تحديث الاستمارة.',
            'net_amount' => number_format($form->net_amount, 2),
        ]);
    }

    /** Close the month: freeze all forms + write a close-log entry. */
    public function postClose(Request $request)
    {
        $request->validate([
            'year'  => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'notes' => 'nullable|string|max:1000',
        ]);

        $log = $this->service->closeMonth((int) $request->year, (int) $request->month, $this->adminId(), $request->notes);
        if (!$log) {
            return back()->with('error', 'هذا الشهر مغلق مسبقاً.');
        }
        return back()->with('success', "تم إغلاق رواتب الشهر بنجاح — {$log->teachers_count} مدرس، إجمالي " . number_format($log->total_amount, 2) . ' ILS.');
    }

    /** Salary-details modal for one teacher in a period. */
    public function getDetails(Request $request, $teacherId)
    {
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $teacher = Teachers::find($teacherId);
        $form    = TeacherSalaryForm::with('details')
            ->where('teacher_id', $teacherId)->where('year', $year)->where('month', $month)->first();

        // Build line rows: prefer the saved details, otherwise compute live from attendance.
        if ($form && $form->details->count()) {
            $lines = $form->details->map(fn ($d) => [
                'group_id' => $d->group_id, 'date' => $d->lecture_date, 'rate' => (float) $d->rate, 'amount' => (float) $d->amount,
            ]);
        } else {
            $rate  = $teacher ? (float) $teacher->lecture_rate : 0.0;
            $lines = $this->service->lectureRows((int) $teacherId, $year, $month)->map(fn ($r) => [
                'group_id' => $r->group_id, 'date' => $r->d, 'rate' => $rate, 'amount' => $rate,
            ]);
        }

        $groupNames = Groups::whereIn('id', $lines->pluck('group_id')->filter()->unique())->pluck('name', 'id');

        return view('admin.salaries.parts.details_modal', [
            'teacher'    => $teacher,
            'form'       => $form,
            'lines'      => $lines,
            'groupNames' => $groupNames,
            'year'       => $year,
            'month'      => $month,
        ])->render();
    }

    /** Printable salary-form (estmara) preview for one teacher in a period. */
    public function getPreview(Request $request, $teacherId)
    {
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $teacher = Teachers::find($teacherId);
        $form    = TeacherSalaryForm::with(['details.group', 'closedBy', 'receivedBy'])
            ->where('teacher_id', $teacherId)->where('year', $year)->where('month', $month)->first();

        // Build figures: from the saved form when present, else compute live from attendance.
        if ($form) {
            $lectures = (int) $form->lectures_count;
            $rate     = (float) $form->lecture_rate;
            $bonus    = (float) $form->bonus;
            $deduction = (float) $form->deduction;
            $lines = $form->details->map(fn ($d) => [
                'date' => $d->lecture_date, 'group' => optional($d->group)->name, 'amount' => (float) $d->amount,
            ]);
        } else {
            $rate     = $teacher ? (float) $teacher->lecture_rate : 0.0;
            $rows     = $this->service->lectureRows((int) $teacherId, $year, $month);
            $lectures = $rows->count();
            $bonus = 0.0; $deduction = 0.0;
            $groupNames = Groups::whereIn('id', $rows->pluck('group_id')->filter()->unique())->pluck('name', 'id');
            $lines = $rows->map(fn ($r) => [
                'date' => $r->d, 'group' => $groupNames[$r->group_id] ?? ('#' . $r->group_id), 'amount' => $rate,
            ]);
        }
        $gross = round($lectures * $rate, 2);
        $net   = round($gross + $bonus - $deduction, 2);

        return view('admin.salaries.parts.preview_form', [
            'teacher'   => $teacher,
            'form'      => $form,
            'lines'     => $lines,
            'year'      => $year,
            'month'     => $month,
            'lectures'  => $lectures,
            'rate'      => $rate,
            'gross'     => $gross,
            'bonus'     => $bonus,
            'deduction' => $deduction,
            'net'       => $net,
            'settings'  => parent::$data['mysettings'] ?? null,
        ])->render();
    }

    /** Record whether the teacher actually received the salary (and who logged it). */
    public function postMarkReceived(Request $request)
    {
        $request->validate([
            'teacher_id'  => 'required|exists:teachers,id',
            'year'        => 'required|integer',
            'month'       => 'required|integer|min:1|max:12',
            'is_received' => 'required|boolean',
        ]);

        // Ensure a form exists (build it from attendance if still a draft)
        $form = TeacherSalaryForm::where('teacher_id', $request->teacher_id)
            ->where('year', $request->year)->where('month', $request->month)->first();
        if (!$form) {
            $this->service->generateOrRefresh((int) $request->year, (int) $request->month);
            $form = TeacherSalaryForm::where('teacher_id', $request->teacher_id)
                ->where('year', $request->year)->where('month', $request->month)->first();
        }
        if (!$form) {
            return response()->json(['status' => 'error', 'message' => 'لا توجد استمارة راتب لهذا المدرس في هذا الشهر.'], 422);
        }

        $received = $request->boolean('is_received');
        $form->is_received = $received;
        $form->received_at = $received ? now() : null;
        $form->received_by = $received ? $this->adminId() : null;
        $form->save();

        return response()->json([
            'status'      => 'success',
            'message'     => $received ? 'تم تسجيل استلام المدرس للراتب.' : 'تم إلغاء حالة الاستلام.',
            'is_received' => $received,
        ]);
    }
}
