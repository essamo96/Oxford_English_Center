<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Intervention\Image\Facades\Image;
use App\Models\Fees;
use App\Models\Students;
use App\Models\Pending_Data;
use App\Models\Teachers;
use App\Models\Questions;
use Illuminate\Http\Request;
use App\Models\GroupStudents;
use App\Models\GroupExamDates;
use App\Models\Message;
use App\Models\Students_Admin_Messages;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentsController extends Controller {

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

    public function getCoursesPartial() {
        $student_groups_model = new GroupStudents();
        $user_id = Auth::guard('students')->user()->id;
        $student_groups = $student_groups_model->getStudentGroup($user_id)->filter(function($item) {
            return $item->group !== null;
        });
        
        return view('frontend.students.courses_partial', ['student_groups' => $student_groups]);
    }

    public function getIndex() {
        $student = new Students();
        $student_groups = new GroupStudents();
        $user_id = Auth::guard('students')->user()->id;
        parent::$data['student_info'] = $student->getStudent($user_id);
        // dd(parent::$data['student_info']);
        $studentGroups = $student_groups->getStudentGroup($user_id)->filter(function($item) {
            return $item->group !== null;
        });

        $studentGroupsName = $student_groups->getStudentGroupName($user_id);
        $group = $studentGroups->pluck('group_id')->all();
        
        $studentGropesExamday = GroupExamDates::whereIn('group_id', $group)->get();

        parent::$data['student_groups'] = $studentGroups;
        parent::$data['studentGropesExamday'] = $studentGropesExamday;

        // Groups the student was seated in but whose due payment is NOT yet confirmed
        // (a pending financial order exists). Access to such a group is held until confirmed.
        parent::$data['pendingPaymentGroupIds'] = \App\Models\GroupStudentsFees::where('student_id', $user_id)
            ->whereNotNull('group_id')
            ->where('audit_status', 'pending')
            ->whereNull('deleted_at')
            ->pluck('group_id')->map(fn ($g) => (int) $g)->unique()->values()->all();

        parent::$data['user'] = $user = Auth::guard('students')->user();
        $groups_array_string = implode(',', $group);
        $teacher_ids = Teachers::whereNull('deleted_at')->where('evaluations', 1)->get();

        $student = Auth::guard('students')->user();
        $count = $student->unreadNotifications->count();
        parent::$data['count'] = $count;
        parent::$data['groups_array'] = $groups_array_string;
        parent::$data['teacherStudentEvaluation'] = $teacher_ids;

        // ---------------- Dashboard KPIs / charts / activity (real data) ----------------
        $enrolled   = $studentGroups->count();
        $completed  = $studentGroups->filter(function ($g) {
            return ($g->progress !== null && $g->progress >= 100) || !empty($g->cer_code);
        })->count();
        $inProgress = max($enrolled - $completed, 0);
        $avgProgress = $enrolled ? (int) round($studentGroups->avg('progress') ?: 0) : 0;
        $gradedTotals = $studentGroups->filter(fn ($g) => $g->total_degree !== null)->pluck('total_degree');
        $avgScore = $gradedTotals->count() ? round($gradedTotals->avg(), 1) : 0;

        parent::$data['kpis'] = [
            'enrolled'     => $enrolled,
            'completed'    => $completed,
            'in_progress'  => $inProgress,
            'avg_progress' => $avgProgress,
            'avg_score'    => $avgScore,
        ];

        // chart 1: progress per course
        $progressChart = [
            'labels' => $studentGroups->map(fn ($g) => optional($g->group)->name ?? 'N/A')->values(),
            'data'   => $studentGroups->map(fn ($g) => (int) ($g->progress ?: 0))->values(),
        ];
        // chart 2: grade trend (average of each assessment across courses)
        $assessments = ['exam1_degree' => 'P.T 1', 'exam2_degree' => 'P.T 2', 'exam3_degree' => 'P.T 3', 'exam4_degree' => 'Final'];
        $trendData = [];
        foreach (array_keys($assessments) as $field) {
            $vals = $studentGroups->filter(fn ($g) => $g->$field !== null)->pluck($field);
            $trendData[] = $vals->count() ? round($vals->avg(), 1) : 0;
        }
        parent::$data['charts'] = [
            'progress' => $progressChart,
            'trend'    => ['labels' => array_values($assessments), 'data' => $trendData],
        ];

        // recent activity (proxy): latest teacher evaluations of this student
        $recentEvals = \App\Models\Teacher_Evaluate_Student::where('student_id', $user_id)
            ->orderByDesc('created_at')->limit(5)->get();
        parent::$data['recent'] = $recentEvals->map(function ($e) use ($studentGroups) {
            $gname = optional(optional($studentGroups->firstWhere('group_id', $e->group_id))->group)->name ?? 'Course';
            return ['group' => $gname, 'date' => $e->created_at, 'total' => $e->total, 'progress' => $e->progress];
        });

        // upcoming exam deadlines (real dates from GroupExamDates)
        $upcoming = [];
        foreach ($studentGropesExamday as $ed) {
            $gname = optional(optional($studentGroups->firstWhere('group_id', $ed->group_id))->group)->name ?? 'Course';
            foreach (['progress_test1' => 'P.T 1', 'progress_test2' => 'P.T 2', 'progress_test3' => 'P.T 3', 'final_exam' => 'Final Exam'] as $field => $label) {
                if (!empty($ed->$field)) {
                    try { $d = \Carbon\Carbon::parse($ed->$field); } catch (\Exception $e) { continue; }
                    if ($d->isFuture()) {
                        $upcoming[] = ['label' => $label, 'group' => $gname, 'date' => $d];
                    }
                }
            }
        }
        usort($upcoming, fn ($a, $b) => $a['date'] <=> $b['date']);
        parent::$data['upcoming'] = array_slice($upcoming, 0, 5);

        return view('frontend.students.index', parent::$data);
    }

    //////////////////////////////////////////////
    public function getEvaluate(Request $request, string $id) {
        try {

            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect('/teacher');
        }
        $teacher = Teachers::find($id);
        parent::$data['questions'] = Questions::whereNull('deleted_at')->where('status', 1)->get();
        parent::$data['teacher'] = $teacher;
        // dd(parent::$data['group_students']);
        return view('frontend.students.teacher_evaluation', parent::$data);

        echo '<center><h1>Sorry, the data could not be found</h1>';
    }

    public function updateProfile(Request $request) {
        $id = $request->input('id');
        try {

            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect('/student');
        }
        $student = Students::find($id);

        if ($student) {
            $student->update(['ask_update' => 1]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function postIndex() {
        echo 'in';
        exit;
        $student = new Students();
        $student_groups = new GroupStudents();
        $user_id = Auth::guard('students')->user()->id;
        parent::$data['student_info'] = $student->getStudent($user_id);
        parent::$data['student_groups'] = $student_groups->getStudentGroup($user_id);

        return view('frontend.students.success', parent::$data);
    }

    public function getAjaxGroupFees(string $group_id) {
        $student_fee = new Fees();
        $user_id = Auth::guard('students')->user()->id;
        $groupStudents = new GroupStudents();
        $data['fees'] = $student_fee->getMyGroupFees($user_id, $group_id);
        return view('frontend.students.ajax_fees', $data);
    }

    //////////////////////////////////////////////
    public function getDegrees(string $group_id) {
        //print_r($group_id);die;
        $studentGroup = new GroupStudents();
        parent::$data['student_group'] = $studentGroup->getStudent($group_id);
        return view('frontend.students.degrees', parent::$data);
    }

    //////////////////////////////////////////////
    public function postEdit(Request $request) {
//        dd($request->all());
        $student = new Pending_Data();
        $user_id = Auth::guard('students')->user()->id;
        $info = $student->getStudentPending_Data($user_id);
        
        if (!empty($info)) {
            session()->flash('danger','User has a pending request' ); 
            return redirect(route('student.view'));
        }
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'mobile' => ['required', Rule::unique('students')->ignore(Auth::guard('students')->user()->id)],
                    'dob' => 'required',
                    'job' => 'required',
                    'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect(route('student.view'))
                            ->withErrors($validator)
                            ->withInput();
        }

        $user_id = Auth::guard('students')->user()->id;

        if ($request->hasFile('fileToUpload')) {
            $image = $request->file('fileToUpload');
            $destinationPath = 'uploads/Images/profile/';
            $filename = 'image_' . strtotime(date("Y-m-d H:i:s")) . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(200, 200)->save(public_path($destinationPath . $filename)); 
            $imageUrl = $destinationPath . $filename;
            $request->merge(['fileToUpload' => $imageUrl]); 
        } else {
            $imageUrl = null; 
        }
        $data = $request->all();
        $data['student_id'] = $user_id;
        $data['ask_update'] = 0;
        $data['fileToUpload'] = $imageUrl;
        $add = Pending_Data::create($data);

        if ($add) {
            $student = new Students();
            $info = $student->getStudent($user_id);
            $info->update(['ask_update' => 1]);
            session()->flash('success', self::UPDATE_SUCCESS);
        } else {
            session()->flash('danger', self::EXECUTION_ERROR);
        }

        return redirect(route('student.view'));
    }

    public function postPassword(Request $request) {
        $student = new Students();
        $user_id = Auth::guard('students')->user()->id;

        $info = $student->getStudent($user_id);
        if ($info) {
            $password = $request->get('npassword');
            $password_confirmation = $request->get('rpassword');

            if ($request->hasFile('fileToUpload')) {
                $image = $request->file('fileToUpload');
                $destinationPath = 'uploads/Images/profile/';
                $filename = 'image_' . strtotime(date("Y-m-d H:i:s")) . '.' . $image->getClientOriginalExtension();
                Image::make($image)->resize(200, 200)->save($destinationPath . $filename);
                $imageUrl = $destinationPath . $filename;
                $update = $student->updateImage($user_id, $imageUrl);
                if (!$password) {
                    session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('student.view'));
                }
            }

            if ($password) {
                $validator = Validator::make([
                            'password' => $password,
                            'password_confirmation' => $password_confirmation
                                ], [
                            'password' => 'required|between:6,16|confirmed',
                            'password_confirmation' => 'required|between:6,16'
                ]);

                if ($validator->fails()) {
                    session()->flash('danger', $validator->messages());
                    return redirect(route('student.view'))->withInput();
                } else {
                    $update = $student->updatePassword($user_id, Hash::make($password));
                    if ($update) {
                        session()->flash('success', self::PASSWORD_SUCCESS);
                        return redirect(route('student.view'));
                    } else {
                        session()->flash('danger', self::EXECUTION_ERROR);
                        return redirect(route('student.view'))->withInput();
                    }
                }
            }
        } else {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('student.view'));
        }
    }

/////////////////////////////
    public function indexStudentNotify(Request $request) {
        $student = Auth::guard('students')->user();
        $notifys = $student->notifications;
        
        // Fetch group messages (last 20 across all student groups)
        $groupIds = GroupStudents::where('student_id', $student->id)->pluck('group_id')->toArray();
        $group_messages = Message::whereIn('group_id', $groupIds)
            ->with(['chatGroup.teacher', 'chatGroup.program'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Fetch Admin Messages (direct correspondence)
        $admin_messages = Students_Admin_Messages::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        parent::$data['notifys'] = $notifys;
        parent::$data['group_messages'] = $group_messages;
        parent::$data['admin_messages'] = $admin_messages;
        parent::$data['student_groups'] = GroupStudents::where('student_id', $student->id)->with('group')->get();
        
        return view('frontend.students.studentNotify', parent::$data);
    }

    public function postSendMessageToAdmin(Request $request) {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required'
        ]);

        $student = Auth::guard('students')->user();
        $msg = new Students_Admin_Messages();
        $msg->student_id = $student->id;
        $msg->title = $request->title;
        $msg->content = $request->content;
        $msg->seen = 0;
        $msg->save();

        return response()->json(['status' => 'success', 'message' => 'Message sent to administration successfully.']);
    }

//////////////////////////////////////////////
    public function getStudentGroueMarks(Request $request) {
        $student_id = Auth::guard('students')->user()->id;
        
        $groups_student = GroupStudents::where('student_id', $student_id)->whereNull('deleted_at')->get();
        if ($groups_student->count() > 0) {
            parent::$data['data'] = $groups_student;
            return view('frontend.students.student_marks', parent::$data);
        } else {
            parent::$data['data'] = collect();
            return view('frontend.students.student_marks', parent::$data);
        }
    }

//////////////////////////////////////////////
    public function getStudentGroueProgress(Request $request) {
        $student_id = Auth::guard('students')->user()->id;

        $groups_student = GroupStudents::where('student_id', $student_id)->whereNull('deleted_at')->get();
        if ($groups_student->count() > 0) {
            parent::$data['data'] = $groups_student;
            return view('frontend.students.group_progress', parent::$data);
        } else {
            parent::$data['data'] = collect();
            return view('frontend.students.group_progress', parent::$data);
        }
    }
}
