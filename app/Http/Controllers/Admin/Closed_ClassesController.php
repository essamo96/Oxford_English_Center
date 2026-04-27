<?php

namespace App\Http\Controllers\Admin;

use App\Models\Closed_Classes;
use App\Models\Teachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
class Closed_ClassesController extends AdminController
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
        parent::$data['active_menu'] = 'closed_classes';
    }
    //////////////////////////////////////////
    public function getIndex()
    {
        Closed_Classes::where('seen', 0)->update(['seen' => 1]);
        parent::$data['teachers'] = Teachers::where('status', 1)->get();
        return view('admin.closed_classes.view', parent::$data);
    }
    ////////////////////////////////////////////////////
    public function getList(Request $request)
    {
        $contact = new Closed_Classes();
        $name = $request->get('name');
        $teacher_id = $request->get('teacher_id');
        $closed_date = $request->get('closed_date');

        $info = $contact->getAllClosed_Classes($name, $teacher_id, $closed_date);
        $datatable = Datatables::of($info);

        $datatable->addColumn('teacher_name', function ($row) {
            $name = $row->Teacher->name ?? '';
            if (!$name) {
                $name = $row->Teacher->username ?? ($row->Teacher->email ?? 'مدرس #' . ($row->teacher_id ?? '??'));
            }
            return $name;
        });

        $datatable->addColumn('group_name', function ($row) {
            return $row->Groups->name ?? 'مجموعة #' . ($row->group_id ?? '??');
        });


        $datatable->addColumn('closed_date', function ($row) {
            $closedDate = Carbon::parse($row->closed_date)->format('d / F / Y - H:i');
            return $closedDate;
        });


        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.closed_classes.parts.actions', $data)->render();
        });

        $datatable->escapeColumns(['*']);

        return $datatable->make(true);
    }
    ////////////////////////////////////////////////
    public function postDelete(Request $request)
    {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $obj = new Closed_Classes();
        $info = $obj->getClosed_Classes($id);
        if ($info) {
            $delete = $obj->deleteClosed_Classes($info);
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
