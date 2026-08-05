<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\Students;
use App\Models\Teachers;
use App\Models\Teachers_Admin_Messages;
use App\Models\GroupStudents;
use App\Models\Closed_Classes;
use Illuminate\Http\Request;
use App\Models\Groups;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Image;

class TeachersController extends Controller {

    const SEND_SUCCESS_MESSAGE = "Message sent successfully";
    const INSERT_SUCCESS_MESSAGE = 'site.add_student_success';
    const UPDATE_SUCCESS = "Updated successfully";
    const DELETE_SUCCESS = "Deleted successfully";
    const PASSWORD_SUCCESS = "Password changed successfully";
    const EXECUTION_ERROR = "Sorry, an error occurred while processing the request";
    const NOT_FOUND = "Sorry, the requested data could not be found";
    const ACTIVATION_SUCCESS = "Activated successfully";
    const DISABLE_SUCCESS = "Disabled successfully";
    
    public function __construct() {
        parent::__construct();
    }

    /**
     * The logged-in teacher's salary history (forms + per-lecture details) — loaded into a
     * modal when the teacher clicks their profile photo on the dashboard.
     */
    public function mySalaries() {
        $teacherId = Auth::guard('teachers')->user()->id;
        $forms = \App\Models\TeacherSalaryForm::with(['details.group'])
            ->where('teacher_id', $teacherId)
            ->orderByDesc('year')->orderByDesc('month')
            ->get();
        return view('frontend.teachers.my_salaries', ['forms' => $forms]);
    }

