<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;
////////////////////////////////////
use App\Models\Fees;
use App\Models\GroupStudents;

class FeesController extends AdminController {

    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بتجاح";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const PASSWORD_SUCCESS = "نجاح، تم تغيير كلمة المرور بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً،لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";

    //////////////////////////////////////////////
    public function __construct() {
        parent::__construct();
        parent::$data['active_menu'] = 'fees';
    }

    //////////////////////////////////////////////
    public function getIndex() {
        return view('admin.fees.view', parent::$data);
    }

    //////////////////////////////////////////////
    public function getList(Request $request) {
        $title = $request->get('title', NULL);
        $group_id = $request->get('group_id', NULL);

        $groups = new Fees();
        $info = $groups->getSearchFees($title, $group_id);
        $datatable = Datatables::of($info);

        $datatable->editColumn('name', function ($row) {
            return (!empty($row->student_name) ? $row->student_name : 'N/A');
        });
        $datatable->editColumn('group_name', function ($row) {
            return (!empty($row->group_name) ? $row->group_name : 'N/A');
        });
        $datatable->editColumn('fee_type', function ($row) {
            return $row->student_paid_type == 0 ? 'رسوم دورة' : 'رسوم كتب';
        });
        $datatable->editColumn('created_at', function ($row) {
            return date('Y-m-d', strtotime($row->created_at));
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.fees.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getAdd() {
        return view('admin.fees.add', parent::$data);
    }

    ////////////////////////////////////////////
    public function postAdd(Request $request) {
        $student_id = $request->get('student_id');
        $group_id = $request->get('group_id');
        $student_paid_type = $request->get('student_paid_type');
        $student_fee_paid = $request->get('student_fee_paid');

        $validator = Validator::make([
                    'student_id' => $student_id,
                    'group_id' => $group_id,
                    'student_fee_paid' => $student_fee_paid,
                        ], [
                    'student_id' => 'required',
                    'group_id' => 'required',
                    'student_fee_paid' => 'required|numeric'
        ]);
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('fees.add'))->withInput();
        } else {
            $fees = new Fees();
            $add = $fees->addGroupStudentFees($student_id, $student_fee_paid, $student_paid_type, $group_id);
            if ($add) {
                $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route('fees.view'));
            } else {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('fees.add'))->withInput();
            }
        }
    }

    //////////////////////////////////////////////
    public function getEdit(Request $request, $id) {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('news.view'));
        }
        //////////////////////////////////////////////
        $fees = new Fees();
        $info = $fees->getFees($id);
        if ($info) {
            parent::$data['info'] = $info;
            return view('admin.fees.edit', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('fees.view'));
        }
    }

    ////////////////////////////////////////////////
    public function postEdit(Request $request, $id) {
        try {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('pages.view'));
        }
        /////////////////////////////
        $fees = new Fees();
        $info = $fees->getFees($id);
        if ($info) {
            $student_fee_paid = $request->get('student_fee_paid');
            $student_paid_type = (int)$request->get('student_paid_type');
            
            $validator = Validator::make([
                        'student_fee_paid' => $student_fee_paid,
                            ], [
                        'student_fee_paid' => 'required|numeric',
            ]);
            //////////////////////////////////////////////////////////
            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('fees.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                $update = $fees->updateStudentFees($info, $student_fee_paid, $student_paid_type);
                if ($update) {
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('fees.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('fees.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('fees.view'));
        }
    }

    ////////////////////////////////////////////////
    public function postDelete(Request $request) {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $fees = new Fees();
        $info = $fees->getStudentFees($id);
        if ($info) {
            $delete = $fees->deleteStudentFees($info);
            if ($delete) {
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            } else {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

}
