<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
////////////////////////////////////
use App\Models\ShoppingCategory;
use App\Models\Shopping;

class ShoppingController extends AdminController {

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
        parent::$data['active_menu'] = 'shopping';
    }

    //////////////////////////////////////////////
    public function getIndexCategory() {
        return view('admin.shopping.category.view', parent::$data);
    }

    public function getListCategory(Request $request) {
        $title = $request->get('title', NULL);

        $shopping = new ShoppingCategory();
        $info = $shopping->getSearchCategory($title);
        $datatable = Datatables::of($info);

        $datatable->editColumn('title', function ($row) {
            return (!empty($row->name) ? $row->name : 'N/A');
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.shopping.category.parts.status', $data)->render();
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.shopping.category.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    public function getEditCategory(Request $request, $id) {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('shopping.view_category'));
        }
        /////////////////////////////
        $categories = new ShoppingCategory();
        $info = $categories->getCategories($id);
        if ($info) {
            parent::$data['info'] = $info;
            return view('admin.shopping.category.edit', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('shopping.view_category'));
        }
    }

    public function getAddCategory() {
        return view('admin.shopping.category.add', parent::$data);
    }

    //////////////////////////////////////////////
    public function postAddCategory(Request $request) {
        $name = $request->get('name');
        $sort = $request->get('sort');
        $tags = $request->get('tags');
        $descs = $request->get('descs');
        $status = (int) $request->get('status');
        $in_menu = (int) $request->get('in_menu');

        $validator = Validator::make([
                    'name' => $name,
                    'sort' => $sort,
                    'tags' => $tags,
                    'status' => $status,
                    'in_menu' => $in_menu
                        ], [
                    'name' => 'required',
                    'sort' => 'required|numeric',
                    'tags' => 'required',
                    'status' => 'required|numeric|in:0,1',
                    'in_menu' => 'required|numeric|in:0,1'
        ]);
        ////////////////////////////////////////
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('shopping.add_category'))->withInput();
        } else {
            $categories = new ShoppingCategory();
            $add = $categories->addCategories($name, $sort, $tags, $descs, $status, $in_menu);
            if ($add) {
                $this->clearCache();
                //////////////////////////////////////////////////////////////////
                $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route('shopping.view_category'));
            } else {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('shopping.add_category'))->withInput();
            }
        }
    }

    //////////////////////////////////////////////
    public function postEditCategory(Request $request, $id) {
        try {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('shopping.view_category'));
        }
        /////////////////////////////
        $categories = new ShoppingCategory();
        $info = $categories->getCategories($id);
        if ($info) {
            $name = $request->get('name');
            $sort = $request->get('sort');
            $tags = $request->get('tags');
            $descs = $request->get('descs');

            $status = (int) $request->get('status');
            $in_menu = (int) $request->get('in_menu');

            $validator = Validator::make([
                        'name' => $name,
                        'sort' => $sort,
                        'tags' => $tags,
                        'status' => $status,
                        'in_menu' => $in_menu
                            ], [
                        'name' => 'required',
                        'sort' => 'required|numeric',
                        'tags' => 'required',
                        'status' => 'required|numeric|in:0,1',
                        'in_menu' => 'required|numeric|in:0,1',
            ]);

            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('shopping.edit_category', ['id' => $encrypted_id]))->withInput();
            } else {
                $update = $categories->updateCategories($info, $name, $sort, $tags, $descs, $status, $in_menu);
                if ($update) {
                    $this->clearCache();
                    ///////////////////////////////////////////////////////////
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('shopping.view_category'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('shopping.edit_category', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('shopping.view_category'));
        }
    }

    public function postDeleteCategory(Request $request) {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $shopping = new ShoppingCategory();
        $info = $shopping->getCategories($id);
        if ($info) {
            $delete = $shopping->deleteCategories($info);
            if ($delete) {
                $this->clearCache();
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            } else {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

    //////////////////////////////////////////////
    public function postStatusCategory(Request $request) {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $shopping = new ShoppingCategory();
        $info = $shopping->getCategories($id);
        if ($info) {
            $status = $info->status;
            if ($status == 0) {
                $update = $shopping->updateStatus($id, 1);
                if ($update) {
                    $this->clearCache();
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $shopping->updateStatus($id, 0);
                if ($update) {
                    $this->clearCache();
                    return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

    public function getIndex() {
        return view('admin.shopping.view', parent::$data);
    }

    //////////////////////////////////////////////
    public function getList(Request $request) {
        $title = $request->get('title', NULL);

        $shopping = new Shopping();
        $info = $shopping->getSearchShopping($title);
        $datatable = Datatables::of($info);

        $datatable->editColumn('title', function ($row) {
            return (!empty($row->title) ? $row->title : 'N/A');
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.shopping.parts.status', $data)->render();
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.shopping.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getAdd() {
        $shopping = new ShoppingCategory();
        parent::$data['info'] = $shopping->getActiveCategories();
        return view('admin.shopping.add', parent::$data);
    }

    //////////////////////////////////////////////
    public function postAdd(Request $request) {
        $category_id = $request->get('category_id');
        $title = $request->get('title');
        $descs = $request->get('descs');
        $image = $request->get('image');
        $tags = $request->get('tags');
        $publish = (int) $request->get('status');


        $validator = Validator::make([
                    'category_id' => $category_id,
                    'title' => $title,
                    'descs' => $descs,
                    'image' => $image,
                        ], [
                    'category_id' => 'required',
                    'title' => 'required',
                    'descs' => 'required',
                    'image' => 'required',
        ]);
//////////////////////////////////////////////////////////
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('shopping.add'))->withInput();
        } else {
            $shopping = new Shopping();

////////////////////////////////////////////
            $add = $shopping->addShopping($title, $descs, $image, $category_id, $tags, $publish, Auth::guard('admin')->user()->id);
            if ($add) {
                if ($publish == 1) {
                    $this->clearCache($category_id);
                }
///////////////////////////////////////////////////////////////////
                $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route('shopping.view'));
            } else {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('shopping.add'))->withInput();
            }
        }
    }

    //////////////////////////////////////////////
    public function getEdit(Request $request, $id) {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('shopping.view'));
        }
        //////////////////////////////////////////////
        $shopping = new Shopping();
        $categories = new ShoppingCategory();

        $info = $shopping->getShopping($id);
        if ($info) {
            parent::$data['categories'] = $categories->getActiveCategories();

            parent::$data['info'] = $info;
            return view('admin.shopping.edit', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('shopping.view'));
        }
    }

    ////////////////////////////////////////////////
    public function postEdit(Request $request, $id) {
        try {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('shopping.view'));
        }
        /////////////////////////////
        $shopping = new Shopping();
        $info = $shopping->getShopping($id);
        if ($info) {
            $category_id = $request->get('category_id');
            $title = $request->get('title');
            $descs = $request->get('descs');
            $image = $request->get('image');
            $tags = $request->get('tags');
            $publish = (int) $request->get('status');


            $validator = Validator::make([
                        'category_id' => $category_id,
                        'title' => $title,
                        'descs' => $descs,
                            ], [
                        'category_id' => 'required',
                        'title' => 'required',
                        'descs' => 'required',
            ]);
//////////////////////////////////////////////////////////
            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('shopping.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                $old_category_id = $info->category_id;
////////////////////////////////////////////
                $update = $shopping->updateShopping($info, $title, $descs, $image, $category_id, $tags, $publish);
                if ($update) {
                    if ($info->publish == 1) {
                        if ($old_category_id != $category_id) {
                            $this->clearCache($old_category_id);
                        }
                        $this->clearCache($category_id);
                        //   $this->getMedium($title, $descs, $image);
                        //$this->getTwitter($update);
                    }
///////////////////////////////////////////////////////////
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('shopping.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('shopping.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('shopping.view'));
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
        $shopping = new Shopping();
        $info = $shopping->getShopping($id);
        if ($info) {
            $delete = $shopping->deleteShopping($info);
            if ($delete) {
                $this->clearCache();
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
        $shopping = new Shopping();
        $info = $shopping->getShopping($id);
        if ($info) {
            $status = $info->status;
            if ($status == 0) {
                $update = $shopping->updateStatus($id, 1);
                if ($update) {
                    $this->clearCache();
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $shopping->updateStatus($id, 0);
                if ($update) {
                    $this->clearCache();
                    return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

    /////////////////////////////////////////
    public function clearCache() {
//        Cache::forget('shopping');
//        $photo = new Shopping();
//        $info = $photo->getAllShopping();
//        Cache::forever('shopping', $info);
    }

}