    public function getIndex() {

        $teacher = new Teachers();
        $groups = new Groups();
        $groupStudents = new GroupStudents();
        $user_id = Auth::guard('teachers')->user()->id;
        parent::$data['teacher_info'] = $teacher->getTeacher($user_id);
        $teacher_groups = $groups->getGroupByTeacher($user_id);
        parent::$data['groups'] = $teacher_groups;
        // dd(parent::$data['groups']);
        foreach ($teacher_groups as $teacher_group){
            $studentsCount = $groupStudents->countStudentGroup($teacher_group->id);
            $teacher_group->studentsCount = $studentsCount;
        }
        $groups_array="";
        foreach ($teacher_groups as $item){
            $groups_array .= $item->id;
           if(!$teacher_groups->last() == $item)
            $groups_array .= ",";
        }
        $teacher = Auth::guard('teachers')->user();
        $count = $teacher->unreadnotifications->count();
        parent::$data['count'] = $count;
        parent::$data['teacher_id'] = $user_id;
        parent::$data['groups_array'] = $groups_array;

        // ---------------- Dashboard KPIs / charts / activity (real data) ----------------
        $gids = $teacher_groups->pluck('id')->all();

        $allGroupStudents = !empty($gids)
            ? \App\Models\GroupStudents::whereIn('group_id', $gids)->whereNull('deleted_at')->get()
            : collect();

        $totalStudents = $allGroupStudents->pluck('student_id')->unique()->count();
        $activeCourses = $teacher_groups->count();
        $pendingGrade  = $allGroupStudents->where('has_evaluation', 0)->count();
        $gradedTotals  = $allGroupStudents->whereNotNull('total_degree')->pluck('total_degree');
        $avgScore      = $gradedTotals->count() ? round($gradedTotals->avg(), 1) : 0;
        $avgProgress   = $allGroupStudents->count() ? (int) round($allGroupStudents->avg('progress') ?: 0) : 0;

        // Finished Groups count
        $finishedGroupsCount = \App\Models\Groups::where('teacher_id', $user_id)
            ->where('status', 0)
            ->whereNull('deleted_at')
            ->count();

        // Financial Stats
        $salaryStats = \App\Models\TeacherSalaryForm::where('teacher_id', $user_id)
            ->where('is_received', 1)
            ->selectRaw('count(id) as total_received_forms, sum(net_amount) as total_amount')
            ->first();

        parent::$data['financials'] = [
            'total_forms' => $salaryStats->total_received_forms ?? 0,
            'total_amount' => $salaryStats->total_amount ?? 0,
        ];

        // Weekly Schedule for active groups
        $teacher_groups_with_time = \App\Models\Groups::with(['ctime', 'program'])
            ->where('teacher_id', $user_id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->get();

        $weeklySchedule = [
            'saturday' => [],
            'sunday' => [],
            'monday' => [],
            'tuesday' => [],
            'wednesday' => [],
            'thursday' => [],
            'friday' => [],
        ];

        $dayMap = [
            'السبت'     => 'saturday',
            'الأحد'     => 'sunday',
            'الاحد'     => 'sunday',
            'الاثنين'   => 'monday',
            'الإثنين'   => 'monday',
            'الثلاثاء'  => 'tuesday',
            'الأربعاء'  => 'wednesday',
            'الاربعاء'  => 'wednesday',
            'الخميس'    => 'thursday',
            'الجمعة'    => 'friday',
            'saturday'  => 'saturday',
            'sunday'    => 'sunday',
            'monday'    => 'monday',
            'tuesday'   => 'tuesday',
            'wednesday' => 'wednesday',
            'thursday'  => 'thursday',
            'friday'    => 'friday',
        ];

        foreach ($teacher_groups_with_time as $group) {
            if ($group->ctime && $group->ctime->days && $group->ctime->times) {
                $daysStr = $group->ctime->days;
                $timesStr = $group->ctime->times;
                $isDaily = (mb_strpos($daysStr, 'يومي') !== false || stripos($daysStr, 'daily') !== false);

                foreach ($dayMap as $label => $canonical) {
                    $found = false;
                    if ($isDaily) {
                        $found = true;
                    } else {
                        if (preg_match('/[ء-ي]/u', $label)) {
                            $found = mb_strpos($daysStr, $label) !== false;
                        } else {
                            $found = stripos($daysStr, $label) !== false;
                        }
                    }
                    if ($found) {
                        $weeklySchedule[$canonical][] = [
                            'id' => $group->id,
                            'group_name' => $group->name,
                            'program_title' => optional($group->program)->title ?? 'N/A',
                            'times' => $timesStr,
                            'zoom' => $group->zoom,
                            'drive' => $group->drive,
                        ];
                    }
                }
            }
        }

        // Deduplicate events per day
        foreach ($weeklySchedule as $day => $events) {
            $temp = [];
            foreach ($events as $ev) {
                $key = $ev['id'] . '|' . $ev['times'];
                $temp[$key] = $ev;
            }
            $weeklySchedule[$day] = array_values($temp);
        }

        parent::$data['weeklySchedule'] = $weeklySchedule;

        parent::$data['kpis'] = [
            'total_students' => $totalStudents,
            'active_courses' => $activeCourses,
            'pending_grade'  => $pendingGrade,
            'avg_progress'   => $avgProgress,
            'avg_score'      => $avgScore,
            'finished_courses' => $finishedGroupsCount,
        ];

        // chart 1: student performance distribution (by total_degree)
        $buckets = ['0-49' => 0, '50-69' => 0, '70-84' => 0, '85-100' => 0];
        foreach ($allGroupStudents as $row) {
            if ($row->total_degree === null) continue;
            $t = (float) $row->total_degree;
            if ($t < 50)      $buckets['0-49']++;
            elseif ($t < 70)  $buckets['50-69']++;
            elseif ($t < 85)  $buckets['70-84']++;
            else              $buckets['85-100']++;
        }
        // chart 2: enrollment per course (studentsCount is an array of ids)
        parent::$data['charts'] = [
            'distribution' => ['labels' => array_keys($buckets), 'data' => array_values($buckets)],
            'enrollment'   => [
                'labels' => $teacher_groups->map(fn ($g) => $g->name)->values(),
                'data'   => $teacher_groups->map(fn ($g) => is_array($g->studentsCount) ? count($g->studentsCount) : (int) $g->studentsCount)->values(),
            ],
        ];

        // needs grading (last 5 ungraded)
        parent::$data['needsGrading'] = !empty($gids)
            ? \App\Models\GroupStudents::with(['student', 'group'])->whereIn('group_id', $gids)
                ->where('has_evaluation', 0)->whereNull('deleted_at')
                ->orderByDesc('created_at')->limit(5)->get()
            : collect();

        // recently active students (attendance) — Absent_Student has no relations, resolve via maps
        $recentRows = !empty($gids)
            ? \App\Models\Absent_Student::whereIn('group_id', $gids)->orderByDesc('recorded_at')->limit(5)->get()
            : collect();
        $studentsMap = \App\Models\Students::whereIn('id', $recentRows->pluck('student_id')->unique()->all())->pluck('name', 'id');
        $groupsMap   = \App\Models\Groups::whereIn('id', $recentRows->pluck('group_id')->unique()->all())->pluck('name', 'id');
        parent::$data['recentActive'] = $recentRows->map(function ($a) use ($studentsMap, $groupsMap) {
            return [
                'student' => $studentsMap[$a->student_id] ?? ('#' . $a->student_id),
                'group'   => $groupsMap[$a->group_id] ?? 'Group',
                'date'    => $a->recorded_at ?: $a->days,
                'status'  => $a->status,
            ];
        });

        // Examination Center KPIs for this teacher's own groups only
        $teacherExams = \App\Models\Exam::where('category', 'group')->whereIn('group_id', $gids)->get();
        $teacherExamIds = $teacherExams->pluck('id');
        parent::$data['exam_kpis'] = [
            'total_exams' => $teacherExams->count(),
            'published_exams' => $teacherExams->where('status', 'published')->count(),
            'pending_reviews' => \App\Models\ExamAttempt::whereIn('exam_id', $teacherExamIds)->where('status', 'submitted')->count(),
            'upcoming_exams' => $teacherExams->filter(fn($e) => $e->start_date && $e->start_date->isFuture())->count(),
            'avg_score' => round(\App\Models\ExamAttempt::whereIn('exam_id', $teacherExamIds)->whereNotNull('percentage')->avg('percentage') ?? 0, 1),
        ];
        parent::$data['exam_recent_attempts'] = \App\Models\ExamAttempt::with(['exam', 'student'])
            ->whereIn('exam_id', $teacherExamIds)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('frontend.teachers.index', parent::$data);
    }
    public function getIndex2($id) {
        $info = Groups::where('teacher_id', $id)->whereNull('deleted_at')->get();
        parent::$data['info'] = $info;
        return view('frontend.teachers.progress', parent::$data);
    }

    public function postIndex() {
        echo 'in';
        exit;
        $teacher = new Teachers();
        $user_id = Auth::guard('teachers')->user()->id;
        parent::$data['teacher_info'] = $teacher->getTeacher($user_id);

        return view('frontend.teachers.success', parent::$data);
    }
    
    //////////////////////////////////////////////
    public function postEdit(Request $request) {
        $teacher = new Teachers();
        $user_id = Auth::guard('teachers')->user()->id;

        $info = $teacher->getTeacher($user_id);
        if ($info) {
            $dob = $request->get('dob');
            $email = $request->get('email');

            if ($request->hasFile('fileToUpload')) {
                $image = $request->file('fileToUpload');
                $destinationPath = 'uploads/Images/profile/';
                $filename = 'image_' . strtotime(date("Y-m-d H:i:s")) . '.' . $image->getClientOriginalExtension();
                Image::make($image)->resize(200, 200)->save($destinationPath . $filename);
                $imageUrl = $destinationPath . $filename;
                $update = $teacher->updateImage($user_id, $imageUrl);
            }
            
            $validator = Validator::make([
                        'dob' => $dob,
                        'email' => $email,
                            ], [
                        'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('teacher.view'));
            } else {
                $update = $teacher->updateTeacherFrontEnd($info, $dob, $email);
                if ($update) {
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('teacher.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('teacher.view', ['id' => $encrypted_id]))->withInput();
                }
            }
        }
    }
    
    //////////////////////////////////////////////
    public function postPassword(Request $request)
    {
        $teacher = new Teachers();
        $user_id = Auth::guard('teachers')->user()->id;
        
        $info = $teacher->getTeacher($user_id);
        if ($info)
        {
            //print_r("user found");die;
            $password = $request->get('npassword');
            $password_confirmation = $request->get('rpassword');
            
           if ($request->hasFile('fileToUpload')){
                $image = $request->file('fileToUpload');
                //print_r("file found");die;
                $destinationPath = 'uploads/Images/profile/';
                $filename = 'image_' . strtotime(date("Y-m-d H:i:s")) . '.' . $image->getClientOriginalExtension();
                Image::make($image)->resize(200, 200)->save($destinationPath . $filename);
                $imageUrl =  $destinationPath . $filename;
                $update =$teacher->updateImage($user_id, $imageUrl);
                if(!$password){
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('teacher.view'));
                }
            }
              
            if($password){     
                $validator = Validator::make([
                    'password' => $password,
                    'password_confirmation' => $password_confirmation
                ], [
                    'password' => 'required|between:6,16|confirmed',
                    'password_confirmation' => 'required|between:6,16'
                ]);

                if ($validator->fails()){
                    $request->session()->flash('danger', $validator->messages());
                    return redirect(route('teacher.view', ['id' => $encrypted_id]))->withInput();
                }
                else{
                    $update =$teacher->updatePassword($user_id, Hash::make($password));
                    if ($update){
                        $request->session()->flash('success', self::PASSWORD_SUCCESS);
                        return redirect(route('teacher.view'));
                    }
                    else{
                        $request->session()->flash('danger', self::EXECUTION_ERROR);
                        return redirect(route('teacher.view', ['id' => $encrypted_id]))->withInput();
                    }
                }
            }
        }
        else
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teacher.view'));
        }
    }
    //////////////////////////////////////////////
    public function TeachersSendMessage(Request $request)
    {
        // dd($request->json()->all());
        $jsonPayload = $request->json()->all();
        $title = $jsonPayload['title'];
        $body = $jsonPayload['body'];
        $id  = Auth::guard('teachers')->user()->id;
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'body' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first()
            ], 422);
        } else {
            $opj = new Teachers_Admin_Messages();
            $add = $opj->SaveMessages($title, $body, $id);
            if ($add) {

                return response()->json(['status' => 'success', 'message' => 'Done Send successfully !']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'something error!!']);
            }
        }
    }
    /////////////////////////////
    public function indexTeacherNotify(Request $request)
    {
        $teacher = Auth::guard('teachers')->user();
        $notifys = $teacher->notifications;
        // $notifys = $student->notifications->take(7)->get();

        $data['notifys'] = $notifys;
        return view('frontend.teachers.teacherNotify', $data);
        //to get unread notification we can use this $student->unreadnotifications->count(); // $notification->makAsRead(); this make all notification read // $notification->read_at;
        // $notification->created_at->diffForHumans(); 
    }


    public function postCloseClass(Request $request)
    {
        $closedDate = $request->input('end_date');
        $groupId = $request->input('group_id');
        $CurrentDate = Carbon::now()->toDateString();
        $teacher_id = Auth::guard('teachers')->user()->id;

        if ($closedDate > $CurrentDate) {
            return response()->json(['error' => 'The closing date is earlier than the course closing date.'], 400);
        }else{
         $obj = new Groups();
         $updateStatus = $obj->updateStatus($groupId, 0);

            $closedClass = new Closed_Classes();
            $closedClass->closed_date = $closedDate;
            $closedClass->teacher_id = $teacher_id;
            $closedClass->group_id = $groupId;
            $closedClass->save();
            $groupInfos = $obj->getGroup($groupId);

            $recipientEmail = 'almasri.ibrahim@oxford.ps'; //almasri.ibrahim@oxford.ps
            $subject = 'Class '. $groupInfos->name.' Closed';
            $message = 'The class has been closed , by ' .$groupInfos->teacher->name. ' at : '. $CurrentDate.'';

            Mail::raw($message, function ($mail) use ($recipientEmail, $subject) {
                $mail->to($recipientEmail)
                    ->subject($subject);
            });

            return response()->json(['success' => 'The operation was successfully completed..']);
        }

    }

}
