<?php

namespace App\Services;

use App\Support\NotifyCounts;

class NotificationCounterService
{
    public static function getAllCounters(?int $branchId = null): array
    {
        return [
            'total'                    => NotifyCounts::total($branchId),
            'unread_contacts'          => NotifyCounts::unreadContacts(),
            'unread_bookings'          => NotifyCounts::pendingBookings($branchId),
            'student_messages'         => NotifyCounts::unreadStudentMessages($branchId),
            'teacher_messages'         => NotifyCounts::unreadTeacherMessages($branchId),
            'closed_classes'           => NotifyCounts::closedClasses($branchId),
            'pending_student_payments' => NotifyCounts::pendingStudentPayments($branchId),
            'pending_financial_orders' => NotifyCounts::pendingFinancialOrders($branchId),
        ];
    }

    /**
     * Per-branch breakdown of pending financial notifications (booking-driven pending
     * fee orders + student payment submissions awaiting review). Used by the super admin
     * (branch_id = null) sidebar badge tooltip so they know which branch the total belongs to.
     */
    public static function branchFinancialBreakdown(): array
    {
        return \App\Models\Branch::query()
            ->get(['id', 'name_ar'])
            ->map(function ($branch) {
                return [
                    'branch_id' => $branch->id,
                    'name'      => $branch->name_ar,
                    'count'     => NotifyCounts::pendingFinancialOrders($branch->id)
                                 + NotifyCounts::pendingStudentPayments($branch->id),
                ];
            })
            ->values()
            ->all();
    }
}
