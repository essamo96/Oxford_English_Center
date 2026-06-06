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
use Illuminate\Support\Facades\File;
////////////////////////////////////
use App\Models\Photos;
use App\Models\Categories;
use App\Models\Images;

class PhotosController extends AdminController {

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
        parent::$data['active_menu'] = 'photos';
    }

    //////////////////////////////////////////////
    public function getIndex() {
        $categories = new Categories();
        parent::$data['categories'] = $categories->getAllActiveCategories();
        return view('admin.photos.view', parent::$data);
    }

    //////////////////////////////////////////////
    public function getList(Request $request) {
        $title = $request->get('title', NULL);

        $photos = new Photos();
        $info = $photos->getSearchPhotos($title);
        $datatable = Datatables::of($info);
        $datatable->editColumn('album', function ($row) {
            $data['id'] = $row->id;
            $data['album'] = sizeof($row->images);

            return view('admin.photos.parts.album', $data)->render();
        });
        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.photos.parts.status', $data)->render();
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.photos.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getAdd() {
        $categories = new Categories();
        parent::$data['categories'] = $categories->getAllActiveCategories();
        return view('admin.photos.add', parent::$data);
    }

    //////////////////////////////////////////////
    public function postAdd(Request $request) {
        $title = $request->get('title');
        $descs = $request->get('descs');
        $tags = $request->get('tags');
        $status = (int) $request->get('status');

        $validator = Validator::make([
                    'title' => $title,
                    'descs' => $descs,
                    'status' => $status,
                        ], [
                    'title' => 'required',
                    'descs' => 'required',
                    'status' => 'required|numeric|in:0,1'
        ]);
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('photos.add'))->withInput();
        } else {

            $photos = new Photos();
            $add = $photos->addPhoto($title, $descs, $tags, $status, Auth::guard('admin')->user()->id);
            if ($add) {
                $this->clearCache();
                ///////////////////////////////////////////////////////////////////
                $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route('photos.view'));
            } else {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('photos.add'))->withInput();
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
        $photos = new Photos();
        $info = $photos->getPhoto($id);
        if ($info) {
            $categories = new Categories();
            parent::$data['categories'] = $categories->getAllActiveCategories();
            parent::$data['info'] = $info;
            return view('admin.photos.edit', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('photos.view'));
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
        $photos = new Photos();
        $info = $photos->getPhoto($id);
        if ($info) {
            $title = $request->get('title');
            $descs = $request->get('descs');
            $tags = $request->get('tags');
            $status = (int) $request->get('status');

            $validator = Validator::make([
                        'title' => $title,
                        'descs' => $descs,
                        'status' => $status
                            ], [
                        'title' => 'required',
                        'descs' => 'required',
                        'status' => 'required|numeric|in:0,1'
            ]);
            //////////////////////////////////////////////////////////
            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('photos.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                $update = $photos->updatePhoto($info, $title, $descs, $tags, $status);
                if ($update) {
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('photos.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('photos.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('photos.view'));
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
        $photos = new Photos();
        $info = $photos->getPhoto($id);
        if ($info) {
            $delete = $photos->deletePhoto($info);
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
        $photos = new Photos();
        $info = $photos->getPhoto($id);
        if ($info) {
            $status = $info->status;
            if ($status == 0) {
                $update = $photos->updateStatus($id, 1);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $photos->updateStatus($id, 0);
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

    //////////////////////////////////////////////
    public function postFeature(Request $request) {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $photos = new Photos();
        $info = $photos->getPhoto($id);
        if ($info) {
            $status = $info->feature;
            if ($status == 0) {
                $update = $photos->updateFeature($id, 1);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $photos->updateFeature($id, 0);
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

    /////////////////////////////////////////
    public function clearCache() {
        Cache::forget('photo');
        $photo = new Photos();
        $info = $photo->getLastPhoto();
        Cache::forever('photo', $info);
    }

    public function getImages($id) {
        parent::$data['id'] = $id;
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('photos.view'));
        }
        $images = new Images();
        parent::$data['images'] = $images->getAllImages($id);
        return view('admin.photos.images', parent::$data);
    }

    public function ajaxImage(Request $request) {
        $validator = Validator::make($request->all(), [
                    'file' => 'image',
                        ], [
                    'file.image' => 'The file must be an image (jpeg, png, bmp, gif, or svg)'
        ]);
        if ($validator->fails())
            return array(
                'fail' => true,
                'errors' => $validator->errors()
            );
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        $extension = $request->file('file')->getClientOriginalExtension();
        $photo = new Photos();
        $info = $photo->getPhoto($id);
        if ($info) {
            $dir = 'File/Images/photo/';
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $request->file('file')->move($dir, $filename);
            $photo = new Images();
            $final_name = $filename;
            $info = $photo->addPhoto($final_name, $id);
            // Image::make($dir . $filename)->resize(200, 200)->save($dir . 'thumb/' . $filename);
            return $final_name;
        } else {
            return false;
        }
    }

    public function deleteImage(Request $request) {
        $name = $request->get('file');
        $name = str_replace("File/Images/photo/", "", $name);
        $photo = new Images();
        $info = $photo->getImageByName($name);
        if ($info) {
            $delete = $photo->deleteImage($info);
            File::delete('File/Images/photo/' . $name);
        }
    }

    public function featureImage(Request $request) {
        $name = $request->get('file');
        $name = str_replace("File/Images/photo/", "", $name);
        $photo = new Images();
        $info = $photo->getImageByName($name);
        if ($info) {
            $photo->featureImage($info->id, $info->album_id);
        }
    }

}
