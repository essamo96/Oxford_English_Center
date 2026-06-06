<?php

namespace App\Services;

use App\Models\Closed_Classes;
use App\Models\Contacts;
use App\Models\Students;
use App\Models\Students_Admin_Messages;
use App\Models\Teachers_Admin_Messages;

class NotificationCounterService
{
    public static function getUnreadContacts(): int
    {
        return Contacts::where('status', 0)->count();
    }

    public static function getUnreadBookings(): int
    {
        // Bookings are Students where seen = 0 and status = 0
        return Students::where('seen', 0)->where('status', 0)->whereNull('deleted_at')->count();
    }

    public static function getUnreadStudentsMessages(): int
    {
        return (int) (new Students_Admin_Messages())->getAllUnreadStudentsMessages();
    }

    public static function getUnreadTeachersMessages(): int
    {
        return (int) (new Teachers_Admin_Messages())->getAllUnreadTeachersMessages();
    }

    public static function getClosedClassesCount(): int
    {
        return Closed_Classes::where('seen', 0)->whereNull('deleted_at')->count();
    }

    public static function getTotal(): int
    {
        return self::getUnreadContacts() +
               self::getUnreadBookings() +
               self::getUnreadStudentsMessages() +
               self::getUnreadTeachersMessages() +
               self::getClosedClassesCount();
    }

    public static function getAllCounters(): array
    {
        return [
            'total'             => self::getTotal(),
            'unread_contacts'   => self::getUnreadContacts(),
            'unread_bookings'   => self::getUnreadBookings(),
            'student_messages'  => self::getUnreadStudentsMessages(),
            'teacher_messages'  => self::getUnreadTeachersMessages(),
            'closed_classes'    => self::getClosedClassesCount()
        ];
    }
}
