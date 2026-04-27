<?php

namespace App\Http\Controllers\Admin;

use App\Models\Socials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SocialsController extends AdminController
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
        parent::$data['active_menu'] = 'socials';
    }
    //////////////////////////////////////////
    public function getIndex()
    {
        $social = new Socials();
        parent::$data['socials'] = $social->getAllSocial();
        return view('admin.socials.view', parent::$data);
    }
    //////////////////////////////////////////
    public function postIndex(Request $request)
    {
        $ids = $request->get('id');
        $link = $request->get('link', []);
        $icon = $request->get('icon', []);
        $status = $request->get('status', []);
        foreach ($ids as $id)
        {
            $social = new Socials();
            $info = $social->getSocial($id);
            if($info)
            {
                $social_link = isset($link[$id]) ? $link[$id] : $info->link;
                $social_icon = isset($icon[$id]) ? $icon[$id] : $info->icon;
                $social_status = isset($status[$id]) ? $status[$id] : $info->status;
                $social->updateSocial($info, $social_link, $social_icon, $social_status);
            }
        }
        /////////////////////////////////////////////
        Cache::forget('social');
        $info = $social->getAllSocialActive();
        Cache::forever('social', $info);
        ////////////////////////////////////////////
        $request->session()->flash('success', self::UPDATE_SUCCESS);
        return redirect(route('socials.view'));
    }
}
