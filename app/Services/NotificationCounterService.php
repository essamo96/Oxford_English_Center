<?php

namespace App\Services;

use App\Support\NotifyCounts;

class NotificationCounterService
{
    public static function getAllCounters(?int $branchId = null): array
    {
        return [
            'total'            => NotifyCounts::total($branchId),
            'unread_contacts'  => NotifyCounts::unreadContacts(),
            'unread_bookings'  => NotifyCounts::pendingBookings($branchId),
            'student_messages' => NotifyCounts::unreadStudentMessages($branchId),
            'teacher_messages' => NotifyCounts::unreadTeacherMessages($branchId),
            'closed_classes'   => NotifyCounts::closedClasses($branchId),
        ];
    }
}
