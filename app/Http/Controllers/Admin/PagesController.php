<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Hash;
use Crypt;
use Session;
use Validator;
use Illuminate\Http\Request;
//////////////////////////////////
use App\Models\Pages;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Encryption\DecryptException;

class PagesController extends AdminController {

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
        parent::$data['active_menu'] = 'pages';
    }

    //////////////////////////////////////////
    public function getIndex() {
        return view('admin.pages.view', parent::$data);
    }

    ////////////////////////////////////////////////////
    public function getList(Request $request) {
        $page = new Pages();

        $length = $request->get('length');
        $start = $request->get('start');
        $title = $request->get('title');

        $info = $page->getPages($title);

        $datatable = Datatables::of($info);

        $datatable->editColumn('title', function ($row) {
            return (!empty($row->title) ? $row->title : 'N/A');
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.pages.parts.status', $data)->render();
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.pages.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    ////////////////////////////////////////////////
    public function getEdit(Request $request, $id) {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('pages.view'));
        }
        /////////////////////////////
        $pages = new Pages();
        $info = $pages->getPage($id);
        if ($info) {
            parent::$data['info'] = $info;
            return view('admin.pages.edit', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('pages.view'));
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
        $pages = new Pages();
        $info = $pages->getPage($id);
        if ($info) {
            $db_img = $info->image;

            $title = $request->get('title');
            $details = $request->get('details');
            $image = $request->file('image');
            $url = $request->get('url');
            $tags = $request->get('tags');
            $banner = $request->get('banner');
            $status = (int) $request->get('status');

            // ///
            $age = $request->get('age');
            $level = $request->get('level');
            $weeks = $request->get('weeks');
            $hours = $request->get('hours');
            $mock = $request->get('mock');
            $duration = $request->get('duration');
            $class_size = $request->get('class_size');
            $price = $request->get('price');
            $fees = $request->get('fees');
            $start = $request->get('start');
            $days = $request->get('days');
            $time = $request->get('time');

            $validator = Validator::make([
                        'title' => $title,
                        'details' => $details,
                        'image' => $image,
                        'tags' => $tags,
                        'status' => $status,
                            ], [
                        'title' => 'required',
                        'details' => 'required',
                        'image' => 'nullable|image',
                        'tags' => 'required',
                        'status' => 'required|numeric|in:0,1'
            ]);
            //////////////////////////////////////////////////////////
            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('pages.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                if ($request->hasFile('image') && $image->isValid()) {
                    $destinationPath = 'uploads/image/';
                    $images = 'image_' . strtotime(date("Y-m-d H:i:s")) . '.' . $image->getClientOriginalExtension();
                    $image->move($destinationPath, $images);

                    @unlink(@'uploads/image/' . $db_img);
                } else {
                    $images = $db_img;
                }

                $update = $pages->updatePage($info, $title, $details, $images, $banner, $tags, $url, $status, $age, $level, $weeks, $hours, $mock, $duration, $class_size, $fees, $price, $start, $days, $time);
                if ($update) {
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('pages.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('pages.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('pages.view'));
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

        $pages = new Pages();
        $info = $pages->getPage($id);
        if ($info) {
            $status = $info->status;
            if ($status == 0) {
                $delete = $pages->updateStatus($id, 1);
                if ($delete) {
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $delete = $pages->updateStatus($id, 0);
                if ($delete) {
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
