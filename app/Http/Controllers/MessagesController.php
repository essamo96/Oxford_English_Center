<?php

namespace App\Http\Controllers;

use App\Lib\PusherFactory;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\GroupStudents;
use App\Models\GroupChatBan;
use App\Models\Groups;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller {

    const SEND_SUCCESS_MESSAGE = "تم إرسال الرسالة";
    const INSERT_SUCCESS_MESSAGE = 'site.add_student_success';
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const PASSWORD_SUCCESS = "نجاح، تم تغيير كلمة المرور بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً،لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";
    const CHAT_LOCKED_MESSAGE = "تم إيقاف المراسلة في هذه المجموعة من قبل الإدارة.";
    const CHAT_BANNED_MESSAGE = "لا يمكنك إرسال رسائل في هذه المجموعة — تم حظرك من قبل الإدارة.";

    public function __construct() {
        parent::__construct();
    }

    ////////////////////////////////////////////////////////////////////////////
    public function getLoadLatestMessages(Request $request, $type) {
        if (!$request->group_id) {
            return;
        }
        $group_id = $request->group_id;

        // A full ban removes read access, so bail out before loading anything —
        // returning the history and hiding it in the UI would not actually
        // withhold it from a student reading the AJAX response.
        if ($type === 'student' && Auth::guard('students')->check()) {
            $ban = GroupChatBan::activeBan(Auth::guard('students')->id(), $group_id);
            if ($ban && $ban->isFullBan()) {
                return response()->json([
                    'state'        => 1,
                    'messages'     => [],
                    'can_send'     => false,
                    'can_view'     => false,
                    'block_reason' => GroupChatBan::blockMessage($ban->type, $ban->reason),
                ]);
            }
        }

        $messages = new Message();
        $messages = $messages->getLastMessages($group_id, $type);
        $reversed = $messages->reverse();
        $return = [];

        foreach ($reversed as $message) {

            $return[] = view('frontend.chat.message-line')->with('message', $message)->render();
        }

        // The chat box disables its composer from this, so a locked group or a
        // banned student sees the reason instead of a send button that 403s.
        $group  = Groups::withoutGlobalScopes()->find($group_id);
        $locked = $group && (int) ($group->chat_locked ?? 0) === 1;
        $ban    = null;
        if ($type === 'student' && Auth::guard('students')->check()) {
            $ban = GroupChatBan::activeBan(Auth::guard('students')->id(), $group_id);
        }

        return response()->json([
            'state'    => 1,
            'messages' => $return,
            'can_send' => !$locked && !$ban,
            'can_view' => true,
            'block_reason' => $locked
                ? self::CHAT_LOCKED_MESSAGE
                : ($ban ? GroupChatBan::blockMessage($ban->type, $ban->reason) : null),
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////
    public function postSendMessage(Request $request, $type) {
        if (!$request->to_user || !$request->message) {
            return;
        }

        $message = new Message();
        if ($type == 'student') {
            $user_type = 0;
            $user = Auth::guard('students')->user();
        } else if ($type == 'teacher') {
            $user_type = 1;
            $user = Auth::guard('teachers')->user();
        } else {
            // Any other $type left $user/$user_type undefined and fatalled on the
            // next line; the routes always pass one of the two, but a bad request
            // should not be able to 500 the chat.
            return response()->json(['state' => 0, 'message' => self::EXECUTION_ERROR], 400);
        }

        if (!$user) {
            return response()->json(['state' => 0, 'message' => self::EXECUTION_ERROR], 401);
        }

        // ── moderation gates ──────────────────────────────────────────────
        // Enforced here, not just in the UI: hiding the send button does not stop
        // a crafted POST, and this is the only place a message can be created.
        $group = Groups::withoutGlobalScopes()->find($request->to_user);
        if (!$group) {
            return response()->json(['state' => 0, 'message' => self::NOT_FOUND], 404);
        }

        if ((int) ($group->chat_locked ?? 0) === 1) {
            return response()->json([
                'state'   => 0,
                'blocked' => 'locked',
                'message' => self::CHAT_LOCKED_MESSAGE,
            ], 403);
        }

        if ($user_type === 0) {
            $ban = GroupChatBan::activeBan($user->id, $group->id);
            if ($ban) {
                return response()->json([
                    'state'    => 0,
                    'blocked'  => $ban->isFullBan() ? 'banned' : 'muted',
                    'can_view' => !$ban->isFullBan(),
                    'reason'   => $ban->reason,
                    'message'  => GroupChatBan::blockMessage($ban->type, $ban->reason),
                ], 403);
            }
        }

        $message->from_user = $user->id;
        $message->user_type = $user_type;
        $message->content = $request->message;
        $message->group_id = $request->to_user;
        $message->save();

        // prepare some data to send with the response
        $message->dateTimeStr = date("Y-m-dTH:i", strtotime($message->created_at->toDateTimeString()));
        $message->dateHumanReadable = $message->created_at->diffForHumans();
        $message->fromUserName = $message->name ?? $user->name;
        $message->from_user_id = $user->id;
        $message->image = $user->image ?? null;
        // Resolved server-side: the three sender tables store `image` in three
        // different shapes (bare filename / absolute URL / relative path), so the
        // receiving JS cannot build this itself. See App\Support\ChatAvatar.
        $message->avatar_url = \App\Support\ChatAvatar::url($user->image ?? null, $user_type);
        $message->gender = $type == 'student' ? ($user->gender ?? null) : null;
        PusherFactory::make()->trigger('chat', 'send', ['data' => $message]);

        return response()->json(['state' => 1, 'data' => $message]);
    }

    ////////////////////////////////////////////////////////////////////////////
    public function getOldMessages(Request $request) {
//        if(!$request->old_message_id || !$request->to_user)
//            return;
//        $message = Message::find($request->old_message_id);
//          $group_id = 1;
//            $lastMessages = Message::where(function($query) use ($request, $message, $group_id) {
//            $query->where('group_id', $group_id)
//                ->where('created_at', '<', $message->created_at);
//        })->orderBy('created_at', 'ASC')->limit(10)->get();
//
//        $return = [];
//
//        if($lastMessages->count() > 0) {
//            foreach ($lastMessages as $message) {
//                $return[] = view('frontend.chat.message-line')->with('message', $message)->render();
//            }
//            PusherFactory::make()->trigger('chat', 'oldMsgs', ['to_user' => $request->to_user, 'data' => $return]);
//        }
//        return response()->json(['state' => 1, 'data' => $return]);
    }

    ////////////////////////////////////////////////////////////////////////////
}
