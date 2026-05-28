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
use App\Models\Times;

class TimesController extends AdminController {

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
        parent::$data['active_menu'] = 'times';
    }

    //////////////////////////////////////////////
    public function getIndex() {
        return view('admin.times.view', parent::$data);
    }

    //////////////////////////////////////////////
    public function getList(Request $request) {
        $title = $request->get('title', NULL);
        $isPlacement = $request->get('is_placement_test', null);

        $times = new Times();
        $info = $times->getSearchTimes($title, $isPlacement);
        $datatable = Datatables::of($info);

        $datatable->editColumn('is_placement_test', function ($row) {
            return $row->is_placement_test
                ? '<span class="badge badge-light-success fw-bold"><i class="bi bi-clipboard-check me-1"></i>اختبار تحديد المستوى</span>'
                : '<span class="badge badge-light-info fw-bold"><i class="bi bi-mortarboard me-1"></i>عام</span>';
        });

        $datatable->editColumn('name', function ($row) {
            return (!empty($row->name) ? $row->name : 'N/A');
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.times.parts.status', $data)->render();
        });
        $datatable->editColumn('days', function ($row) {
            $days = $row->days;
            if ($days) {
                return '<span class="badge badge-light-warning fw-bold px-4 py-3">' . $days . '</span>';
            } else {
                return '<span class="text-danger"><i class="bi bi-exclamation-circle text-danger me-1"></i>لا يوجد</span>';
            }
        });
        $datatable->editColumn('times', function ($row) {
            $times = $row->times;
            if ($times) {
                return '<span class="badge badge-light-info fw-bold px-4 py-3">' . $times . '</span>';
            } else {
                return '<span class="text-danger"><i class="bi bi-exclamation-circle text-danger me-1"></i>لا يوجد</span>';
            }
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.times.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getAdd() {
        return view('admin.times.add', parent::$data);
    }

    //////////////////////////////////////////////
    public function postAdd(Request $request) {
        $days = $request->get('days');
        $times = $request->get('times');
        $status = (int) $request->get('status');
        $isPlacement = (bool) $request->get('is_placement_test', false);

        $validator = Validator::make([
                    'days' => $days,
                    'times' => $times,
                    'status' => $status
                        ], [
                    'days' => 'required',
                    'times' => 'required',
                    'status' => 'required|numeric|in:0,1'
        ]);
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('times.add'))->withInput();
        } else {
            $time = new Times();
            $add = $time->addTime($days, $times, $status, $isPlacement);
            if ($add) {
                $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route('times.view'));
            } else {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('times.add'))->withInput();
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
        $times = new Times();
        $info = $times->getTime($id);
        if ($info) {
            parent::$data['info'] = $info;
            return view('admin.times.edit', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('times.view'));
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
        $time = new Times();
        $info = $time->getTime($id);
        if ($info) {
            $days = $request->get('days');
            $times = $request->get('times');
            $status = (int) $request->get('status');
            $isPlacement = (bool) $request->get('is_placement_test', false);

            $validator = Validator::make([
                        'days' => $days,
                        'times' => $times,
                        'status' => $status
                            ], [
                        'days' => 'required',
                        'times' => 'required',
                        'status' => 'required|numeric|in:0,1'
            ]);
            //////////////////////////////////////////////////////////
            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('times.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                $update = $time->updateTime($info, $days, $times, $status, $isPlacement);
                if ($update) {
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('times.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('times.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('times.view'));
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
        $times = new Times();
        $info = $times->getTime($id);
        if ($info) {
            $delete = $times->deleteTime($info);
            if ($delete) {
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            } else {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

    //////////////////////////////////////////////
    public function postStatus(Request $request) {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $times = new Times();
        $info = $times->getTime($id);
        if ($info) {
            $status = $info->status;
            if ($status == 0) {
                $update = $times->updateStatus($id, 1);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $times->updateStatus($id, 0);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

}
