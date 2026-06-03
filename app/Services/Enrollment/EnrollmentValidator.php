<?php

namespace App\Services\Enrollment;

use App\Models\GroupStudents;
use App\Models\GroupStudentsFees;
use App\Models\Groups;
use App\Models\Times;
use App\Services\FinancialService;
use App\Services\ScheduleParser;

/**
 * Pure business-rule validation for branching / enrollment.
 * Contains NO database writes — it only reads state and answers questions, so
 * it can be unit-tested and reused by any caller (bulk-assign, promotion, API).
 */
class EnrollmentValidator
{
    public function __construct(
        private FinancialService $finance,
        private ScheduleParser $parser
    ) {}

    /** Is the student already an active member of this group? */
    public function isEnrolledIn(int $studentId, int $groupId): bool
    {
        return GroupStudents::where('student_id', $studentId)
            ->where('group_id', $groupId)
            ->whereNull('deleted_at')
            ->exists();
    }

    /** Total outstanding balance across ALL of the student's ledgers (groups + pre-group). */
    public function outstandingBalance(int $studentId): float
    {
        $total = 0.0;

        $groupIds = GroupStudents::where('student_id', $studentId)
            ->whereNull('deleted_at')
            ->pluck('group_id')->filter()->unique();

        foreach ($groupIds as $gid) {
            $ledger = $this->finance->getStudentLedger($studentId, $gid);
            if ($ledger) $total += (float) $ledger['remaining_balance'];
        }

        $pre = $this->finance->getStudentLedger($studentId, null);
        if ($pre) $total += (float) $pre['remaining_balance'];

        return round($total, 2);
    }

    /** Outstanding balance for a single group ledger. */
    public function outstandingForGroup(int $studentId, int $groupId): float
    {
        $ledger = $this->finance->getStudentLedger($studentId, $groupId);
        return $ledger ? round((float) $ledger['remaining_balance'], 2) : 0.0;
    }

    /** Net credit balance the centre owes the student (verified credit rows). */
    public function creditBalance(int $studentId): float
    {
        return round((float) GroupStudentsFees::where('student_id', $studentId)
            ->where('audit_status', 'verified')
            ->where('transaction_type', 'credit')
            ->sum('transaction_amount'), 2);
    }

    /**
     * Programs the student has ALREADY PAID for — i.e. has a VERIFIED course-enrollment
     * payment carrying that program_id. Seating into a group of such a program does not
     * incur a new charge; a different program is treated as a fresh enrollment.
     *
     * @return int[]
     */
    public function paidProgramIds(int $studentId): array
    {
        return GroupStudentsFees::where('student_id', $studentId)
            ->where('audit_status', 'verified')
            ->whereNotNull('program_id')
            ->where('student_paid_type', 'like', '%Course%')
            ->where('student_paid_type', 'not like', '%Placement%')
            ->distinct()
            ->pluck('program_id')
            ->map(fn ($p) => (int) $p)
            ->all();
    }

    /** Parse a group's schedule → ['days'=>int[], 'start'=>'H:i', 'end'=>'H:i'] or null. */
    private function scheduleMeta(?Groups $group): ?array
    {
        if (!$group || !$group->date_id) return null;
        $time = Times::find($group->date_id);
        if (!$time) return null;

        $days  = $this->parser->dayNumbers($time->days);
        $range = $this->parser->timeRange($time->times);
        if (empty($days) || !$range) return null; // undeterminable → cannot assert a conflict

        return ['days' => $days, 'start' => $range['start'], 'end' => $range['end']];
    }

    /** Do two groups overlap on a shared weekday AND overlapping time window? */
    public function schedulesConflict(Groups $a, Groups $b): bool
    {
        $ma = $this->scheduleMeta($a);
        $mb = $this->scheduleMeta($b);
        if (!$ma || !$mb) return false;                 // fail-open: never block on unparseable schedules
        if (!array_intersect($ma['days'], $mb['days'])) return false;
        // 'HH:MM' zero-padded → lexicographic compare == chronological compare
        return ($ma['start'] < $mb['end']) && ($mb['start'] < $ma['end']);
    }

    /** Returns the FIRST current group whose schedule clashes with $target, or null. */
    public function findTimeConflict(int $studentId, Groups $target): ?Groups
    {
        $current = Groups::whereIn('id', function ($q) use ($studentId) {
                $q->select('group_id')->from('group_students')
                  ->where('student_id', $studentId)->whereNull('deleted_at');
            })
            ->where('id', '!=', $target->id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->get();

        foreach ($current as $group) {
            if ($this->schedulesConflict($group, $target)) {
                return $group;
            }
        }
        return null;
    }

    /**
     * Hard blocking rules for enrollment → array of error messages (empty = allowed).
     *
     * NOTE: an outstanding balance no longer blocks enrollment. The financial handling is
     * program-aware and done in EnrollmentService::enroll:
     *   • same program already paid → seated for free (no new charge)
     *   • a new program             → credit auto-deducted, remainder raised as a pending order
     * Only a duplicate membership or a hard time conflict block the operation.
     */
    public function validateEnrollment(int $studentId, Groups $target): array
    {
        $errors = [];

        if ($this->isEnrolledIn($studentId, $target->id)) {
            $errors[] = 'الطالب مسجّل مسبقاً في هذه المجموعة.';
        }

        if ($conflict = $this->findTimeConflict($studentId, $target)) {
            $errors[] = 'تعارض زمني مع مجموعة «' . $conflict->name . '» — لا يمكن التسجيل في موعد متداخل.';
        }

        return $errors;
    }
}
