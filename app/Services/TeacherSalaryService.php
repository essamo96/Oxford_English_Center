<?php

namespace App\Services;

use App\Models\SalaryCloseLog;
use App\Models\Teachers;
use App\Models\TeacherSalaryDetail;
use App\Models\TeacherSalaryForm;
use Illuminate\Support\Facades\DB;

class TeacherSalaryService
{
    /**
     * One delivered lecture = one distinct (group_id, date) in absent_teacher.
     * Returns a collection of objects { group_id, d (date) } for the teacher in the period.
     */
    public function lectureRows(int $teacherId, int $year, int $month)
    {
        return DB::table('absent_teacher')
            ->where('teacher_id', $teacherId)
            ->whereNull('deleted_at')
            ->whereYear('days', $year)
            ->whereMonth('days', $month)
            ->select('group_id', DB::raw('DATE(days) as d'))
            ->groupBy('group_id', DB::raw('DATE(days)'))
            ->orderBy('d')
            ->get();
    }

    /** Teacher ids that delivered at least one lecture in the period. */
    public function teacherIdsWithLectures(int $year, int $month): array
    {
        return DB::table('absent_teacher')
            ->whereNull('deleted_at')
            ->whereYear('days', $year)
            ->whereMonth('days', $month)
            ->distinct()
            ->pluck('teacher_id')
            ->map(fn ($x) => (int) $x)
            ->all();
    }

    public function isPeriodClosed(int $year, int $month): bool
    {
        return SalaryCloseLog::where('year', $year)->where('month', $month)->exists();
    }

    /** Human-readable, stored form (invoice) number, e.g. SAL-202605-00042. */
    public function formNumber(int $year, int $month, int $formId): string
    {
        return 'SAL-' . $year . str_pad((string) $month, 2, '0', STR_PAD_LEFT) . '-' . str_pad((string) $formId, 5, '0', STR_PAD_LEFT);
    }

    /** Teacher ids that delivered a lecture between two dates (inclusive). */
    public function teacherIdsActiveBetween(string $from, string $to): array
    {
        return DB::table('absent_teacher')
            ->whereNull('deleted_at')
            ->whereBetween(DB::raw('DATE(days)'), [$from, $to])
            ->distinct()
            ->pluck('teacher_id')
            ->map(fn ($x) => (int) $x)
            ->all();
    }

    /**
     * Live preview for a period: computed straight from attendance, merged with any
     * saved form (so bonus/deduction and closed state are reflected).
     */
    public function preview(int $year, int $month): array
    {
        $teacherIds = $this->teacherIdsWithLectures($year, $month);
        $teachers   = Teachers::whereIn('id', $teacherIds)->get()->keyBy('id');
        $forms      = TeacherSalaryForm::where('year', $year)->where('month', $month)
                        ->get()->keyBy('teacher_id');

        // Group-name lookup for every group that saw a lecture this period
        $groupNames = \App\Models\Groups::whereIn('id', function ($q) use ($year, $month) {
                $q->select('group_id')->from('absent_teacher')
                  ->whereNull('deleted_at')->whereYear('days', $year)->whereMonth('days', $month);
            })->pluck('name', 'id');

        $rows = [];
        foreach ($teacherIds as $tid) {
            $teacher    = $teachers->get($tid);
            $lectureRows = $this->lectureRows($tid, $year, $month);
            $lectures   = $lectureRows->count();
            $rate       = $teacher ? (float) $teacher->lecture_rate : 0.0;
            $form       = $forms->get($tid);

            // Per-group breakdown for this teacher (group_id => lectures count)
            $groups = [];
            foreach ($lectureRows->groupBy('group_id') as $gid => $rowsOfGroup) {
                $groups[] = [
                    'id'       => (int) $gid,
                    'name'     => $groupNames[$gid] ?? ('#' . $gid),
                    'lectures' => $rowsOfGroup->count(),
                ];
            }

            $bonus     = $form ? (float) $form->bonus : 0.0;
            $deduction = $form ? (float) $form->deduction : 0.0;
            // A closed form is frozen — use its stored figures, not the live recompute.
            if ($form && $form->status === 'closed') {
                $lectures = (int) $form->lectures_count;
                $rate     = (float) $form->lecture_rate;
            }
            $gross = round($lectures * $rate, 2);

            $rows[] = [
                'teacher_id'     => $tid,
                'teacher_name'   => $teacher ? $teacher->name : ('مدرس #' . $tid),
                'lectures_count' => $lectures,
                'lecture_rate'   => $rate,
                'gross_amount'   => $gross,
                'bonus'          => $bonus,
                'deduction'      => $deduction,
                'net_amount'     => round($gross + $bonus - $deduction, 2),
                'status'         => $form ? $form->status : 'draft',
                'form_id'        => $form?->id,
                'form_no'        => $form ? $this->formNumber($year, $month, $form->id) : null,
                'is_received'    => $form ? (bool) $form->is_received : false,
                'groups'         => $groups,                       // [{id,name,lectures}]
                'group_ids'      => array_column($groups, 'id'),   // for filtering
            ];
        }
        return $rows;
    }

