<?php

namespace App\Http\Controllers\Admin;

use App\Models\Closed_Classes;
use App\Models\Groups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Programs;
use App\Models\Teachers;

class Progress_MenuController extends AdminController
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
        parent::$data['active_menu'] = 'progress_menu';
    }
    //////////////////////////////////////////
    public function getIndex()
    {
        Groups::where('seen_progress', 0)->update(['seen_progress' => 1]);
        parent::$data['programs'] = Programs::all();
        parent::$data['groups'] = Groups::whereNull('deleted_at')->get();
        return view('admin.progress_menu.view', parent::$data);
    }
    ////////////////////////////////////////////////////
    public function postDelete(Request $request)
    {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        $info = Groups::find($id);
        if ($info) {
            $info->progress = null;
            $info->progress_at = null;
            $info->seen_progress = 0;
            $info->save();
            return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }
    /////////////////////////////////////////
    public function getList(Request $request)
    {
        $teacher_name = $request->get('teacher_name', NULL);
        $program_id = $request->get('program_id', NULL);
        $group_name = $request->get('group_name', NULL);
        // dd($group_name) ;
        $groups = new Groups();
        $info = $groups->Progress_list($teacher_name, $program_id, $group_name);
        $datatable = Datatables::of($info);

        $datatable->editColumn('name', function ($row) {
            return '<div class="d-flex align-items-center justify-content-center" style="height: 48px;">
            <a href="javascript:;" onclick="showGroupModal(' . $row->id . ')" class="text-gray-900 text-hover-primary fw-bold fs-6">' . $row->name . '</a>
            </div>';
        });

        $datatable->editColumn('teacher_name', function ($row) {
            if (!$row->teacher) return 'N/A';
            return '<a href="javascript:;" onclick="showTeacherModal(' . $row->teacher_id . ')" class="text-gray-800 text-hover-primary fw-bold">' . $row->teacher->name . '</a>';
        });

        $datatable->editColumn('program_name', function ($row) {
            if (!$row->program) return 'N/A';
            return '<a href="javascript:;" onclick="showProgramModal(' . $row->program_id . ')" class="text-gray-800 text-hover-primary fw-bold">' . $row->program->title . '</a>';
        });

        $datatable->editColumn('progress_at', function ($row) {
            return ($row->progress_at ? $row->progress_at : 'N/A');
        });

        $datatable->editColumn('progress', function ($row) {
            $data['progress'] = $row->progress;
            return view('admin.progress_menu.parts.actions', $data)->render();
        });

        $datatable->addColumn('actions', function ($row) {
            return '<button type="button" onclick="deleteProgress(\'' . Crypt::encrypt($row->id) . '\')" class="btn btn-icon btn-light-danger w-30px h-30px" title="حذف التقدم">
                        <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                    </button>';
        });

        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }
}
