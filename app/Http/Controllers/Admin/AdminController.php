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


class AdminController extends BaseController
{
    public static $data = [];

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        self::$data['component_style'] = 1;
        self::$data['color'] = "grey";
        self::$data['fixed_sidebar'] = 0;
        self::$data['form_class'] = "dark";
        self::$data['btn_class'] = "dark";
        self::$data['per_page'] = 20;
        $count_disabled_students = MembershipsController::getCountOfStudentMembershipRequests();
        $obj = new Students_Admin_Messages();
        $obj2 = new Teachers_Admin_Messages();
        $obj3 = new Students();
        $Unread_measges_student = $obj->getAllUnreadStudentsMessages();
        $Unread_measges_teacher = $obj2->getAllUnreadTeachersMessages();
        $BirthdaysCount = $obj3->getAllStudentsHaveBirthdaysCount();
        self::$data['count_disabled_students'] =  $count_disabled_students;
        self::$data['Unread_measges_student'] =  $Unread_measges_student;
        self::$data['Unread_measges_teacher'] =  $Unread_measges_teacher;
        self::$data['BirthdaysCount'] =  $BirthdaysCount;
        self::$data['Closed_Classes_count'] =  Closed_Classes::where('seen', 0)->whereNull('deleted_at')->count();
        self::$data['Unseen_Students_Count'] = Students::where('seen', 0)->where('status', 0)->whereNull('deleted_at')->count();
        self::$data['Unseen_Contacts_Count'] = \App\Support\NotifyCounts::unreadContacts();

        // Calculate grand total for header bell (kept in sync with the live
        // broadcast counter via App\Support\NotifyCounts).
        self::$data['total_notify_count'] = self::$data['Closed_Classes_count'] +
            self::$data['Unseen_Students_Count'] +
            self::$data['Unseen_Contacts_Count'] +
            self::$data['Unread_measges_student'] +
            self::$data['Unread_measges_teacher'];

        self::$data['total_teachers_students_measge'] =  $Unread_measges_teacher + $Unread_measges_student;
        self::$data['minutes'] = 60 * 24;
        //////////////////////////////////////
        self::$data['home'] = 0;
        self::$data['mysettings'] = Cache::rememberForever('mysettings', function () {
            $settings = new Settings();
            return $settings->getSetting(1);
        });
        self::$data['social'] = Cache::remember('social', self::$data['minutes'], function () {
            $social = new Socials();
            return $social->getAllSocialActive();
        });
        self::$data['notify_closed_clases'] = Closed_Classes::with(['Teacher', 'Groups'])->where('seen', 0)->latest()->take(5)->get();

        self::$data['notify_students'] = Students::where('seen', 0)->where('status', 0)->latest()->take(5)->get();
        self::$data['notify_Students_Admin_Messages'] = Students_Admin_Messages::where('seen', 0)->latest()->take(5)->get();
        self::$data['notify_Teachers_Admin_Messages'] = Teachers_Admin_Messages::where('seen', 0)->latest()->take(5)->get();
        self::$data['notify_Groups'] = Groups::where('seen_progress', 0)->where('progress', '!=', 0)->latest()->take(5)->get();
        self::$data['notify_contacts'] = \App\Models\Contacts::where('status', 0)->latest()->take(5)->get();

        // Original list variables (standardized names)
        self::$data['Programs'] = Programs::whereNull('deleted_at')->get();
        self::$data['Groups'] = Groups::whereNull('deleted_at')->get();
        self::$data['teachers'] = Teachers::whereNull('deleted_at')->get();
        self::$data['Students'] = Students::whereNull('deleted_at')->get();
        self::$data['GroupStudents'] = GroupStudents::distinct('group_id')->whereNull('deleted_at')->get();
    }
}
