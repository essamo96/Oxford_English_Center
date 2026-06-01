<?php

namespace App\Http\Controllers\Admin;

use App\Models\AttendanceSetting;
use Illuminate\Http\Request;

class AttendanceSettingController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'attendance_settings';
    }

    public function getIndex()
    {
        parent::$data['setting']    = AttendanceSetting::current();
        parent::$data['server_ip']  = request()->server('SERVER_ADDR');
        parent::$data['client_ip']  = request()->ip();
        return view('admin.attendance.settings', parent::$data);
    }

    public function postIndex(Request $request)
    {
        $request->validate([
            'allowed_ips'   => 'nullable|string|max:2000',
            'grace_minutes' => 'required|integer|min:0|max:240',
        ]);

        $setting = AttendanceSetting::current();
        $setting->allowed_ips   = $request->allowed_ips;
        $setting->grace_minutes = (int) $request->grace_minutes;
        $setting->enforce_ip    = $request->boolean('enforce_ip');
        $setting->enforce_time  = $request->boolean('enforce_time');
        $setting->save();

        return redirect()->route('admin.attendance.settings')
            ->with('success', 'تم حفظ إعدادات الحضور بنجاح.');
    }
}
