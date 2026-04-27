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
use App\Models\Questions;

class QuestionsController extends AdminController
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
        parent::$data['active_menu'] = 'questions';
    }
    //////////////////////////////////////////////
    public function getIndex()
    {
        return view('admin.questions.view', parent::$data);
    }
    //////////////////////////////////////////////
    public function getList(Request $request)
    {
        $user = new Questions();
        $title = $request->get('title');
        $info = $user->getSearchQuestions($title);
        $datatable = Datatables::of($info);
        $datatable->editColumn('status', function ($row)
        {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.questions.parts.status', $data)->render();
        });
        $datatable->addColumn('actions', function ($row)
        {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];
            return view('admin.questions.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }
    //////////////////////////////////////////////
    public function getAdd()
    {
        $roles = new Roles();
        parent::$data['roles'] = $roles->getAllRolesActive();

        return view('admin.questions.add', parent::$data);
    }
    //////////////////////////////////////////////
    public function postAdd(Request $request)
    {
        // dd($_POST);
        $name = $request->get('name');
        if($name != null){
                $validator = Validator::make([
                    'name' => $name[0],
                ], [
                    'name' => 'required',
                ]);
                if ($validator->fails()) {
                    $request->session()->flash('danger', $validator->messages());
                    return redirect(route('questions.add'))->withInput();
                } else {
                    foreach ($name as $question) {
                        Questions::create([
                            'name' => $question,
                            'status' => 1,
                        ]);
                    }
                    $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                    return redirect(route('questions.view'));
                }
        }else{
            $request->session()->flash('danger', 'خطا, لايمكن اضافة سؤال فارغ!!');
            return redirect(route('questions.add'));
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
            return redirect(route('questions.view'));
        }
        $obj = new Questions();
        
        $info = $obj->getQuestions($id);
        
        if ($info){
            parent::$data['info'] = $info;
            return view('admin.questions.edit', parent::$data);
        }else{
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('questions.view'));
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
            return redirect(route('questions.view'));
        }
        $obj = new Questions();
        $info = $obj->getQuestions($id);
        if ($info)
        {
            $name = $request->get('name');

                $validator = Validator::make([
                    'name' => $name,
                ], [
                    'name' => 'required',
                ]);
                if ($validator->fails()) {
                    $request->session()->flash('danger', $validator->messages());
                    return redirect(route('questions.view'))->withInput();
                } else {
                   
                   $update = $obj->updateQuestions($obj,$name);
                   if($update ){

                       $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                       return redirect(route('questions.view'));
                   }
                }
            } else {
                $request->session()->flash('danger', 'خطا, لايمكن اضافة سؤال فارغ!!');
                return redirect(route('questions.edit'));
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

        $questions = new Questions();
        $info = $questions->getQuestions($id);
        if ($info)
        {
            $status = $info->status;
            if($status == 0)
            {
                $delete = $questions->updateStatus($id,1);
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
                $delete = $questions->updateStatus($id,0);
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

        $questions = new Questions();
        $info = $questions->getQuestions($id);
        if ($info)
        {
            $delete = $questions->deleteQuestions($info);
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
