<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Categories;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class DashboardController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'dashboard';
    }
    ///////////////////////////////
    public function getIndex()
    {
        $categories = new Categories();
        parent::$data['categories'] = $categories->getCategoriesWithNewsCount();

        // Advanced Statistics — scoped to branch when admin has branch_id
        $branchId = app(\App\Services\BranchContext::class)->getId();

        $groups   = \App\Models\Groups::whereNull('deleted_at')->get();   // BranchScope auto-applies
        $teachers = \App\Models\Teachers::whereNull('deleted_at')->get(); // BranchScope auto-applies
        $students = \App\Models\Students::whereNull('deleted_at')->get(); // BranchScope auto-applies

        // GroupStudents count scoped by branch when needed
        $groupStudentsCount = \App\Models\GroupStudents::whereNull('deleted_at')
            ->when($branchId, fn($q) => $q->whereHas('student', fn($sq) => $sq->where('branch_id', $branchId)))
            ->count();

        parent::$data['total_groups'] = $groups->count();
        parent::$data['active_groups_count'] = $groups->where('status', 1)->count();
        parent::$data['total_students_count'] = $students->count();
        parent::$data['active_students_count'] = $students->where('status', 1)->count();
        parent::$data['delayed_students_count'] = $students->where('delaying', 1)->count();
        parent::$data['inactive_students_count'] = $students->where('status', 0)->count();
        parent::$data['total_teachers_count'] = $teachers->count();

        // Avg Students per Group
        parent::$data['avg_students_per_group'] = parent::$data['total_groups'] > 0
            ? round($groupStudentsCount / parent::$data['total_groups'], 1)
            : 0;

        // Most Active Teacher (Teacher with most active groups) — scoped
        $mostActiveTeacher = \App\Models\Groups::whereNull('deleted_at')
            ->where('status', 1)
            ->select('teacher_id', \DB::raw('count(*) as total'))
            ->groupBy('teacher_id')
            ->orderBy('total', 'desc')
            ->first();

        if ($mostActiveTeacher && $mostActiveTeacher->teacher_id) {
            $teacherInfo = \App\Models\Teachers::find($mostActiveTeacher->teacher_id);
            parent::$data['most_active_teacher'] = $teacherInfo ? $teacherInfo->name : 'N/A';
            parent::$data['most_active_teacher_count'] = $mostActiveTeacher->total;
        } else {
            parent::$data['most_active_teacher'] = 'N/A';
            parent::$data['most_active_teacher_count'] = 0;
        }

        // Groups Today — scoped
        $todayName = \Carbon\Carbon::now()->format('l');
        $times = \App\Models\Times::where('status', 1)->get();
        $todayTimeIds = $times->filter(function($t) use ($todayName) {
            return stripos($t->days, $todayName) !== false;
        })->pluck('id');

        parent::$data['groups_today_count'] = $groups->whereIn('date_id', $todayTimeIds)->where('status', 1)->count();

        // Weekly Schedule — scoped via BranchScope on Groups
        $daysMapping = [
            'Monday' => 'الاثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد'
        ];

        $weeklySchedule = [];
        $allActiveGroups = \App\Models\Groups::with(['program', 'ctime', 'teacher'])
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->get();

        foreach ($daysMapping as $enDay => $arDay) {
            $weeklySchedule[$enDay] = $allActiveGroups->filter(function($group) use ($arDay) {
                return $group->ctime && (stripos($group->ctime->days, $arDay) !== false);
            })->sortBy(function($group) {
                return $group->ctime ? $group->ctime->times : '';
            });
        }
        parent::$data['weekly_schedule'] = $weeklySchedule;
        parent::$data['all_active_groups'] = $allActiveGroups;

        // Course Progress — scoped: count active groups per program filtered by branch
        parent::$data['course_progress'] = \App\Models\Programs::withCount(['grope as active_groups_count' => function($q) use ($branchId) {
            $q->where('status', 1)
              ->when($branchId, fn($sq) => $sq->where('branch_id', $branchId));
        }])
        ->where('status', 1)
        ->orderBy('active_groups_count', 'desc')
        ->take(6)
        ->get();

        return view('admin.dashboard.view', parent::$data);

    }
    public function getLang($lang)
    {
        Cache::forget('lang');
        Cache::forever('lang', $lang);
        return redirect(url()->previous());
    }
    ///////////////////////////////
    public function getProfile()
    {
        $id = Auth::user()->id;
        $user = new User();
        parent::$data['info'] = $user->getUser($id);
        return view('admin.dashboard.profile', parent::$data);
    }
    ///////////////////////////////
    public function getPassword()
    {
        $id = Auth::user()->id;
        $user = new User();
        parent::$data['info'] = $user->getUser($id);
        return view('admin.dashboard.password', parent::$data);
    }
    ///////////////////////////////
    public function postPassword(Request $request)
    {
        $id = Auth::user()->id;
        $user = new User();
        $info = $user->getUser($id);
        if ($info)
        {
            $db_password = $info->password;
            //////////////////////////////
            $old_password = $request->get('old_password');
            $password = $request->get('password');
            $password_confirmation = $request->get('password_confirmation');
            ///////////////////////////////////////////////////////////////
            $validator = Validator::make([
                'password' => $password,
                'password_confirmation' => $password_confirmation
            ], [
                'password' => 'required|between:6,16|alpha_dash|confirmed',
                'password_confirmation' => 'required|between:6,16'
            ]);
            /////////////////////////////////////////////////////
            if ($validator->fails())
            {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('dashboard.password'))->withInput();
            }
            else
            {
                if (Hash::check($old_password, $db_password))
                {
                    $save = $user->updatePassword($id, Hash::make($password));

                    if ($save)
                    {
                        Session::flash('success', 'Your Password has been changed');
                        return redirect(route('dashboard.password'));
                    }
                    else
                    {
                        Session::flash('danger', 'Sorry, an error occurred while processing your request.');
                        return redirect(route('dashboard.password'));
                    }
                }
                else
                {
                    Session::flash('danger', 'Incorrect Password');
                    return redirect(route('dashboard.password'));
                }
            }
        }
        else
        {
            Session::flash('danger', 'Sorry, an error occurred while processing your request.');
            return redirect(route('dashboard.password'));
        }
    }
}
