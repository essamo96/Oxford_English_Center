<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\DataTables;
use App\Models\ExamSkill;

class ExamSkillsController extends AdminController
{
    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بنجاح";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً، لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";

    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'exam_skills';
    }

    public function getIndex()
    {
        return view('admin.exam_skills.view', parent::$data);
    }

    public function getList(Request $request)
    {
        $title = $request->get('title', null);

        $info = ExamSkill::when($title, fn($q) => $q->where('name_ar', 'LIKE', "%{$title}%")->orWhere('name_en', 'LIKE', "%{$title}%"))
            ->orderBy('id', 'desc');

        $datatable = Datatables::of($info);

        $datatable->editColumn('status', function ($row) {
            return view('admin.exam_skills.parts.status', ['id' => $row->id, 'status' => $row->status])->render();
        });

        $datatable->addColumn('actions', function ($row) {
            return view('admin.exam_skills.parts.actions', ['id' => $row->id])->render();
        });

        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    public function getAdd()
    {
        return view('admin.exam_skills.add', parent::$data);
    }

    public function postAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:191',
            'name_en' => 'required|string|max:191',
            'slug' => 'required|string|max:191|unique:exam_skills,slug',
            'status' => 'required|numeric|in:0,1',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('exam_skills.add'))->withInput();
        }

        ExamSkill::create($request->only(['name_ar', 'name_en', 'slug', 'status']));

        $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
        return redirect(route('exam_skills.view'));
    }

    public function getEdit(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('exam_skills.view'));
        }

        $info = ExamSkill::find($id);
        if (!$info) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('exam_skills.view'));
        }

        parent::$data['info'] = $info;
        return view('admin.exam_skills.edit', parent::$data);
    }

    public function postEdit(Request $request, $id)
    {
        $encrypted_id = $id;
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('exam_skills.view'));
        }

        $info = ExamSkill::find($id);
        if (!$info) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('exam_skills.view'));
        }

        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:191',
            'name_en' => 'required|string|max:191',
            'slug' => 'required|string|max:191|unique:exam_skills,slug,' . $id,
            'status' => 'required|numeric|in:0,1',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('exam_skills.edit', ['id' => $encrypted_id]))->withInput();
        }

        $info->update($request->only(['name_ar', 'name_en', 'slug', 'status']));

        $request->session()->flash('success', self::UPDATE_SUCCESS);
        return redirect(route('exam_skills.view'));
    }

    public function postDelete(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $info = ExamSkill::find($id);
        if (!$info) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $info->delete();
        return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
    }

    public function postStatus(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $info = ExamSkill::find($id);
        if (!$info) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $newStatus = $info->status == 1 ? 0 : 1;
        $info->update(['status' => $newStatus]);

        if ($newStatus == 1) {
            return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
        }
        return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
    }
}
