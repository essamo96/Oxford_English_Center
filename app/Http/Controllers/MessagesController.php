<?php

namespace App\Http\Controllers;

use App\Lib\PusherFactory;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\GroupStudents;
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

    public function __construct() {
        parent::__construct();
    }

    ////////////////////////////////////////////////////////////////////////////
    public function getLoadLatestMessages(Request $request, $type) {
        if (!$request->group_id) {
            return;
        }
        $group_id = $request->group_id;
        $messages = new Message();
        $messages = $messages->getLastMessages($group_id, $type);
        $reversed = $messages->reverse();
        $return = [];

        foreach ($reversed as $message) {

            $return[] = view('frontend.chat.message-line')->with('message', $message)->render();
        }
        return response()->json(['state' => 1, 'messages' => $return]);
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
