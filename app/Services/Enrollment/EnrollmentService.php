<?php

namespace App\Services\Enrollment;

use App\Exceptions\EnrollmentException;
use App\Models\FeeSettings;
use App\Models\Groups;
use App\Models\GroupStudents;
use App\Models\GroupStudentsFees;
use App\Models\Programs;
use App\Models\Students;
use App\Services\FinancialService;
use Illuminate\Support\Facades\DB;

/**
 * All persistence for branching & promotion. Every money-touching operation is
 * wrapped in a database transaction. Validation is delegated to EnrollmentValidator
 * so this class only runs once the rules have passed.
 */
class EnrollmentService
{
    public function __construct(
        private FinancialService $finance,
        private EnrollmentValidator $validator
    ) {}

    /**
     * Total fee due for a program = SUM of ALL its FeeSettings regardless of type
     * (course, registration, books, placement_test, …). Every branching operation
     * charges the full due for the target program.
     */
    public function programFee(int $programId): float
    {
        return (float) FeeSettings::where('program_id', $programId)->sum('amount');
    }

    /**
     * Enroll a student into a group — program-aware financial handling, one transaction:
     *
     *  SAME program the student already paid for (≥ its minimum at registration):
     *    → may be seated in ANY of that program's groups with NO new charge.
     *      If the registration payment is still pre-group it is carried into this group
     *      so the group ledger reflects the real due/paid/remaining; otherwise it is a free seat.
     *
     *  NEW (different) program — not tied to any of the student's payments:
     *    → billed the program's full fee; the due is DEDUCTED from the student's credit balance,
     *      and any remainder is raised as a PENDING order (الطلبات العالقة) at the minimum
     *      installment (the student may still pay in full). The credit deduction is noted on
     *      the pending order so it is distinguished when the payment is confirmed.
     *
     * @return array{fee:float, credit_applied:float, carried:float, remaining:float,
     *               min_payment:float, pending_created:bool, new_program:bool, no_fee:bool}
     */
    public function enroll(int $studentId, Groups $group, ?int $adminId = null): array
    {
        $errors = $this->validator->validateEnrollment($studentId, $group);
        if (!empty($errors)) {
            throw new EnrollmentException($errors);
        }

        return DB::transaction(function () use ($studentId, $group, $adminId) {
            $programId    = (int) $group->program_id;
            $isNewProgram = !in_array($programId, $this->validator->paidProgramIds($studentId), true);

            $membership = GroupStudents::create([
                'student_id'         => $studentId,
                'group_id'           => $group->id,
                'student_fee_total'  => 0,
                'student_book_total' => 0,
                'status'             => 1,
            ]);

            $fee = 0.0; $carried = 0.0; $creditApplied = 0.0; $remaining = 0.0;
            $minDue = 0.0; $pendingCreated = false;

            if ($isNewProgram) {
                // ---------- NEW program → fresh charge ----------
                $fee     = $this->programFee($programId);
                $program = Programs::find($programId);
                $minDue  = $program ? (float) $program->computeMinimumDue($fee) : $fee;
                $membership->student_fee_total = $fee;
                $membership->save();

                // Deduct from credit first
                $creditApplied = $this->applyCreditToGroup($studentId, $group->id, $adminId);

                $ledger    = $this->finance->getStudentLedger($studentId, $group->id);
                $remaining = $ledger ? round((float) $ledger['remaining_balance'], 2)
                                     : round(max(0, $fee - $creditApplied), 2);

                // Remainder → pending order at the minimum installment
                if ($remaining > 0.009) {
                    $minNow     = min($minDue > 0 ? $minDue : $remaining, $remaining);
                    $creditNote = $creditApplied > 0.009
                        ? ' — خُصم من الرصيد الدائن ' . number_format($creditApplied, 2) . ' ILS'
                        : '';
                    GroupStudentsFees::create([
                        'student_id'         => $studentId,
                        'group_id'           => $group->id,
                        'program_id'         => $programId,
                        'student_fee_paid'   => 0,
                        'transaction_amount' => 0,
                        'transaction_type'   => 'adjustment',
                        'total_due_amount'   => $fee,
                        'remaining_amount'   => $remaining,
                        'student_paid_type'  => 'Course Enrollment',
                        'status'             => 'pending',
                        'audit_status'       => 'pending', // → الطلبات العالقة
                        'verified_by'        => $adminId,
                        'notes'              => 'رسوم برنامج جديد عند التشعيب — الحد الأدنى للدفعة: '
                                                . number_format($minNow, 2) . ' ILS (الإجمالي المتبقّي '
                                                . number_format($remaining, 2) . ' ILS)' . $creditNote,
                    ]);
                    $pendingCreated = true;
                }
            } else {
                // ---------- SAME program already paid → no new charge ----------
                // Carry the registration payment of THIS program into the group when still pre-group.
                $carried = $this->adoptProgramPayment($studentId, $group);
                if ($carried > 0.009) {
                    $fee = $this->programFee($programId);
                    $membership->student_fee_total = $fee;
                    $membership->save();
                    // Apply any credit to whatever remains of the program's balance
                    $creditApplied = $this->applyCreditToGroup($studentId, $group->id, $adminId);
                    $ledger    = $this->finance->getStudentLedger($studentId, $group->id);
                    $remaining = $ledger ? round((float) $ledger['remaining_balance'], 2) : 0.0;
                }
                // else: additional group of an already-paid program → free seat (fee_total stays 0)
            }

            return [
                'fee'             => round($fee, 2),
                'credit_applied'  => round($creditApplied, 2),
                'carried'         => round($carried, 2),
                'remaining'       => round($remaining, 2),
                'min_payment'     => round($remaining > 0 ? min($minDue, $remaining) : 0, 2),
                'pending_created' => $pendingCreated,
                'new_program'     => $isNewProgram,
                'no_fee'          => $isNewProgram && $fee <= 0.009,
            ];
        });
    }

