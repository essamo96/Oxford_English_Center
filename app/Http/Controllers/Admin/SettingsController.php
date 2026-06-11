<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Hash;
use Crypt;
use Session;
use Validator;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Settings;

//////////////////////////////////

class SettingsController extends AdminController {

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
        parent::$data['active_menu'] = 'settings';
    }

    //////////////////////////////////////////
    public function getIndex(Request $request) {
        $settings = new Settings();
        $info = $settings->getSetting(1);
        if ($info) {
            parent::$data['info'] = $info;
            return view('admin.settings.view', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('settings.view'));
        }
    }

    ////////////////////////////////////////////////
    public function postIndex(Request $request) {
        $settings = new Settings();
        $info = $settings->getSetting(1);
        if ($info) {
            $db_logo = $info->logo;
            ////////////////////////////////
            $title = $request->get('title');
            $description = $request->get('description');
            $more_desc = $request->get('more_desc');
            $tags = $request->get('tags');
            $mobile = $request->get('mobile');
            $address = $request->get('address');
            $donars = $request->get('donars');
            $clients = $request->get('clients');
            $happy = $request->get('happy');
            $tickects = $request->get('tickects');
            $contact_email = $request->get('contact_email');
            $payment_required = $request->get('payment_required', 0) ? 1 : 0;
            $registration_open = $request->get('registration_open', 0) ? 1 : 0;
            $registration_closed_message = $request->get('registration_closed_message');

            $validator = Validator::make([
                        'title' => $title,
                        'description' => $description,
                        'tags' => $tags,
                        'mobile' => $mobile,
                        'address' => $address,
                        'donars' => $donars,
                        'clients' => $clients,
                        'happy' => $happy,
                        'tickects' => $tickects,
                        'contact_email' => $contact_email,
                            ], [
                        'title' => 'required',
                        'description' => 'required',
                        'tags' => 'required',
                        'mobile' => 'required',
                        'address' => 'required',
                        'donars' => 'required|numeric',
                        'clients' => 'required|numeric',
                        'happy' => 'required|numeric',
                        'tickects' => 'required|numeric',
                        'contact_email' => 'nullable|email',
            ]);
            //////////////////////////////////////////////////////////
            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('settings.view'))->withInput();
            } else {
                $update = $settings->updateSettings($info, $title, $description, $more_desc, $tags, $mobile, $address, $donars, $clients, $happy, $tickects, $contact_email, $payment_required, $registration_open, $registration_closed_message);
                if ($update) {
                    Cache::forget('settings');
                    Cache::forget('site_settings');
                    $info = $settings->getSetting(1);
                    Cache::forever('mysettings', $info);
                    ////////////////////////////////////////////
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('settings.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('settings.view'));
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('settings.view'));
        }
    }

}
