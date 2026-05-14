<?php

namespace App\Services;

use App\Models\GroupStudents;
use App\Models\GroupStudentsFees;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    /**
     * Get the student's financial ledger for a specific group/level.
     */
    public function getStudentLedger($studentId, $groupId)
    {
        if (!$studentId || !$groupId) {
            return null;
        }

        $groupStudent = GroupStudents::where('student_id', $studentId)
            ->where('group_id', $groupId)
            ->first();

        $transactions = GroupStudentsFees::with('paymentMethod')
            ->where('student_id', $studentId)
            ->where('group_id', $groupId)
            ->orderBy('created_at', 'asc')
            ->get();

        $totalFee = $groupStudent ? $groupStudent->student_fee_total : 0;
        $confirmedPaid = $transactions->where('audit_status', 'verified')->sum('transaction_amount');
        $remainingBalance = $totalFee - $confirmedPaid;

        return [
            'group_student' => $groupStudent,
            'transactions' => $transactions,
            'total_fee' => $totalFee,
            'total_paid' => $confirmedPaid,
            'remaining_balance' => $remainingBalance,
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
        $totalCollected = GroupStudentsFees::confirmed()->where('transaction_type', 'payment')->sum('transaction_amount')
                        - GroupStudentsFees::confirmed()->where('transaction_type', 'refund')->sum(DB::raw('ABS(transaction_amount)'));

        $pendingAmount = GroupStudentsFees::where('audit_status', 'pending')->sum('transaction_amount');

        // Remaining is harder globally without joining group_students
        $totalFees = GroupStudents::sum('student_fee_total');
        $totalRemaining = $totalFees - $totalCollected;

        return [
            'total_collected' => $totalCollected,
            'total_remaining' => $totalRemaining,
            'pending_amount' => $pendingAmount,
        ];
    }
}
