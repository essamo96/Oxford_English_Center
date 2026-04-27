<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Hash;
use Crypt;
use Session;
use Validator;
use Illuminate\Http\Request;
//////////////////////////////////
use App\Models\GroupStudents;
use App\Models\Students;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Encryption\DecryptException;

class Students_ReportController extends AdminController {

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
        parent::$data['active_menu'] = 'students_report';
    }

    //////////////////////////////////////////
    public function getIndex() {
        return view('admin.students_report.view', parent::$data);
    }

    ////////////////////////////////////////////////////
    public function getList(Request $request) {
        $mark = new GroupStudents();

        $mobile = $request->get('info') ?? null;
        // $name = $request->get('info') ?? null;
        //get student id where name or mobile like $mobile or $name
        $currentid = Students::where('name', 'like', '%' . $mobile . '%')->orWhere('mobile', 'like', '%' . $mobile . '%')->first();
        // dd($currentid);
        if ($currentid) {
            $info = $mark->getAllStudentGroupMark($currentid->id);
        } else {
            $info = $mark->getAllStudentGroupMark(); // Pass null as the argument
        }

        $datatable = Datatables::of($info);

        $datatable->editColumn('student_id', function ($row) {
            return !empty($row->student) 
                ? '<div class="d-flex align-items-center">
                    <div class="d-flex flex-column">
                        <a href="'.route('students.edit', \Crypt::encrypt($row->student->id)).'" class="text-gray-800 text-hover-primary mb-1 fw-bold">'.$row->student->name.'</a>
                        <span class="text-muted fs-7">'.$row->student->mobile.'</span>
                    </div>
                </div>'
                : '<span class="badge badge-light-danger"><i class="ki-duotone ki-information fs-5 me-1 text-danger"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> N/A</span>';
        });

        $datatable->editColumn('group_id', function ($row) {
            return !empty($row->group) 
                ? '<span class="badge badge-light-warning fw-bold text-gray-700 fs-7">'.$row->group->name.'</span>'
                : '<span class="badge badge-light-danger">Unknown</span>';
        });

        $datatable->editColumn('exam1_degree', function ($row) {
            return $row->exam1_degree !== null ? '<span class="fw-bold text-gray-700">'.$row->exam1_degree.'</span>' : '<span class="text-gray-300 fs-8">-</span>';
        });
        $datatable->editColumn('exam2_degree', function ($row) {
            return $row->exam2_degree !== null ? '<span class="fw-bold text-gray-700">'.$row->exam2_degree.'</span>' : '<span class="text-gray-300 fs-8">-</span>';
        });
        $datatable->editColumn('exam3_degree', function ($row) {
            return $row->exam3_degree !== null ? '<span class="fw-bold text-gray-700">'.$row->exam3_degree.'</span>' : '<span class="text-gray-300 fs-8">-</span>';
        });
        $datatable->editColumn('exam4_degree', function ($row) {
            return $row->exam4_degree !== null ? '<span class="fw-bold text-gray-800">'.$row->exam4_degree.'</span>' : '<span class="text-gray-300 fs-8">-</span>';
        });
        $datatable->editColumn('activity_degree', function ($row) {
            return $row->activity_degree !== null ? '<span class="text-gray-600">'.$row->activity_degree.'</span>' : '<span class="text-gray-300 fs-8">-</span>';
        });
        $datatable->editColumn('workbook_degree', function ($row) {
            return $row->workbook_degree !== null ? '<span class="text-gray-600">'.$row->workbook_degree.'</span>' : '<span class="text-gray-300 fs-8">-</span>';
        });
        $datatable->editColumn('total_degree', function ($row) {
            if ($row->total_degree === null) return '<span class="text-gray-300 fs-8">-</span>';
            $class = $row->total_degree >= 50 ? 'badge-light-success text-success' : 'badge-light-danger text-danger';
            return '<span class="badge '.$class.' fw-bolder px-4 py-2">'.$row->total_degree.'%</span>';
        });
      

        // $datatable->addColumn('actions', function ($row) {
        //     $data['id'] = $row->id;
        //     $data['btn_class'] = parent::$data['btn_class'];

        //     return view('admin.students_report.parts.actions', $data)->render();
        // });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function topStuudentInfos(Request $request) {
        $name = $request->input('info');
        if (empty($name)) return '';

        $info = Students::where('name', 'like', '%' . $name . '%')
                        ->orWhere('mobile', 'like', '%' . $name . '%')
                        ->first();
        
        if (!$info) return '<div class="text-center text-muted p-10">No matching student found.</div>';

        $data['info'] = $info;
        return view('admin.students_report.info', $data)->render();
    }

}