    /**
     * Create/refresh OPEN salary forms (+ detail lines) for the period from current
     * attendance. Closed forms and closed periods are left untouched.
     */
    public function generateOrRefresh(int $year, int $month): int
    {
        if ($this->isPeriodClosed($year, $month)) {
            return 0;
        }

        $teacherIds = $this->teacherIdsWithLectures($year, $month);
        $teachers   = Teachers::whereIn('id', $teacherIds)->get()->keyBy('id');
        $count = 0;

        DB::transaction(function () use ($teacherIds, $teachers, $year, $month, &$count) {
            foreach ($teacherIds as $tid) {
                $teacher = $teachers->get($tid);
                $rate    = $teacher ? (float) $teacher->lecture_rate : 0.0;

                $form = TeacherSalaryForm::firstOrNew([
                    'teacher_id' => $tid, 'year' => $year, 'month' => $month,
                ]);
                if ($form->status === 'closed') {
                    continue; // never touch a closed form
                }

                $lectureRows = $this->lectureRows($tid, $year, $month);
                $lectures    = $lectureRows->count();

                $form->lectures_count = $lectures;
                $form->lecture_rate   = $rate;
                $form->gross_amount   = round($lectures * $rate, 2);
                $form->status         = 'open';
                $form->recalcNet();
                $form->save();

                // Rebuild detail lines (one per group/date lecture)
                TeacherSalaryDetail::where('salary_form_id', $form->id)->delete();
                foreach ($lectureRows as $lr) {
                    TeacherSalaryDetail::create([
                        'salary_form_id' => $form->id,
                        'group_id'       => $lr->group_id,
                        'lecture_date'   => $lr->d,
                        'lectures'       => 1,
                        'rate'           => $rate,
                        'amount'         => $rate,
                    ]);
                }
                $count++;
            }
        });

        return $count;
    }

    /**
     * Close the month: ensure forms exist & are frozen, then write the close log.
     * Returns the created SalaryCloseLog, or null if already closed.
     */
    public function closeMonth(int $year, int $month, ?int $adminId, ?string $notes = null): ?SalaryCloseLog
    {
        if ($this->isPeriodClosed($year, $month)) {
            return null;
        }

        // Make sure every teacher with lectures has an up-to-date open form first
        $this->generateOrRefresh($year, $month);

        $log = null;
        DB::transaction(function () use ($year, $month, $adminId, $notes, &$log) {
            $forms = TeacherSalaryForm::where('year', $year)->where('month', $month)->get();

            $totalLectures = 0;
            $totalAmount   = 0.0;
            foreach ($forms as $form) {
                $form->status    = 'closed';
                $form->closed_by = $adminId;
                $form->closed_at = now();
                $form->recalcNet();
                $form->save();

                $totalLectures += (int) $form->lectures_count;
                $totalAmount   += (float) $form->net_amount;
            }

            $log = SalaryCloseLog::create([
                'year'           => $year,
                'month'          => $month,
                'teachers_count' => $forms->count(),
                'total_lectures' => $totalLectures,
                'total_amount'   => round($totalAmount, 2),
                'closed_by'      => $adminId,
                'notes'          => $notes,
            ]);
        });

        return $log;
    }
}
