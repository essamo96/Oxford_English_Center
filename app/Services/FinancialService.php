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
            $remainingBalance = max(0, $totalFee - $netPaid);

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
        $remainingBalance = max(0, $totalFee - $netPaid);

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
            $paid = (float) $bucketRows->where('audit_status', 'verified')
                                       ->where('transaction_type', '!=', 'refund')
                                       ->sum('transaction_amount');
            $refunded = (float) $bucketRows->where('audit_status', 'verified')
                                           ->where('transaction_type', 'refund')
                                           ->sum(\DB::raw('ABS(transaction_amount)'));
            $net = $paid - $refunded;
            $summary[] = [
                'group_id'   => $groupId,
                'label'      => $groupId
                                ? (($first->group && $first->group->program ? $first->group->program->title . ' — ' : '') . ($first->group ? $first->group->name : 'Group'))
                                : ($first->student_paid_type ?: 'رسوم خارج المجموعة'),
                'total_fee'  => $totalFee,
                'paid'       => $net,
                'remaining'  => max(0, $totalFee - $net),
                'rows'       => $bucketRows,
            ];
        }

        return [
            'rows'      => $rows,
            'summary'   => $summary,
            'grand_total_due'  => array_sum(array_column($summary, 'total_fee')),
            'grand_total_paid' => array_sum(array_column($summary, 'paid')),
            'grand_total_left' => array_sum(array_column($summary, 'remaining')),
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
            'payment_method_id' => $data['payment_method_id'] ?? null,
            'transaction_type' => $data['type'] ?? 'payment',
            'transaction_amount' => $data['amount'],
            'admin_verified_amount' => $data['verified_amount'] ?? 0,
            'audit_status' => $data['audit_status'] ?? 'pending',
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

        // 2. Pending Amount (Student submitted but not yet verified)
        // For pending records, transaction_amount is usually 0 until admin verifies it.
        $pendingAmount = GroupStudentsFees::where('audit_status', 'pending')
            ->sum('student_fee_paid');

        // 3. Total Remaining (What students still owe)
        // We calculate this by taking the total fees from groups and adding any other 
        // independent fees (like placement tests) that haven't been fully paid.
        $groupFees = GroupStudents::sum('student_fee_total');
        
        // Sum of all verified payments that were specifically for groups
        $groupCollected = GroupStudentsFees::confirmed()
            ->whereNotNull('group_id')
            ->where('transaction_type', 'payment')
            ->sum('transaction_amount');
            
        // For independent fees (like Placement Tests not tied to a group yet)
        $independentRemaining = GroupStudentsFees::whereNull('group_id')
            ->where('audit_status', '!=', 'rejected')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('student_id') // Simplification: get latest for each student
            ->sum('remaining_amount');

        $totalRemaining = ($groupFees - $groupCollected) + $independentRemaining;

        return [
            'total_collected' => $totalCollected,
            'total_remaining' => max(0, $totalRemaining),
            'pending_amount' => $pendingAmount,
        ];
    }
}
