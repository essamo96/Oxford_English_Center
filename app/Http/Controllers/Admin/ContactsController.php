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
        $info = $contact->getContactUs($id);
        if ($info)
        {
            $delete = $contact->deleteContactUs($info);
            if ($delete)
            {
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
