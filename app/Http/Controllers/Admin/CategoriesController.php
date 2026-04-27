<?php

namespace App\Http\Controllers\Admin;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use DataTables;

class CategoriesController extends AdminController {

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
        parent::$data['active_menu'] = 'categories';
    }

    //////////////////////////////////////////////
    public function getIndex() {
        return view('admin.categories.view', parent::$data);
    }

    //////////////////////////////////////////////
    public function getList(Request $request) {
        $user = new Categories();

        $name = $request->get('name');

        $info = $user->getSearchCategories($name);

        $datatable = Datatables::of($info);

        $datatable->editColumn('name', function ($row) {
            return (!empty($row->name) ? $row->name : 'N/A');
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.categories.parts.status', $data)->render();
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.categories.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getAdd() {
        $categories = new Categories();
        parent::$data['categories'] = $categories->getAllActiveCategories();

        return view('admin.categories.add', parent::$data);
    }

    //////////////////////////////////////////////
    public function postAdd(Request $request) {
        $name = $request->get('name');
        $sort = $request->get('sort');
        $tags = $request->get('tags');
        $color = $request->get('color');
        $category_id = $request->get('category_id');
        $status = (int) $request->get('status');
        $in_menu = (int) $request->get('in_menu');

        $validator = Validator::make([
                    'name' => $name,
                    'sort' => $sort,
                    'tags' => $tags,
                    'color' => $color,
                    'status' => $status,
                    'in_menu' => $in_menu
                        ], [
                    'name' => 'required',
                    'sort' => 'required|numeric',
                    'tags' => 'required',
                    'color' => 'required',
                    'status' => 'required|numeric|in:0,1',
                    'in_menu' => 'required|numeric|in:0,1'
        ]);
        ////////////////////////////////////////
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('categories.add'))->withInput();
        } else {
            $categories = new Categories();
            $add = $categories->addCategories($name, $category_id, $sort, $tags, $color, $status, $in_menu);
            if ($add) {
                $this->clearCache();
                //////////////////////////////////////////////////////////////////
                $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route('categories.view'));
            } else {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('categories.add'))->withInput();
            }
        }
    }

    //////////////////////////////////////////////
    public function getEdit(Request $request, $id) {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('categories.view'));
        }
        /////////////////////////////
        $categories = new Categories();
        $info = $categories->getCategories($id);
        parent::$data['categories'] = $categories->getAllActiveCategories();
        if ($info) {
            parent::$data['info'] = $info;
            return view('admin.categories.edit', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('categories.view'));
        }
    }

    //////////////////////////////////////////////
    public function postEdit(Request $request, $id) {
        try {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('categories.view'));
        }
        /////////////////////////////
        $categories = new Categories();
        $info = $categories->getCategories($id);
        if ($info) {
            $name = $request->get('name');
            $category_id = $request->get('category_id');
            $sort = $request->get('sort');
            $tags = $request->get('tags');
            $color = $request->get('color');

            $status = (int) $request->get('status');
            $in_menu = (int) $request->get('in_menu');

            $validator = Validator::make([
                        'name' => $name,
                        'sort' => $sort,
                        'tags' => $tags,
                        'color' => $color,
                        'status' => $status,
                        'in_menu' => $in_menu
                            ], [
                        'name' => 'required',
                        'sort' => 'required|numeric',
                        'tags' => 'required',
                        'color' => 'required',
                        'status' => 'required|numeric|in:0,1',
                        'in_menu' => 'required|numeric|in:0,1',
            ]);

            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('categories.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                $update = $categories->updateCategories($info, $name, $category_id, $sort, $tags, $color, $status, $in_menu);
                if ($update) {
                    $this->clearCache();
                    ///////////////////////////////////////////////////////////
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('categories.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('categories.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('categories.view'));
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
        /////////////////////////////
        $categories = new Categories();
        $info = $categories->getCategories($id);
        if ($info) {
            $status = $info->status;
            if ($status == 0) {
                $update = $categories->updateStatus($id, 1);
                if ($update) {
                    $this->clearCache();
                    ///////////////////////////////////////////////////////////
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $categories->updateStatus($id, 0);
                if ($update) {
                    $this->clearCache();
                    ///////////////////////////////////////////////////////////
                    return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

    //////////////////////////////////////////////
    public function postDelete(Request $request) {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////
        $categories = new Categories();
        $info = $categories->getCategories($id);
        if ($info) {
            $delete = $categories->deleteCategories($info);
            if ($delete) {
                $this->clearCache();
                ///////////////////////////////////////////////////////////
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            } else {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

    /////////////////////////////////////////
    public function clearCache() {
        Cache::forget('categories');
        $categories = new Categories();
        $info = $categories->getAllActiveCategories();
        Cache::forever('categories', $info);
        foreach ($info as $row) {
            Cache::forget('category_info_' . $row->id);
            ////////////////////////////////////////
            $category_info = $categories->getActiveCategories($row->id);
            Cache::forever('category_info_' . $row->id, $category_info);
        }
    }

}
