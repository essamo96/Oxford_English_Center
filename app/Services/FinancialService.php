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
