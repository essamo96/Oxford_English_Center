<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use DataTables;
/////////////////////////////////////
use App\Models\Advertisements;

class AdvertisementsController extends AdminController
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
        parent::$data['active_menu'] = 'advertisements';
    }
    //////////////////////////////////////////////
    public function getIndex()
    {
        return view('admin.advertisements.view', parent::$data);
    }
    //////////////////////////////////////////////
    public function getList(Request $request)
    {
        $advertisements = new Advertisements();

        $length = $request->get('length');
        $start = $request->get('start');
        $name = $request->get('name');

        $info = $advertisements->getSearchAdvertisements($name);

        $datatable = Datatables::of($info);

        $datatable->editColumn('name', function ($row)
        {
            return (!empty($row->name) ? $row->name : 'N/A');
        });

        $datatable->editColumn('status', function ($row)
        {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.advertisements.parts.status', $data)->render();
        });

        $datatable->addColumn('actions', function ($row)
        {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.advertisements.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }
    //////////////////////////////////////////////
    public function getAdd()
    {
        return view('admin.advertisements.add', parent::$data);
    }
    //////////////////////////////////////////////
    public function postAdd(Request $request)
    {
        $name = $request->get('name');
        $image = $request->file('image');
        $type = $request->get('type');
        $expiry_date = $request->get('expiry_date');
        $url = $request->get('url');
        $status = (int)$request->get('status');

        $validator = Validator::make([
            'name' => $name,
            'image' => $image,
            'type' => $type,
            'expiry_date' => $expiry_date,
            'url' => $url,
            'status' => $status
        ], [
            'name' => 'required|unique:advertisements,name,0,id,deleted_at,NULL',
            'image' => 'required|image',
            'type' => 'required|numeric|in:1,2',
            'expiry_date' => 'required|date|date_format:Y-m-d|after:today',
            'url' => 'required|url',
            'status' => 'required|numeric|in:0,1'
        ]);
        ////////////////////////////////////////
        if ($validator->fails())
        {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('advertisements.add'))->withInput();
        }
        else
        {
            if ($request->hasFile('image') && $image->isValid())
            {
                $destinationPath = 'uploads/advertisements/';
                $image_name = 'image_' . strtotime(date("Y-m-d H:i:s")) . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $image_name);
            }
            else
            {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('advertisements.add'))->withInput();
            }
            //////////////////////////////////////
            $advertisements = new Advertisements();
            $add = $advertisements->addAdvertisement($name,$image_name,$type,$expiry_date,$url,$status);
            if ($add)
            {
                $this->clearCache();
                //////////////////////////////////////////////////////////////////
                $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route('advertisements.view'));
            }
            else
            {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('advertisements.add'))->withInput();
            }
        }
    }
    //////////////////////////////////////////////
    public function getEdit(Request $request, $id)
    {
        try
        {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('advertisements.view'));
        }
        /////////////////////////////
        $advertisements = new Advertisements();
        $info = $advertisements->getAdvertisement($id);
        if ($info)
        {
            parent::$data['info'] = $info;
            return view('admin.advertisements.edit', parent::$data);
        }
        else
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('advertisements.view'));
        }
    }
    //////////////////////////////////////////////
    public function postEdit(Request $request, $id)
    {
        try
        {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('advertisements.view'));
        }
        /////////////////////////////
        $advertisements = new Advertisements();
        $info = $advertisements->getAdvertisement($id);
        if ($info)
        {
            $db_image = $info->image;
            //////////////////////////////////
            $name = $request->get('name');
            $image = $request->file('image');
            $type = $request->get('type');
            $expiry_date = $request->get('expiry_date');
            $url = $request->get('url');
            $status = (int)$request->get('status');

            $validator = Validator::make([
                'name' => $name,
                'image' => $image,
                'type' => $type,
                'expiry_date' => $expiry_date,
                'url' => $url,
                'status' => $status
            ], [
                'name' => 'required|unique:advertisements,name,'.$id.',id,deleted_at,'.'NULL',
                'image' => 'nullable|image',
                'type' => 'required|numeric|in:1,2',
                'expiry_date' => 'required|date|date_format:Y-m-d|after:today',
                'url' => 'required|url',
                'status' => 'required|numeric|in:0,1'
            ]);
            ////////////////////////////////////////////////////////////////
            if ($validator->fails())
            {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('advertisements.edit', ['id' => $encrypted_id]))->withInput();
            }
            else
            {
                if ($request->hasFile('image') && $image->isValid())
                {
                    $destinationPath = 'uploads/advertisements/';
                    $image_name = 'image_' . strtotime(date("Y-m-d H:i:s")) . '.' . $image->getClientOriginalExtension();
                    $image->move($destinationPath, $image_name);
                    @unlink(@'uploads/advertisements/'.$db_image);
                }
                else
                {
                    $image_name = $db_image;
                }
                /////////////////////////////////////////////////
                $update = $advertisements->updateAdvertisement($info, $name, $image_name,$type,$expiry_date,$url,$status);
                if ($update)
                {
                    $this->clearCache();
                    ///////////////////////////////////////////////////////////
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('advertisements.view'));
                }
                else
                {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('advertisements.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        }
        else
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('advertisements.view'));
        }
    }
    //////////////////////////////////////////////
    public function postStatus(Request $request)
    {
        $id = $request->get('id');
        try
        {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////
        $advertisements = new Advertisements();
        $info = $advertisements->getAdvertisement($id);
        if ($info)
        {
            $status = $info->status;
            if($status == 0)
            {
                $update = $advertisements->updateStatus($id,1);
                if($update)
                {
                    $this->clearCache();
                    ///////////////////////////////////////////////////////////
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                }
                else
                {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
            else
            {
                $update = $advertisements->updateStatus($id,0);
                if($update)
                {
                    $this->clearCache();
                    ///////////////////////////////////////////////////////////
                    return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
                }
                else
                {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
        }
        else
        {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }
    //////////////////////////////////////////////
    public function postDelete(Request $request)
    {
        $id = $request->get('id');
        try
        {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////
        $advertisements = new Advertisements();
        $info = $advertisements->getAdvertisement($id);
        if ($info)
        {
            $delete = $advertisements->deleteAdvertisement($info);
            if ($delete)
            {
                $this->clearCache();
                ///////////////////////////////////////////////////////////
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            }
            else
            {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        }
        else
        {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }
    /////////////////////////////////////////
    public function clearCache()
    {
//        $advertisements = new Advertisements();
//        $info = $advertisements->getAllActiveAdvertisements();
//        foreach($info as $row)
//        {
//            Cache::forget('advertisements_info_'.$row->id);
//            ////////////////////////////////////////
//            $data = $this->diff($row->expiry_date);
//            $hours = $data->h;
//            ///////////////////////////////////////
//            Cache::remember('advertisements_info_'.$row->id, ($hours*60), function () use ($row) {
//                return $row;
//            });
//        }
    }
    /////////////////////////////////////////
    function diff($date)
    {
        $start = new \DateTime();
        $end   = new \DateTime($date);
        $diff  = $start->diff($end);

        return $diff;
    }
}
