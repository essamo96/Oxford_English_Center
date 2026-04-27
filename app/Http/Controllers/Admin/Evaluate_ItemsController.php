<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;

use App\Models\Roles;
use App\Models\Evaluate_Items;

class Evaluate_ItemsController extends AdminController
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
        parent::$data['active_menu'] = 'evaluate_items';
    }
    //////////////////////////////////////////////
    public function getIndex()
    {
        return view('admin.evaluate_items.view', parent::$data);
    }
    //////////////////////////////////////////////
    public function getList(Request $request)
    {
        $user = new Evaluate_Items();
        $title = $request->get('title');
        $info = $user->getSearchEvaluate_Items($title);
        $datatable = Datatables::of($info);
        $datatable->editColumn('status', function ($row)
        {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.evaluate_items.parts.status', $data)->render();
        });
        $datatable->addColumn('actions', function ($row)
        {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];
            return view('admin.evaluate_items.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }
    //////////////////////////////////////////////
    public function getAdd()
    {
        $roles = new Roles();
        parent::$data['roles'] = $roles->getAllRolesActive();

        return view('admin.evaluate_items.add', parent::$data);
    }
    //////////////////////////////////////////////
    public function postAdd(Request $request)
    {
        // dd($_POST);
        $name_en = $request->get('name_en');
        if($name_en != null){
                $validator = Validator::make([
                    'name_en' => $name_en[0],
                ], [
                    'name_en' => 'required',
                ]);
                if ($validator->fails()) {
                    $request->session()->flash('danger', $validator->messages());
                    return redirect(route('evaluate_items.add'))->withInput();
                } else {
                    foreach ($name_en as $question) {
                        Evaluate_Items::create([
                            'name_en' => $question,
                            'status' => 1,
                        ]);
                    }
                    $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                    return redirect(route('evaluate_items.view'));
                }
        }else{
            $request->session()->flash('danger', 'خطا, لايمكن اضافة سؤال فارغ!!');
            return redirect(route('evaluate_items.add'));
        }
    }
    //////////////////////////////////////////////
    public function getEdit(Request $request, $id)
    {
        try{
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('evaluate_items.view'));
        }
        $obj = new Evaluate_Items();
        
        $info = $obj->getEvaluate_Items($id);
        
        if ($info){
            parent::$data['info'] = $info;
            return view('admin.evaluate_items.edit', parent::$data);
        }else{
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('evaluate_items.view'));
        }
    }
    //////////////////////////////////////////////
    public function postEdit(Request $request, $id)
    {
        try
        {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('evaluate_items.view'));
        }
        $obj = new Evaluate_Items();
        $info = $obj->getEvaluate_Items($id);
        if ($info)
        {
            $name_en = $request->get('name_en');

                $validator = Validator::make([
                    'name_en' => $name_en,
                ], [
                    'name_en' => 'required',
                ]);
                if ($validator->fails()) {
                    $request->session()->flash('danger', $validator->messages());
                    return redirect(route('evaluate_items.view'))->withInput();
                } else {
                   
                   $update = $obj->updateEvaluate_Items($obj,$name_en);
                   if($update ){

                       $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                       return redirect(route('evaluate_items.view'));
                   }
                }
            } else {
                $request->session()->flash('danger', 'خطا, لايمكن اضافة سؤال فارغ!!');
                return redirect(route('evaluate_items.edit'));
            }
    }
    //////////////////////////////////////////////
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
        if ($id == 1)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return response()->json(['status' => 'error', 'message' => 'Error, Data not found']);
        }

        $evaluate_items = new Evaluate_Items();
        $info = $evaluate_items->getEvaluate_Items($id);
        if ($info)
        {
            $status = $info->status;
            if($status == 0)
            {
                $delete = $evaluate_items->updateStatus($id,1);
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
                $delete = $evaluate_items->updateStatus($id,0);
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
    //////////////////////////////////////////////
    public function getPassword(Request $request, $id)
    {
        try
        {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('evaluate_items.view'));
        }
        if ($id == 1)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('evaluate_items.view'));
        }
        /////////////////////////////
        $user = new Evaluate_Items();
        $info = $user->getEvaluate_Items($id);
        if ($info)
        {
            parent::$data['info'] = $info;
            return view('admin.evaluate_items.password', parent::$data);
        }
        else
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('evaluate_items.view'));
        }
    }
    //////////////////////////////////////////////
    public function postPassword(Request $request, $id)
    {
        try
        {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('evaluate_items.view'));
        }
        if ($id == 1)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('evaluate_items.view'));
        }

        $user = new Evaluate_Items();
        $info = $user->getEvaluate_Items($id);
        if ($info)
        {
            $password = $request->get('password');
            $password_confirmation = $request->get('password_confirmation');

            $validator = Validator::make([
                'password' => $password,
                'password_confirmation' => $password_confirmation
            ], [
                'password' => 'required|between:6,16|confirmed',
                'password_confirmation' => 'required|between:6,16'
            ]);

            if ($validator->fails())
            {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('evaluate_items.password', ['id' => $encrypted_id]))->withInput();
            }
            else
            {
                $update = $user->updatePassword($id, Hash::make($password));
                if ($update)
                {
                    $request->session()->flash('success', self::PASSWORD_SUCCESS);
                    return redirect(route('evaluate_items.view'));
                }
                else
                {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('evaluate_items.password', ['id' => $encrypted_id]))->withInput();
                }
            }
        }
        else
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('evaluate_items.view'));
        }
    }
    //////////////////////////////////////////////
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
        if ($id == 1)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return response()->json(['status' => 'error', 'message' => 'Error, Data not found']);
        }

        $evaluate_items = new Evaluate_Items();
        $info = $evaluate_items->getEvaluate_Items($id);
        if ($info)
        {
            $delete = $evaluate_items->deleteEvaluate_Items($info);
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
}
