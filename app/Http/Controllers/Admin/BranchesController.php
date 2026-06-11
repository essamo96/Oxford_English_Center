<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;

class BranchesController extends AdminController
{
    const INSERT_SUCCESS_MESSAGE = "نجاح، تم إضافة الفرع بنجاح";
    const UPDATE_SUCCESS         = "نجاح، تم تعديل الفرع بنجاح";
    const DELETE_SUCCESS         = "نجاح، تم حذف الفرع بنجاح";
    const EXECUTION_ERROR        = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND              = "عذراً، لا يمكن العثور على الفرع";
    const ACTIVATION_SUCCESS     = "نجاح، تم تفعيل الفرع بنجاح";
    const DISABLE_SUCCESS        = "نجاح، تم تعطيل الفرع بنجاح";

    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'branches';
    }

    public function getIndex()
    {
        return view('admin.branches.view', parent::$data);
    }

    public function getList(Request $request)
    {
        $name   = $request->get('name', null);
        $status = $request->get('status', null);

        $branch = new Branch();
        $data   = $branch->getSearch($name, $status);

        return DataTables::of($data)
            ->addColumn('status', function ($row) {
                return view('admin.branches.parts.status', [
                    'id'     => $row->id,
                    'status' => $row->status,
                ])->render();
            })
            ->addColumn('actions', function ($row) {
                return view('admin.branches.parts.actions', [
                    'id' => $row->id,
                ])->render();
            })
            ->addColumn('students_count', function ($row) {
                return '<span class="badge badge-light-info fs-7">' . ($row->students_count ?? 0) . '</span>';
            })
            ->rawColumns(['status', 'actions', 'students_count'])
            ->make(true);
    }

    public function getAdd()
    {
        return view('admin.branches.add', parent::$data);
    }

    public function postAdd(Request $request)
    {
        $name_ar = trim($request->get('name_ar'));
        $name_en = trim($request->get('name_en'));
        $status  = (int) $request->boolean('status');

        $validator = Validator::make(
            ['name_ar' => $name_ar, 'name_en' => $name_en],
            ['name_ar' => 'required|string|max:191', 'name_en' => 'required|string|max:191'],
            ['name_ar.required' => 'اسم الفرع بالعربية مطلوب', 'name_en.required' => 'اسم الفرع بالإنجليزية مطلوب']
        );

        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('branches.add'))->withInput();
        }

        $branch = new Branch();
        $add    = $branch->addBranch($name_ar, $name_en, $status);

        if ($add) {
            $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
            return redirect(route('branches.view'));
        }

        $request->session()->flash('danger', self::EXECUTION_ERROR);
        return redirect(route('branches.add'))->withInput();
    }

    public function getEdit(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('branches.view'));
        }

        $branch = (new Branch())->getBranch($id);
        if (!$branch) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('branches.view'));
        }

        parent::$data['info'] = $branch;
        return view('admin.branches.edit', parent::$data);
    }

    public function postEdit(Request $request, $id)
    {
        try {
            $encrypted_id = $id;
            $id           = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('branches.view'));
        }

        $branch = (new Branch())->getBranch($id);
        if (!$branch) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('branches.view'));
        }

        $name_ar = trim($request->get('name_ar'));
        $name_en = trim($request->get('name_en'));
        $status  = (int) $request->boolean('status');

        $validator = Validator::make(
            ['name_ar' => $name_ar, 'name_en' => $name_en],
            ['name_ar' => 'required|string|max:191', 'name_en' => 'required|string|max:191'],
            ['name_ar.required' => 'اسم الفرع بالعربية مطلوب', 'name_en.required' => 'اسم الفرع بالإنجليزية مطلوب']
        );

        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('branches.edit', ['id' => $encrypted_id]))->withInput();
        }

        $update = (new Branch())->updateBranch($branch, $name_ar, $name_en, $status);
        if ($update) {
            $request->session()->flash('success', self::UPDATE_SUCCESS);
            return redirect(route('branches.view'));
        }

        $request->session()->flash('danger', self::EXECUTION_ERROR);
        return redirect(route('branches.edit', ['id' => $encrypted_id]))->withInput();
    }

    public function postDelete(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $branch = (new Branch())->getBranch($id);
        if (!$branch) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        if ((new Branch())->deleteBranch($branch)) {
            return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
        }

        return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
    }

    public function postStatus(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $branch = (new Branch())->getBranch($id);
        if (!$branch) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $newStatus = $branch->status == 1 ? 0 : 1;
        (new Branch())->updateStatus($id, $newStatus);

        $msg = $newStatus == 1 ? self::ACTIVATION_SUCCESS : self::DISABLE_SUCCESS;
        return response()->json(['status' => 'success', 'message' => $msg]);
    }
}
