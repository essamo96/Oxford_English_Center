<?php

namespace App\Http\Controllers;

use App\Models\GroupChatBan;
use App\Models\Groups;
use App\Models\GroupStudents;
use App\Models\Students;
use App\Support\ChatAvatar;
use App\Support\ChatModeration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Lets a teacher mute or ban a student inside a group they teach.
 *
 * Same effect as the admin monitor's moderation — it goes through
 * App\Support\ChatModeration so the row, the student notification and the live
 * broadcast are identical no matter who applied the restriction.
 *
 * Every action re-checks that the authenticated teacher actually owns the group:
 * the group id arrives from the browser, so ownership can never be assumed.
 */
class TeacherChatModerationController extends Controller
{
    const NOT_FOUND  = "عذراً، لا يمكن العثور على البيانات";
    const NOT_YOURS  = "لا تملك صلاحية إدارة هذه المجموعة";
    const NOT_MEMBER = "هذا الطالب ليس عضواً في المجموعة";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Resolve the group only if this teacher teaches it.
     */
    private function ownedGroup($groupId)
    {
        $teacher = Auth::guard('teachers')->user();
        if (!$teacher) {
            return null;
        }

        $group = Groups::withoutGlobalScopes()->find($groupId);
        if (!$group || (int) $group->teacher_id !== (int) $teacher->id) {
            return null;
        }

        return $group;
    }

    //////////////////////////////////////////////
    /**
     * Current restriction state for one student, used to build the popup menu
     * when the teacher clicks that student's avatar in the chat.
     */
    public function getStudentState(Request $request)
    {
        $group = $this->ownedGroup($request->get('group_id'));
        if (!$group) {
            return response()->json(['state' => 0, 'message' => self::NOT_YOURS], 403);
        }

        $studentId = (int) $request->get('student_id');
        $student   = Students::find($studentId);
        if (!$student) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
        }

        $ban = GroupChatBan::activeBan($studentId, $group->id);

        return response()->json([
            'state'      => 1,
            'student_id' => $studentId,
            'name'       => $student->name,
            'avatar'     => ChatAvatar::url($student->image ?? null, 0),
            'restricted' => (bool) $ban,
            'type'       => $ban->type ?? null,
            'reason'     => $ban->reason ?? null,
        ]);
    }

    //////////////////////////////////////////////
    /**
     * Mute (post-only block) or ban (no access at all) a student.
     */
    public function postRestrict(Request $request)
    {
        $group = $this->ownedGroup($request->get('group_id'));
        if (!$group) {
            return response()->json(['state' => 0, 'message' => self::NOT_YOURS], 403);
        }

        $studentId = (int) $request->get('student_id');

        // A teacher may only moderate students who are actually in this group.
        $isMember = GroupStudents::where('group_id', $group->id)
            ->where('student_id', $studentId)
            ->whereNull('deleted_at')
            ->exists();

        if (!$isMember) {
            return response()->json(['state' => 0, 'message' => self::NOT_MEMBER], 422);
        }

        $type = $request->get('type') === GroupChatBan::TYPE_BAN
            ? GroupChatBan::TYPE_BAN
            : GroupChatBan::TYPE_MUTE;

        $reason = trim((string) $request->get('reason', ''));
        $reason = $reason === '' ? null : $reason;

        ChatModeration::restrict(
            $group, $studentId, $type, $reason, Auth::guard('teachers')->id(), 'teacher'
        );

        return response()->json([
            'state'      => 1,
            'student_id' => $studentId,
            'type'       => $type,
            'message'    => $type === GroupChatBan::TYPE_BAN
                ? 'تم حظر الطالب من المجموعة'
                : 'تم إسكات الطالب',
        ]);
    }

    //////////////////////////////////////////////
    /**
     * Lift whatever restriction is in force.
     */
    public function postLift(Request $request)
    {
        $group = $this->ownedGroup($request->get('group_id'));
        if (!$group) {
            return response()->json(['state' => 0, 'message' => self::NOT_YOURS], 403);
        }

        $studentId = (int) $request->get('student_id');
        $ban = ChatModeration::lift($group, $studentId, Auth::guard('teachers')->id());

        if (!$ban) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
        }

        return response()->json([
            'state'      => 1,
            'student_id' => $studentId,
            'message'    => 'تم رفع القيد عن الطالب',
        ]);
    }
}
