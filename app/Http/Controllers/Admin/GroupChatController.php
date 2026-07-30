<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
////////////////////////////////////
use App\Lib\PusherFactory;
use App\Models\Message;
use App\Models\Groups;
use App\Models\GroupChatBan;
use App\Models\GroupChatRead;
use App\Models\GroupStudents;
use App\Models\Students;
use App\Support\ChatAvatar;
use App\Support\ChatModeration;
use Illuminate\Support\Facades\Log;

/**
 * Admin-side monitor for the group chat that students and teachers already use.
 *
 * It reads and writes the same `messages` table and fires the same Pusher
 * 'chat' / 'send' event the frontend controller does, so an admin comment lands
 * in every open student/teacher chat box live rather than on their next reload.
 * Admin messages are stamped user_type = Message::TYPE_ADMIN (2).
 */
class GroupChatController extends AdminController {

    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND       = "عذراً، لا يمكن العثور على البيانات";

    /** Attachment guard rails — mirrors what the chat UI advertises. */
    const MAX_UPLOAD_KB = 10240; // 10 MB
    const ALLOWED_EXT = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','xls','xlsx','ppt','pptx','txt','zip','rar','mp3','wav','ogg','webm','m4a'];

    //////////////////////////////////////////////
    public function __construct() {
        parent::__construct();
        parent::$data['active_menu'] = 'group_chat';
    }