    /**
     * Move ALL of a student's verified pre-group payments for THIS program into the target
     * group so the FULL amount already paid counts toward the enrollment (registration payment
     * + any later installments). Program-matched so a new-program enrollment never consumes a
     * different program's money; placement-test, credit and refund rows are never moved.
     * Returns the total amount carried.
     */
    private function adoptProgramPayment(int $studentId, Groups $group): float
    {
        $rows = GroupStudentsFees::where('student_id', $studentId)
            ->whereNull('group_id')
            ->whereNull('deleted_at')
            ->where('audit_status', 'verified')
            ->where('student_paid_type', 'not like', '%Placement%')
            // payments only (exclude credit / refund markers)
            ->where(function ($q) {
                $q->where('transaction_type', 'payment')->orWhereNull('transaction_type');
            })
            // this program (or legacy rows with no program set)
            ->where(function ($q) use ($group) {
                $q->where('program_id', $group->program_id)->orWhereNull('program_id');
            })
            ->get();

        $carried = 0.0;
        foreach ($rows as $row) {
            $row->group_id   = $group->id;
            $row->program_id = $group->program_id;
            $row->save();
            $carried += (float) ($row->transaction_amount ?: $row->student_fee_paid);
        }
        return $carried;
    }

    /**
     * Consume the student's available credit balance to pay down a group's REMAINING
     * due, recording it as a proper payment + a matching credit-consumption row.
     * Caller is responsible for wrapping in a transaction when part of a larger unit.
     *
     * @return float the amount of credit applied
     */
    public function applyCreditToGroup(int $studentId, int $groupId, ?int $adminId = null): float
    {
        $ledger = $this->finance->getStudentLedger($studentId, $groupId);
        $due    = $ledger ? (float) $ledger['remaining_balance'] : 0.0;
        if ($due <= 0.009) return 0.0;

        $credit  = $this->validator->creditBalance($studentId);
        $applied = min($credit, $due);
        if ($applied <= 0.009) return 0.0;

        // 1) Count it as a verified payment toward the group (reduces group remaining)
        $this->finance->recordTransaction([
            'student_id'      => $studentId,
            'group_id'        => $groupId,
            'amount'          => $applied,
            'verified_amount' => $applied,
            'audit_status'    => 'verified',
            'verified_by'     => $adminId,
            'type'            => 'payment',
            'paid_type'       => 'Credit Applied',
            'notes'           => 'خصم تلقائي من الرصيد الدائن للطالب لتسديد رسوم المجموعة',
        ]);

        // 2) Draw the same amount down from the credit pool (negative credit row)
        GroupStudentsFees::create([
            'student_id'         => $studentId,
            'group_id'           => null,
            'program_id'         => null,
            'student_fee_paid'   => 0,
            'transaction_amount' => -$applied,
            'transaction_type'   => 'credit',
            'total_due_amount'   => 0,
            'remaining_amount'   => 0,
            'student_paid_type'  => 'Credit Used',
            'status'             => 'confirmed',
            'audit_status'       => 'verified',
            'verified_by'        => $adminId,
            'notes'              => 'استخدام رصيد دائن لتسديد رسوم مجموعة #' . $groupId,
        ]);

        return round($applied, 2);
    }

    /**
     * Promote students from one group/level to another.
     *   - Only students with ZERO outstanding balance are promoted.
     *   - Students who owe money are SKIPPED and returned (name + amount due).
     *   - Promoted students are billed the target program's fee, credit auto-applied,
     *     and their current_level is updated. Each student is handled in its own
     *     transaction so one failure never corrupts the rest.
     *
     * @return array{promoted: array<int,array>, skipped: array<int,array>}
     */
    public function promote(array $studentIds, Groups $source, Groups $target, ?int $adminId = null): array
    {
        $names    = Students::whereIn('id', $studentIds)->pluck('name', 'id');
        $promoted = [];
        $skipped  = [];

        foreach ($studentIds as $sid) {
            $sid = (int) $sid;
            $outstanding = $this->validator->outstandingBalance($sid);

            // Financial gate: anyone with arrears is never promoted
            if ($outstanding > 0.009) {
                $skipped[] = [
                    'student_id' => $sid,
                    'name'       => $names[$sid] ?? ('#' . $sid),
                    'amount'     => $outstanding,
                ];
                continue;
            }

            DB::transaction(function () use ($sid, $source, $target, $adminId, $names, &$promoted) {
                // End the old membership
                GroupStudents::where('student_id', $sid)
                    ->where('group_id', $source->id)
                    ->whereNull('deleted_at')
                    ->delete();

                // Open the target membership + bill the new level's fee (skip if already there)
                if (!$this->validator->isEnrolledIn($sid, $target->id)) {
                    $fee = $this->programFee($target->program_id);
                    GroupStudents::create([
                        'student_id'         => $sid,
                        'group_id'           => $target->id,
                        'student_fee_total'  => $fee,
                        'student_book_total' => 0,
                        'status'             => 1,
                    ]);
                    $this->applyCreditToGroup($sid, $target->id, $adminId);
                }

                Students::where('id', $sid)->update(['current_level' => $target->name]);

                $promoted[] = ['student_id' => $sid, 'name' => $names[$sid] ?? ('#' . $sid)];
            });
        }

        return ['promoted' => $promoted, 'skipped' => $skipped];
    }
}
