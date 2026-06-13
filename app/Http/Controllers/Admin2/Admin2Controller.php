<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Models\PermissionsGroup;
use App\Models\Financial_Loan;
use App\Models\Holiday;
use App\Models\Employee;
use App\Models\Section;
use App\Models\Salary;
use App\Models\Emp_Attachments;
use App\Models\Incentive_Management;
use App\Models\Emp_Allowance;
use App\Models\Circulars;
use App\Models\Settings;
use App;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Students_Admin_Messages;
use App\Models\Teachers_Admin_Messages;
use App\Models\Closed_Classes;
use App\Models\Students;
use App\Models\Groups;
use App\Models\Programs;
use App\Models\GroupStudents;
use App\Models\Teachers;
use App\Models\Socials;


/* * **************************** */

class AdminController extends BaseController
{

    public static $data = [];

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    public function __construct()
    {
        if (Cache::get('lang') !== null) {
            App::setLocale(Cache::get('lang'));
        } else {
            Cache::forever('lang', 'en');
            App::setLocale(Cache::get('lang'));
        }

        $permission_group = new PermissionsGroup();
        self::$data['sidebar'] = $permission_group->getAllParentPermissionGroup();
        self::$data['settings'] = Settings::where('id', 1)->first();
        self::$data['activeBranch'] = app(\App\Services\BranchContext::class)->getBranch();
        self::$data['isBranchScoped'] = app(\App\Services\BranchContext::class)->isScoped();
        $route_name = Route::currentRouteName();
        $route_data = explode('.', $route_name);
        $current_route = $route_data[0];
        $init_obj = new \stdClass();
        $init_obj->name = '';
        $init_obj->parent_id = '';
        self::$data['current_route'] = $init_obj;
        foreach (self::$data['sidebar'] as $menu_item) {
            if ($current_route == $menu_item->name) {
                self::$data['current_route'] = $menu_item;
            }
            foreach ($menu_item->mychild as $child_item) {
                if ($current_route == $child_item->name) {
                    self::$data['current_route'] = $child_item;
                    break;
                }
            }
        }

        // self::$data['holiday_count'] = Holiday::where('status', 0)->where('seen', 0)->count();
        // $latestHoliday = Holiday::where('status', 0)->latest('created_at')->first();
        // self::$data['holiday_created_at'] = $latestHoliday ? $latestHoliday->created_at->diffForHumans() : '';

        // self::$data['loan_count'] = Financial_Loan::where('loan_status', 0)->where('seen', 0)->count();
        // $latestLoan = Financial_Loan::where('loan_status', 0)->latest('created_at')->first();
        // self::$data['loan_created_at'] = $latestLoan ? $latestLoan->created_at->diffForHumans() : '';

        // self::$data['notify_count'] = self::$data['holiday_count'] + self::$data['loan_count'];
        // // dd(Auth::guard('admin')->user()->id);
        // self::$data['employees'] = Employee::all();
        // self::$data['incentive'] = Incentive_Management::whereNull('deleted_at')->get();
        // self::$data['allowance'] = Emp_Allowance::whereNull('deleted_at')->get();
        // self::$data['Section'] = Section::whereNull('deleted_at')->get();
        // self::$data['Salary'] = Salary::whereNull('deleted_at')->get();

        // self::$data['Financial_Loan'] = Financial_Loan::whereNull('deleted_at')->get();

        // self::$data['Holiday'] = Holiday::whereNull('deleted_at')->get();
        // $currentDate = Carbon::now()->toDateString();
        // self::$data['Attachments'] = Emp_Attachments::whereDate('Issue_date', '<=', $currentDate)->whereNull('deleted_at')->get();
        // self::$data['Circulars'] = Circulars::whereNull('deleted_at')->get();
    }
}