    //////////////////////////////////////////////
    /**
     * Monitor home: every group, ordered by most recent chat activity, so the
     * conversations that are actually moving float to the top.
     */
    public function getIndex(Request $request) {
        $search = trim((string) $request->get('q', ''));

        $groups = Groups::with(['teacher', 'program'])
            ->whereNull('deleted_at')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            })
            ->get();

        $activity = Message::groupActivitySummary();

        // Student head-count per group, one query for the whole list.
        $counts = GroupStudents::whereNull('deleted_at')
            ->select('group_id', DB::raw('COUNT(*) as c'))
            ->groupBy('group_id')
            ->pluck('c', 'group_id');

        $unread = GroupChatRead::unreadCountsFor(Auth::guard('admin')->id());

        $rows = $groups->map(function ($group) use ($activity, $counts, $unread) {
            $stat = $activity[$group->id] ?? null;
            $group->chat_total    = $stat->total ?? 0;
            $group->chat_last_at  = ($stat && $stat->last_at) ? \Carbon\Carbon::parse($stat->last_at) : null;
            $group->students_count = (int) ($counts[$group->id] ?? 0);
            $group->unread_count   = (int) ($unread[$group->id] ?? 0);
            return $group;
        })->sortByDesc(function ($group) {
            // Groups with unread traffic come first, then by recency.
            return [$group->unread_count > 0 ? 1 : 0, $group->chat_last_at ? $group->chat_last_at->timestamp : 0];
        })->values();

        parent::$data['groups']      = $rows;
        parent::$data['search']      = $search;
        parent::$data['totalChats']  = $rows->sum('chat_total');
        parent::$data['totalUnread'] = $rows->sum('unread_count');

        return view('admin.group_chat.index', parent::$data);
    }

    //////////////////////////////////////////////
    /**
     * The Metronic group-chat screen for a single group.
     */
    public function getShow(Request $request, $id) {
        $group = Groups::with(['teacher', 'program'])->find($id);
        if (!$group) {
            return redirect()->route('group_chat.view')->with(['error' => self::NOT_FOUND]);
        }

        $messages = (new Message())->getGroupMessages($group->id);
        $lastId   = $messages->count() ? $messages->last()->id : 0;

        // Opening the conversation clears its unread badge. Use the true newest id,
        // not the last rendered one, so a group longer than the render window does
        // not stay permanently unread.
        $newestId = (int) Message::where('group_id', $group->id)->max('id');
        GroupChatRead::markRead(Auth::guard('admin')->id(), $group->id, max($lastId, $newestId));

        parent::$data['group']    = $group;
        parent::$data['messages'] = $messages;
        parent::$data['members']  = $this->groupMembers($group);
        parent::$data['lastId']   = $lastId;
        parent::$data['me']       = Auth::guard('admin')->user();

        // Media panel + ban list are rendered with the page so the header shows
        // real counts without a second round trip.
        parent::$data['attachments'] = Message::where('group_id', $group->id)
            ->whereNotNull('attachment')
            ->orderBy('id', 'desc')
            ->get();

        parent::$data['bans'] = GroupChatBan::with('student')
            ->where('group_id', $group->id)
            ->orderByDesc('status')
            ->orderByDesc('updated_at')
            ->get();

        parent::$data['activeBanIds'] = parent::$data['bans']
            ->where('status', true)->pluck('student_id')->all();

        return view('admin.group_chat.show', parent::$data);
    }

    //////////////////////////////////////////////
    /**
     * Incremental fetch. Pusher is the live path; this covers reconnects, a
     * closed socket, or a browser that never received the push.
     */
    public function getMessages(Request $request, $id) {
        $group = Groups::find($id);
        if (!$group) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
        }

        $afterId  = (int) $request->get('after_id', 0);
        $messages = (new Message())->getGroupMessages($group->id, $afterId ?: null);

        $html = [];
        foreach ($messages as $message) {
            $html[] = view('admin.group_chat.parts.message', [
                'message' => $message,
                'meId'    => Auth::guard('admin')->id(),
            ])->render();
        }

        $lastId = $messages->count() ? $messages->last()->id : $afterId;

        // The admin is looking at this conversation right now, so anything we just
        // handed them counts as read — otherwise the badge would reappear for
        // messages they watched arrive.
        if ($messages->count()) {
            GroupChatRead::markRead(Auth::guard('admin')->id(), $group->id, $lastId);
        }

        return response()->json([
            'state'    => 1,
            'messages' => $html,
            'last_id'  => $lastId,
            // Drives the "ding" on the admin side: only ring for someone else's message.
            'has_incoming' => $messages->contains(function ($m) {
                return (int) $m->user_type !== Message::TYPE_ADMIN
                    || (int) $m->from_user !== (int) Auth::guard('admin')->id();
            }),
        ]);
    }

    //////////////////////////////////////////////
    /**
     * Post an admin comment (text and/or attachment/voice note) into a group.
     */
    public function postSend(Request $request, $id) {
        $group = Groups::find($id);
        if (!$group) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
        }

        $user = Auth::guard('admin')->user();
        if (!$user) {
            return response()->json(['state' => 0, 'message' => self::EXECUTION_ERROR], 401);
        }

        $text = trim((string) $request->get('message', ''));
        $hasFile = $request->hasFile('attachment');

        if ($text === '' && !$hasFile) {
            return response()->json(['state' => 0, 'message' => 'الرسالة فارغة'], 422);
        }

        $attachment = $attachmentName = $attachmentType = null;

        if ($hasFile) {
            $validator = Validator::make($request->all(), [
                'attachment' => 'file|max:' . self::MAX_UPLOAD_KB,
            ]);
            if ($validator->fails()) {
                return response()->json(['state' => 0, 'message' => 'الملف أكبر من الحجم المسموح (10 ميجابايت)'], 422);
            }

            $file = $request->file('attachment');
            $ext  = strtolower($file->getClientOriginalExtension());

            // A MediaRecorder blob arrives with no filename/extension, so fall back
            // to the declared kind before rejecting it as an unknown type.
            $kind = $request->get('attachment_kind');
            if ($ext === '' && $kind === 'audio') {
                $ext = 'webm';
            }

            if (!in_array($ext, self::ALLOWED_EXT, true)) {
                return response()->json(['state' => 0, 'message' => 'نوع الملف غير مسموح'], 422);
            }

            $dir = public_path('uploads/chat');
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            $fileName = 'chat_' . $group->id . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $file->move($dir, $fileName);

            $attachment     = 'uploads/chat/' . $fileName;
            $attachmentName = $file->getClientOriginalName() ?: $fileName;
            $attachmentType = $this->attachmentType($ext, $kind);
        }

        $message = new Message();
        $message->from_user      = $user->id;
        $message->user_type      = Message::TYPE_ADMIN;
        $message->content        = $text;
        $message->group_id       = $group->id;
        $message->attachment     = $attachment;
        $message->attachment_name = $attachmentName;
        $message->attachment_type = $attachmentType;
        $message->save();

        // Payload keys match what the student/teacher chat.js already reads, so the
        // existing frontend renders an admin message without a second event shape.
        $payload = [
            'id'                => $message->id,
            'group_id'          => $message->group_id,
            'content'           => $message->content,
            'user_type'         => Message::TYPE_ADMIN,
            'from_user_id'      => $user->id,
            'from_user'         => $user->id,
            'fromUserName'      => $user->name,
            'name'              => $user->name,
            'image'             => $user->image ?? null,
            // Pre-resolved so the receiving JS never has to guess a path — admin
            // images are bare filenames under assets/media/avatars, unlike the
            // student/teacher columns. See App\Support\ChatAvatar.
            'avatar_url'        => ChatAvatar::url($user->image ?? null, Message::TYPE_ADMIN),
            'gender'            => null,
            'attachment'        => $attachment,
            'attachment_name'   => $attachmentName,
            'attachment_type'   => $attachmentType,
            'dateTimeStr'       => date("Y-m-d\TH:i", strtotime($message->created_at->toDateTimeString())),
            'dateHumanReadable' => $message->created_at->diffForHumans(),
        ];

        try {
            PusherFactory::make()->trigger('chat', 'send', ['data' => $payload]);
        } catch (\Throwable $e) {
            // A Pusher outage must not lose the message — it is already persisted and
            // both sides poll as a fallback.
            Log::warning('Group chat pusher trigger failed: ' . $e->getMessage());
        }

        $stored = (new Message())->getOneWithSender($message->id);

        return response()->json([
            'state'   => 1,
            'html'    => view('admin.group_chat.parts.message', [
                'message' => $stored,
                'meId'    => $user->id,
            ])->render(),
            'last_id' => $message->id,
            'data'    => $payload,
        ]);
    }

    //////////////////////////////////////////////
    /**
     * Remove an off-topic / abusive message from a group conversation.
     */
    public function postDelete(Request $request) {
        $message = Message::find($request->get('id'));
        if (!$message) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
        }

        if ($message->attachment && file_exists(public_path($message->attachment))) {
            @unlink(public_path($message->attachment));
        }

        $groupId = $message->group_id;
        $id      = $message->id;
        $message->delete();

        // Remove it from every open student/teacher chat box and other admin tabs
        // immediately — otherwise a deleted message lingers on screen until reload.
        ChatModeration::broadcastMessageDeleted($groupId, $id);

        return response()->json(['state' => 1, 'id' => $id, 'group_id' => $groupId]);
    }

    //////////////////////////////////////////////
    /**
     * Wipe an entire group conversation, attachments and all.
     */
    public function postClear(Request $request, $id) {
        $group = Groups::find($id);
        if (!$group) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
        }

        $messages = Message::where('group_id', $group->id)->get();

        // Delete the uploaded files too, otherwise clearing a chat leaves its
        // attachments orphaned on disk forever.
        foreach ($messages as $message) {
            if ($message->attachment && file_exists(public_path($message->attachment))) {
                @unlink(public_path($message->attachment));
            }
        }

        $count = Message::where('group_id', $group->id)->delete();

        // Read marks now point at ids that no longer exist; reset them so the
        // group does not read as "unread" forever after a wipe.
        GroupChatRead::where('group_id', $group->id)->update(['last_read_message_id' => 0]);

        $this->broadcastGroupState($group, 'cleared');

        return response()->json(['state' => 1, 'deleted' => $count]);
    }

    //////////////////////////////////////////////
    /**
     * Freeze / unfreeze posting for a whole group. Reading is never affected.
     */
    public function postToggleLock(Request $request, $id) {
        $group = Groups::find($id);
        if (!$group) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
        }

        $locked = !((int) ($group->chat_locked ?? 0) === 1);

        $group->chat_locked = $locked ? 1 : 0;
        $group->chat_locked_at = $locked ? now() : null;
        $group->save();

        $this->broadcastGroupState($group, $locked ? 'locked' : 'unlocked');

        // Tell every student in the group directly, so an open chat box flips to
        // read-only (or back) without waiting for a reload.
        $studentIds = GroupStudents::where('group_id', $group->id)
            ->whereNull('deleted_at')->pluck('student_id');

        foreach ($studentIds as $studentId) {
            ChatModeration::notifyStudent($studentId, [
                'type'     => $locked ? 'group_chat_locked' : 'group_chat_unlocked',
                'group_id' => $group->id,
                'title'    => $locked ? 'تم إيقاف المراسلة' : 'تم استئناف المراسلة',
                'message'  => $locked
                    ? 'أوقفت الإدارة إرسال الرسائل في مجموعة "' . $group->name . '". لا يزال بإمكانك قراءة المحادثة.'
                    : 'يمكنك الآن إرسال الرسائل في مجموعة "' . $group->name . '" مرة أخرى.',
            ]);
        }

        return response()->json([
            'state'  => 1,
            'locked' => $locked,
            'message' => $locked ? 'تم إيقاف المراسلة في المجموعة' : 'تم تفعيل المراسلة في المجموعة',
        ]);
    }

    //////////////////////////////////////////////
    /**
     * Search inside one group's messages.
     */
    public function getSearch(Request $request, $id) {
        $group = Groups::find($id);
        if (!$group) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
        }

        $term = trim((string) $request->get('q', ''));
        if ($term === '') {
            return response()->json(['state' => 1, 'messages' => [], 'total' => 0]);
        }

        $results = (new Message())->searchInGroup($group->id, $term);

        $html = [];
        foreach ($results as $message) {
            $html[] = view('admin.group_chat.parts.search-result', [
                'message' => $message,
                'term'    => $term,
            ])->render();
        }

        return response()->json(['state' => 1, 'messages' => $html, 'total' => $results->count()]);
    }

    //////////////////////////////////////////////
    /**
     * Current restriction state for one student, used to build the avatar-click
     * moderation menu. Read from the server rather than the DOM, since the
     * restriction may have changed since the bubble was rendered.
     */
    public function getStudentState(Request $request, $id) {
        $group = Groups::find($id);
        if (!$group) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
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
     * Ban a student from posting in this group. `reason` is optional — a silent
     * ban still tells the student they were muted, just without a stated cause.
     */
    public function postBan(Request $request, $id) {
        $group = Groups::find($id);
        if (!$group) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
        }

        $studentId = (int) $request->get('student_id');
        $student   = Students::find($studentId);
        if (!$student) {
            return response()->json(['state' => 0, 'message' => 'الطالب غير موجود'], 404);
        }

        $reason = trim((string) $request->get('reason', ''));
        $reason = $reason === '' ? null : $reason;

        // 'ban' cuts off reading too; anything else is a mute (post-only block).
        $type = $request->get('type') === GroupChatBan::TYPE_BAN
            ? GroupChatBan::TYPE_BAN
            : GroupChatBan::TYPE_MUTE;

        $ban = ChatModeration::restrict(
            $group, $studentId, $type, $reason, Auth::guard('admin')->id(), 'admin'
        );

        return response()->json([
            'state' => 1,
            'html'  => view('admin.group_chat.parts.ban-row', [
                'ban' => $ban->load('student'),
            ])->render(),
            'student_id' => $studentId,
            'type'       => $type,
            'message' => $type === GroupChatBan::TYPE_BAN
                ? 'تم حظر الطالب من المجموعة'
                : 'تم إسكات الطالب',
        ]);
    }

    //////////////////////////////////////////////
    /**
     * Lift a ban. The row stays as history with status 0.
     */
    public function postUnban(Request $request, $id) {
        $group = Groups::find($id);
        if (!$group) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
        }

        $studentId = (int) $request->get('student_id');
        $ban = ChatModeration::lift($group, $studentId, Auth::guard('admin')->id());
        if (!$ban) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
        }

        return response()->json([
            'state' => 1,
            'html'  => view('admin.group_chat.parts.ban-row', [
                'ban' => $ban->load('student'),
            ])->render(),
            'student_id' => $studentId,
            'message' => 'تم رفع القيد عن الطالب',
        ]);
    }

    //////////////////////////////////////////////
    /**
     * Live unread counts for the monitor index, so its red badges update without
     * a page reload.
     */
    public function getUnread(Request $request) {
        $counts = GroupChatRead::unreadCountsFor(Auth::guard('admin')->id());

        return response()->json([
            'state'  => 1,
            'counts' => (object) $counts,
            'total'  => array_sum($counts),
        ]);
    }

    //////////////////////////////////////////////
    /**
     * Announce a group-level state change on the shared chat channel, so open
     * chat boxes and other admin tabs react immediately.
     */
    private function broadcastGroupState($group, $state) {
        try {
            PusherFactory::make()->trigger('chat', 'group-state', [
                'data' => [
                    'group_id' => $group->id,
                    'state'    => $state,
                    'locked'   => (int) ($group->chat_locked ?? 0) === 1,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Group chat state broadcast failed: ' . $e->getMessage());
        }
    }

    //////////////////////////////////////////////
    private function attachmentType($ext, $kind = null) {
        if ($kind === 'audio' || in_array($ext, ['mp3','wav','ogg','webm','m4a'], true)) {
            return 'audio';
        }
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'], true)) {
            return 'image';
        }
        return 'file';
    }

    //////////////////////////////////////////////
    /**
     * Group roster (teacher first, then students) for the avatar strip in the
     * chat header and the "view members" modal.
     */
    private function groupMembers($group) {
        $members = collect();

        if ($group->teacher) {
            $members->push((object) [
                'id'      => $group->teacher->id,
                'name'    => $group->teacher->name,
                'image'   => $group->teacher->image ?? null,
                'role'    => 'teacher',
                'role_ar' => 'المعلم',
            ]);
        }

        $students = GroupStudents::with('student')
            ->where('group_id', $group->id)
            ->whereNull('deleted_at')
            ->get();

        foreach ($students as $row) {
            if (!$row->student) {
                continue;
            }
            $members->push((object) [
                'id'      => $row->student->id,
                'name'    => $row->student->name,
                'image'   => $row->student->image ?? null,
                'role'    => 'student',
                'role_ar' => 'طالب',
            ]);
        }

        return $members;
    }
}
