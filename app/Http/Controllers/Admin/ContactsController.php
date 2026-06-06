<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Hash;
use Crypt;
use Session;
use Validator;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Contacts;
//////////////////////////////////
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Encryption\DecryptException;

class ContactsController extends AdminController
{
    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بتجاح";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const PASSWORD_SUCCESS = "نجاح، تم تغيير كلمة المرور بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً،لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";
    //////////////////////////////////////////////
    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'contacts';
    }

    //////////////////////////////////////////
    public function getIndex()
    {
        return view('admin.contacts.view', parent::$data);
    }
    ////////////////////////////////////////////////////
    public function getReply(Request $request, $id)
    {
        try
        {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('contacts.view'));
        }
        /////////////////////////////
        $contacts = new Contacts();
        $info = $contacts->getContacts($id);
        if ($info)
        {
            parent::$data['info'] = $info;
            return view('admin.contacts.reply', parent::$data);
        }
        else
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('contacts.view'));
        }
    }
    ////////////////////////////////////////////////////
    public function postReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'      => 'required',
            'subject' => 'required',
            'body'    => 'required',
        ], [
            'subject.required' => 'يرجى إدخال موضوع الرسالة',
            'body.required'    => 'يرجى إدخال نص الرد',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->errors()->all());
            return redirect()->back()->withInput();
        }

        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('contacts.view'));
        }

        $contact = new Contacts();
        $info = $contact->getContacts($id);

        if (!$info) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('contacts.view'));
        }

        if (empty($info->email)) {
            $request->session()->flash('danger', 'لا يوجد بريد إلكتروني لهذا المرسل');
            return redirect()->back()->withInput();
        }

        $sent = $this->sendReplyMail($info, $request->get('subject'), $request->get('body'));

        if ($sent) {
            // Mark as contacted.
            $contact->updateStatus($id, 1);
            // Broadcast counter update so sidebar/bell sync instantly.
            try {
                broadcast(new \App\Events\CountersUpdated());
            } catch (\Throwable $broadcastEx) {
                \Illuminate\Support\Facades\Log::error('Broadcast CountersUpdated failed in ContactsController@postReply: ' . $broadcastEx->getMessage());
            }
            $request->session()->flash('success', 'تم إرسال الرد عبر البريد الإلكتروني بنجاح');
            return redirect(route('contacts.view'));
        }

        $request->session()->flash('danger', 'تعذر إرسال البريد الإلكتروني، يرجى المحاولة لاحقاً');
        return redirect()->back()->withInput();
    }

    /**
     * Send a reply email to the contact's address using the configured (.env) SMTP credentials.
     * Never throws: returns false and logs on failure so the admin flow degrades gracefully.
     */
    private function sendReplyMail($info, $subject, $body)
    {
        $data = [
            'contactName'     => $info->name,
            'originalSubject' => $info->subject,
            'originalMessage' => $info->message,
            'replyBody'       => $body,
            'mysettings'      => parent::$data['mysettings'],
            'social'          => parent::$data['social'],
        ];

        $fromAddress = config('mail.from.address') ?: 'campany@oxford.ps';
        $fromName    = config('mail.from.name') ?: 'Oxford English Centre';

        try {
            \Illuminate\Support\Facades\Mail::send('emails.contact_reply', $data, function ($message) use ($info, $subject, $fromAddress, $fromName) {
                $message->to($info->email, $info->name)
                        ->subject($subject)
                        ->from($fromAddress, $fromName);
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Contact reply mail failed: ' . $e->getMessage());
            return false;
        }

        return true;
    }
    ////////////////////////////////////////////////////
    public function getList(Request $request)
    {
        $contact = new Contacts();
        $name = $request->get('name');
        $email = $request->get('email');
        $mobile = $request->get('mobile');

        $info = $contact->getAllContactUsForAdmin($name,$email,$mobile);
        $datatable = Datatables::of($info);

        $datatable->addColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.contacts.parts.status', $data)->render();
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['mobile'] = $row->mobile;
            $data['email'] = $row->email;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.contacts.parts.actions', $data)->render();
        });

        $datatable->escapeColumns(['*']);

        return $datatable->make(true);
    }
    ////////////////////////////////////////////////
    public function postDelete(Request $request)
    {
        $id = $request->get('id');
        try
        {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $contact = new Contacts();
        $info = $contact->getContacts($id);
        if ($info)
        {
            $delete = $contact->deleteContactUs($info);
            if ($delete)
            {
                try {
                    broadcast(new \App\Events\CountersUpdated());
                } catch (\Throwable $broadcastEx) {
                    \Illuminate\Support\Facades\Log::error('Broadcast CountersUpdated failed in ContactsController@postDelete: ' . $broadcastEx->getMessage());
                }
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            }
            else
            {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        }
        else
        {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }
    ////////////////////////////////////////////////
    public function postStatus(Request $request)
    {
        $id = $request->get('id');
        try
        {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        ////////////////////////////////
        $contact = new Contacts();
        $info = $contact->getContacts($id);
        if ($info)
        {
            $status = $info->status;
            if($status == 0)
            {
                $delete = $contact->updateStatus($id,1);
                if($delete)
                {
                    try {
                        broadcast(new \App\Events\CountersUpdated());
                    } catch (\Throwable $broadcastEx) {
                        \Illuminate\Support\Facades\Log::error('Broadcast CountersUpdated failed in ContactsController@postStatus: ' . $broadcastEx->getMessage());
                    }
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                }
                else
                {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
            else
            {
                $delete = $contact->updateStatus($id,0);
                if($delete)
                {
                    try {
                        broadcast(new \App\Events\CountersUpdated());
                    } catch (\Throwable $broadcastEx) {
                        \Illuminate\Support\Facades\Log::error('Broadcast CountersUpdated failed in ContactsController@postStatus: ' . $broadcastEx->getMessage());
                    }
                    return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
                }
                else
                {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
        }
        else
        {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }
}
