<?php

namespace App\Support;

use App\Models\Closed_Classes;
use App\Models\Contacts;
use App\Models\Groups;
use App\Models\Students;
use App\Models\Students_Admin_Messages;
use App\Models\Teachers_Admin_Messages;

/**
 * Single source of truth for the admin "header bell" notification counts.
 * All methods accept an optional $branchId — null means "all branches" (super admin).
 */
class NotifyCounts
{
    /** New front-end registrations awaiting activation. */
    public static function pendingBookings(?int $branchId = null): int
    {
        $q = Students::where('seen', 0)->where('status', 0)->whereNull('deleted_at');
        if ($branchId) $q->where('branch_id', $branchId);
        return $q->count();
    }

    /** Registrations created today. */
    public static function todayBookings(?int $branchId = null): int
    {
        $q = Students::whereDate('created_at', today())->whereNull('deleted_at');
        if ($branchId) $q->where('branch_id', $branchId);
        return $q->count();
    }

    /** Unread "Contact Us" messages — not branch-scoped (general form). */
    public static function unreadContacts(): int
    {
        return Contacts::where('status', 0)->count();
    }

    /** Unseen closed classes. */
    public static function closedClasses(?int $branchId = null): int
    {
        $q = Closed_Classes::where('seen', 0)->whereNull('deleted_at');
        if ($branchId) $q->whereHas('Groups', fn($g) => $g->where('branch_id', $branchId));
        return $q->count();
    }

    /** Unread student messages. */
    public static function unreadStudentMessages(?int $branchId = null): int
    {
        $q = Students_Admin_Messages::where('seen', 0)->whereNull('deleted_at');
        if ($branchId) {
            $q->whereHas('student', fn($s) => $s->where('branch_id', $branchId));
        }
        return $q->count();
    }

    /** Unread teacher messages. */
    public static function unreadTeacherMessages(?int $branchId = null): int
    {
        $q = Teachers_Admin_Messages::where('seen', 0)->whereNull('deleted_at');
        if ($branchId) {
            $q->whereHas('teacher', fn($t) => $t->where('branch_id', $branchId));
        }
        return $q->count();
    }

    /** Grand total shown on the header bell badge. */
    public static function total(?int $branchId = null): int
    {
        return self::closedClasses($branchId)
            + self::pendingBookings($branchId)
            + self::unreadContacts()
            + self::unreadStudentMessages($branchId)
            + self::unreadTeacherMessages($branchId);
    }

    /** All notification data needed by the header dropdown (lists + counts). */
    public static function allForView(?int $branchId = null): array
    {
        $closedCount   = self::closedClasses($branchId);
        $studentsCount = self::pendingBookings($branchId);
        $contactsCount = self::unreadContacts();
        $studentMsgs   = self::unreadStudentMessages($branchId);
        $teacherMsgs   = self::unreadTeacherMessages($branchId);

        $closedQ = Closed_Classes::with(['Teacher', 'Groups'])->where('seen', 0)->whereNull('deleted_at');
        if ($branchId) $closedQ->whereHas('Groups', fn($g) => $g->where('branch_id', $branchId));

        $studentsQ = Students::where('seen', 0)->where('status', 0)->whereNull('deleted_at');
        if ($branchId) $studentsQ->where('branch_id', $branchId);

        $studentMsgsQ = Students_Admin_Messages::where('seen', 0)->whereNull('deleted_at');
        if ($branchId) {
            $studentMsgsQ->whereHas('student', fn($s) => $s->where('branch_id', $branchId));
        }

        $teacherMsgsQ = Teachers_Admin_Messages::where('seen', 0)->whereNull('deleted_at');
        if ($branchId) {
            $teacherMsgsQ->whereHas('teacher', fn($t) => $t->where('branch_id', $branchId));
        }

        $groupsQ = Groups::where('seen_progress', 0)->where('progress', '!=', 0)->whereNull('deleted_at');
        if ($branchId) $groupsQ->where('branch_id', $branchId);

        return [
            'Closed_Classes_count'           => $closedCount,
            'Unseen_Students_Count'          => $studentsCount,
            'Unseen_Contacts_Count'          => $contactsCount,
            'Unread_measges_student'         => $studentMsgs,
            'Unread_measges_teacher'         => $teacherMsgs,
            'total_notify_count'             => $closedCount + $studentsCount + $contactsCount + $studentMsgs + $teacherMsgs,
            'total_teachers_students_measge' => $studentMsgs + $teacherMsgs,
            'notify_closed_clases'           => $closedQ->latest()->take(5)->get(),
            'notify_students'                => $studentsQ->latest()->take(5)->get(),
            'notify_Students_Admin_Messages' => $studentMsgsQ->latest()->take(5)->get(),
            'notify_Teachers_Admin_Messages' => $teacherMsgsQ->latest()->take(5)->get(),
            'notify_Groups'                  => $groupsQ->latest()->take(5)->get(),
            'notify_contacts'                => Contacts::where('status', 0)->latest()->take(5)->get(),
        ];
    }
}
