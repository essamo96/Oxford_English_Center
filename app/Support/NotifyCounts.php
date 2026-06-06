<?php

namespace App\Support;

use App\Models\Closed_Classes;
use App\Models\Contacts;
use App\Models\Students;
use App\Models\Students_Admin_Messages;
use App\Models\Teachers_Admin_Messages;

/**
 * Single source of truth for the admin "header bell" notification counts.
 *
 * The definitions here intentionally mirror the ones computed in
 * App\Http\Controllers\Admin\AdminController so the live (broadcast) counter
 * and the server-rendered badge never drift apart.
 */
class NotifyCounts
{
    /** New front-end registrations awaiting activation (a "booking"). */
    public static function pendingBookings(): int
    {
        return Students::where('seen', 0)
            ->where('status', 0)
            ->whereNull('deleted_at')
            ->count();
    }

    /** Registrations created today. */
    public static function todayBookings(): int
    {
        return Students::whereDate('created_at', today())
            ->whereNull('deleted_at')
            ->count();
    }

    /** Unread "Contact Us" messages (status 0 = pending). */
    public static function unreadContacts(): int
    {
        return Contacts::where('status', 0)->count();
    }

    /** Grand total shown on the header bell badge. */
    public static function total(): int
    {
        $closedClasses     = Closed_Classes::where('seen', 0)->whereNull('deleted_at')->count();
        $unreadStudentMsgs = (int) (new Students_Admin_Messages())->getAllUnreadStudentsMessages();
        $unreadTeacherMsgs = (int) (new Teachers_Admin_Messages())->getAllUnreadTeachersMessages();

        return $closedClasses
            + self::pendingBookings()
            + self::unreadContacts()
            + $unreadStudentMsgs
            + $unreadTeacherMsgs;
    }
}
