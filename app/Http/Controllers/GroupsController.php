<?php

namespace App\Http\Controllers;

use DB;
use File;
use Crypt;
use Image;
use Storage;
use Carbon\Carbon;
use App\Models\Fees;
use DecryptException;
use App\Models\Groups;
use App\Models\Times;
use App\Models\Absent_Teacher;
use Illuminate\Http\Request;
use App\Models\GroupStudents;
use App\Models\Evaluate_Items;
use App\Models\Teacher_Evaluate_Student;
use App\Models\GroupExamDates;
use App\Models\Absent_Student;
use App\Models\TeacherLibrary;
use App\Models\AttendanceSetting;
use App\Services\ScheduleParser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Teacher_Evaluate_Answer;

class GroupsController extends Controller {

    const SAVE_SUCCESS = "Saved";
    const NOT_FOUND = "Sorry, the data could not be found ";

    //////////////////////////////////////////////
    public function __construct() {
        parent::__construct();
    }

    //////////////////////////////////////////////
    public function getIndex($group_id) {
        if (ctype_digit($group_id)) {
            $groupStudents = new GroupStudents();
            $groups = new Groups();
            parent::$data['group_info'] = $groups->getGroup($group_id);
            $group_students = $groupStudents->getGroupStudentsDegrees($group_id);
            parent::$data['group_students'] = $group_students;
            // dd($group_students);
            return view('frontend.groups.index', parent::$data);
        }
        echo '<center><h1>Sorry, the data could not be found</h1>';
    }

    //////////////////////////////////////////////
    /**
     * Evaluate the two hard gates for taking attendance:
     *   1) the request must originate from the centre's network (allowed IPs)
     *   2) "now" must fall within the group's scheduled lecture window (today)
     * Returns ['ok'=>bool, 'reasons'=>[], 'eval'=>scheduleEval, 'setting'=>AttendanceSetting].
     */
    private function attendanceGate($group_info, Carbon $now, Request $request) {
        $setting = AttendanceSetting::current();
        $reasons = [];

        // (1) centre network
        if ($setting->enforce_ip && !$setting->ipAllowed($request->ip())) {
            $reasons[] = 'You must be connected to the centre network to record attendance.';
        }

        // (2) lecture time window
        $time = ($group_info && $group_info->date_id) ? Times::find($group_info->date_id) : null;
        $eval = (new ScheduleParser())->evaluate(
            $time->days ?? null,
            $time->times ?? null,
            $now,
            (int) $setting->grace_minutes
        );
        if ($setting->enforce_time) {
            if ($eval['scheduled_today'] === false) {
                $reasons[] = 'Today is not a scheduled lecture day for this group.';
            } elseif ($eval['within'] === false) {
                $win = ($eval['start'] && $eval['end'])
                    ? ' (' . $eval['start']->format('h:i A') . ' - ' . $eval['end']->format('h:i A') . ')'
                    : '';
                $reasons[] = 'Attendance can only be recorded during the lecture time' . $win . '.';
            }
        }

        return ['ok' => empty($reasons), 'reasons' => $reasons, 'eval' => $eval, 'setting' => $setting];
    }

    //////////////////////////////////////////////
    public function getAttendance(Request $request, $group_id) {
        if (!ctype_digit((string) $group_id)) {
            echo '<center><h1>Sorry, the data could not be found</h1>';
            return;
        }
        $groupStudents = new GroupStudents();
        $groups = new Groups();
        $group_info = $groups->getGroup($group_id);
        parent::$data['group_info'] = $group_info;

        $now        = Carbon::now();
        $teacher_id = Auth::guard('teachers')->user()->id;
        $today      = $now->toDateString();

        // Students already marked present today + whether the teacher already opened this lecture
        $presentIds  = Absent_Student::where('group_id', $group_id)->whereDate('days', $today)
                        ->pluck('student_id')->map(fn ($x) => (int) $x)->all();
        $teacherTook = Absent_Teacher::where('teacher_id', $teacher_id)->where('group_id', $group_id)
                        ->whereDate('days', $today)->exists();
        $round = $teacherTook ? 2 : 1;

        $group_students = collect($groupStudents->getGroupStudentsDegrees($group_id));
        if ($round === 2) {
            // Round 2 → show ONLY students not yet recorded (the late-comers)
            $group_students = $group_students
                ->filter(fn ($g) => !in_array((int) $g->student_id, $presentIds, true))
                ->values();
        }
        parent::$data['data'] = $group_students;

        $gate = $this->attendanceGate($group_info, $now, $request);
        parent::$data['att_round']        = $round;
        parent::$data['att_present_count'] = count($presentIds);
        parent::$data['att_gate_ok']      = $gate['ok'];
        parent::$data['att_gate_reasons'] = $gate['reasons'];
        parent::$data['att_window']       = ($gate['eval']['start'] && $gate['eval']['end'])
            ? ($gate['eval']['start']->format('h:i A') . ' - ' . $gate['eval']['end']->format('h:i A'))
            : null;

        return view('frontend.teachers.absent_student', parent::$data);
    }

    //////////////////////////////////////////////
    public function postAttendance(Request $request, $group_id) {
        $teacher_id = Auth::guard('teachers')->user()->id;
        $now   = Carbon::now();
        $today = $now->toDateString();

        $groups = new Groups();
        $group_info = $groups->getGroup($group_id);
        if (!$group_info) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect()->route('teacher.group.attendance', $group_id);
        }

        // ---- Gates: centre network + lecture time (server-side authority) ----
        $gate = $this->attendanceGate($group_info, $now, $request);
        if (!$gate['ok']) {
            $request->session()->flash('danger', implode(' ', $gate['reasons']));
            return redirect()->route('teacher.group.attendance', $group_id);
        }

        $attendanceStatus = $request['attendance']; // [student_id => 1] for checked (present) students

        // Did the teacher already open this lecture today? → second pass = late round
        $teacherRow = Absent_Teacher::where('teacher_id', $teacher_id)
            ->where('group_id', $group_id)->whereDate('days', $today)->first();
        $round = $teacherRow ? 2 : 1;

        // Students already recorded present today (never re-record them)
        $alreadyIds = Absent_Student::where('group_id', $group_id)->whereDate('days', $today)
            ->pluck('student_id')->map(fn ($x) => (int) $x)->all();

        DB::beginTransaction();
        try {
            $saved = 0;
            if (is_array($attendanceStatus)) {
                foreach ($attendanceStatus as $student_id => $status) {
                    $sid = (int) $student_id;
                    if (in_array($sid, $alreadyIds, true)) continue; // skip duplicates / already-present
                    Absent_Student::create([
                        'student_id'  => $sid,
                        'group_id'    => $group_id,
                        'teacher_id'  => $teacher_id,
                        'status'      => 1,
                        'round'       => $round,
                        'is_late'     => $round >= 2 ? 1 : 0,
                        'days'        => $today,
                        'recorded_at' => $now,
                    ]);
                    $saved++;
                }
            }

            // Teacher attendance + lecture counter are recorded ONCE per lecture (first round only)
            if ($round === 1) {
                $lectureNo = (int) ($group_info->attendance ?? 0) + 1;
                Absent_Teacher::create([
                    'teacher_id'  => $teacher_id,
                    'group_id'    => $group_id,
                    'status'      => 1,
                    'lecture_no'  => $lectureNo,
                    'recorded_at' => $now,
                    'ip_address'  => $request->ip(),
                    'days'        => $today,
                ]);
                DB::table('groups')->where('id', $group_id)->update(['attendance' => $lectureNo]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $request->session()->flash('danger', 'An error occurred while saving: ' . $e->getMessage());
            return redirect()->route('teacher.group.attendance', $group_id);
        }

        if ($round === 2) {
            $request->session()->flash('success', 'Late arrivals recorded (' . $saved . ') without counting a new lecture for the teacher.');
        } else {
            $request->session()->flash('success', 'Attendance saved and lecture counted successfully (' . $saved . ' present).');
        }
        return redirect()->route('teacher.group.attendance', $group_id);
    }

    //////////////////////////////////////////////
    public function getEvaluate($group_id, $student_id) {
        try {

            $group_id = Crypt::decrypt($group_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect('/teacher');
        }
        try {

            $student_id = Crypt::decrypt($student_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect('/teacher');
        }
        $group_students = GroupStudents::where('student_id', $student_id)->where('group_id', $group_id)->first();
        $obj = new Evaluate_Items();
        parent::$data['student_info'] = $group_students;
        parent::$data['questions'] = $obj->getAllActiveEvaluate_Items();
        return view('frontend.teachers.teacher_evaluation', parent::$data);

        echo '<center><h1>Sorry, the data could not be found</h1>';
    }

    //////////////////////////////////////////////
    public function getGroupStd($group_id) {
        if (ctype_digit($group_id)) {
            $groupStudents = new GroupStudents();
            $groups = new Groups();
            $group_info = $groups->getGroup($group_id);
            parent::$data['group_students'] = $groupStudents->getGroupStudents($group_id);
            echo view('frontend.teachers.students', parent::$data);
        }
    }

    //////////////////////////////////////////////
    public function postIndex(Request $request, $group_id) {

        $groupStudents = new GroupStudents();
        $info = $groupStudents->getAllGroupStudents($group_id);
        if ($info) {
            $exam1_degrees = $request->get('exam1_degree');
            $exam2_degrees = $request->get('exam2_degree');
            $exam3_degrees = $request->get('exam3_degree');
            $exam4_degrees = $request->get('exam4_degree');
            $activity_degree = $request->get('activity_degree');
            $workbook_degree = $request->get('workbook_degree');
            foreach ($info as $student) {
                $groupStudents = new GroupStudents();
                $groupStudents->updateGroupStudent($student->id, "exam1_degree", $exam1_degrees[$student->student_id]);
                $groupStudents->updateGroupStudent($student->id, "exam2_degree", $exam2_degrees[$student->student_id]);
                $groupStudents->updateGroupStudent($student->id, "exam3_degree", $exam3_degrees[$student->student_id]);
                $groupStudents->updateGroupStudent($student->id, "exam4_degree", $exam4_degrees[$student->student_id]);
                $groupStudents->updateGroupStudent($student->id, "activity_degree", $activity_degree[$student->student_id]);
                $groupStudents->updateGroupStudent($student->id, "workbook_degree", $workbook_degree[$student->student_id]);

                $total = $exam1_degrees[$student->student_id] + $exam2_degrees[$student->student_id] +
                        $exam3_degrees[$student->student_id] + $exam4_degrees[$student->student_id] + $activity_degree[$student->student_id] +
                        $workbook_degree[$student->student_id];

                $groupStudents->updateGroupStudent($student->id, "total_degree", $total);
            }
            $request->session()->flash('success', self::SAVE_SUCCESS);
            return redirect(route('teacher.group.view', ['id' => $group_id]));
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teacher.group.view'));
        }
    }

    //////////////////////////////////////////////
    public function getFiles(Request $request, $group_id) {
        $groups = new Groups();
        $group_info = $groups->getGroup($group_id);
        parent::$data['dir'] = $group_info->subjects;
        return view('frontend.students.index1', parent::$data);
    }

    //////////////////////////////////////////////
    function getDirContents($dir, &$results = array()) {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = public_path($dir . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
                $results[] = $path;
            }
        }
        return $files;
    }

    //////////////////////////////////////////////
    public function getTeacherLibrary($group_id) {
        if (ctype_digit($group_id)) {
            $groups = new Groups();
            parent::$data['info'] = $groups->getGroup($group_id);
            $teacherLibraries = new TeacherLibrary();
            $teacherLibraries = $teacherLibraries->getTeacherLibrariesByGroup($group_id, 10);
            parent::$data['teacher_libraries'] = $teacherLibraries;
            return view('frontend.teacherLibraries.index', parent::$data);
        }
        echo '<center><h1>';
        echo 'An error occurred';
        echo '</h1>';
    }

    //////////////////////////////////////////////
    public function posTeacherLibrary(Request $request, $group_id) {
        $groups = new Groups();
        $info = $groups->getGroup($group_id);
        if ($info) {
            $uploadedFile = $request->file('file');
            if ($uploadedFile) {
                if ($uploadedFile->isValid()) {
                    $dir = 'File/teacherLibrary/';
                    $extension = $uploadedFile->getClientOriginalExtension();
                    $fileName = uniqid() . '_' . time() . '.' . $extension;
                    $uploadedFile->move($dir, $fileName);
                    $attach = $dir . $fileName;
                }
            }
            $update = $info->updateGroupTeacherLib($info, $attach);
            $request->session()->flash('success', self::SAVE_SUCCESS);
            return redirect(route('teacher.library.view', ['id' => $group_id]));
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teacher.library.view', ['id' => $group_id]));
        }
    }

    public function getExamDate($group_id) {
        if (ctype_digit($group_id)) {
            $groups = new Groups();
            $group = $groups->getGroup($group_id);
            if ($group) {
                parent::$data['group'] = $group;
                $examDates = new GroupExamDates();
                parent::$data['exam_dates'] = $examDates->getGroupExamDates($group_id);
                return view('frontend.teachers.date', parent::$data);
            }
        }
        echo '<center><h1>';
        echo 'An error occurred';
        echo '</h1>';
    }

    //////////////////////////////////////////////
    public function postExamDate(Request $request, $group_id) {
        $groups = new Groups();
        $info = $groups->getGroup($group_id);
        if ($info) {
            $save_data = $request->all();
            $save_data['group_id'] = $group_id;
            $examDates = new GroupExamDates();
            $exam = $examDates->getGroupExamDates($group_id);
            if ($exam) {
                $election_lists = GroupExamDates::findOrFail($exam->id);
                $election_lists->update($save_data);
            } else {
                GroupExamDates::create($save_data);
            }
            $request->session()->flash('success', self::SAVE_SUCCESS);
            return redirect(route('teacher.group.examDate', ['id' => $group_id]));
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teacher.group.examDate', ['id' => $group_id]));
        }
    }

    //////////////////////////////////////////////
    public function checkGropeCodeForStudent(Request $request) {
        $code = $request->get('input');
        $id = $request->get('student_id');
        $current_date = Carbon::now();
        try {

            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect('/student');
        }
        $data = Groups::where('code', $code)->where('code_scope', '>', Carbon::now())->first();
        // dd($data);
        if ($data == null) {
            $message = "Your Code " . $code . " Incorrect Or terminated !!";
            return response()->json(['message' => $message, 'status' => 404]);
            $status = 404;
        } else {
            $grope_id = $data->id;
            $student_grope = GroupStudents::where('student_id', $id)->where('group_id', $grope_id)->first();
            if (isset($student_grope)) {
                $message = "Ops!You are already joined in " . $student_grope->group . " group!";
                $status = 404;
                return response()->json(['message' => $message, 'status' => $status, 'data' => $data->code_scope]);
            } else {
                $obj = new GroupStudents();
                $add = $obj->addGroupStudent($id, $student_fee_total = 0, $student_book_total = 0, $grope_id);
                $message = "You Added Successfully";
                $status = 200;
                return response()->json(['message' => $message, 'status' => $status]);
            }
        }

        // return response()->json(['status' => 'error', 'message' => 'فشل الاضافة']);
    }

    //////////////////////////////////////////////
    public function postShowGroueInfo(Request $request) {
        $group_id = $request->get('group_id');
        $student_id = $request->get('student_id');
        try {
            $group_id = Crypt::decrypt($group_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect('/student');
        }
        try {
            $student_id = Crypt::decrypt($student_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect('/student');
        }
        // Hold access until the group's due payment is confirmed by administration
        $awaitingPayment = \App\Models\GroupStudentsFees::where('student_id', $student_id)
            ->where('group_id', $group_id)
            ->where('audit_status', 'pending')
            ->whereNull('deleted_at')
            ->exists();
        if ($awaitingPayment) {
            return '<div class="empty-state">'
                . '<i class="fa fa-clock-o" style="color:var(--c-warning);"></i>'
                . '<div class="empty-state__title">Awaiting payment confirmation</div>'
                . '<p style="max-width:480px;margin:0 auto;">Your payment for this group is pending confirmation by the administration. Once confirmed, you will be able to access this group\'s features.</p>'
                . '</div>';
        }

        $curent_groups_infos = GroupStudents::with('group')->where('group_id', $group_id)->where('student_id', $student_id)->first();
        if ($curent_groups_infos != null) {
            parent::$data['data'] = $curent_groups_infos;
            parent::$data['group_image'] = $curent_groups_infos->group->image;

            return view('frontend.students.student_grope_info', parent::$data);
        } else {

            return response()->json(['message' => self::NOT_FOUND, 'status' => 404], 404);
        }
    }

    //////////////////////////////////////////////
    public function postTeacherGroueInfo(Request $request) {
        $group_id = $request->get('group_id');
        $teacher_id = $request->get('teacher_id');
        try {
            $group_id = Crypt::decrypt($group_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect('/student');
        }
        try {
            $teacher_id = Crypt::decrypt($teacher_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect()->back();
        }
        $curent_groups_infos = GroupStudents::with('group')->where('group_id', $group_id)->first();
        if ($curent_groups_infos != null) {
            parent::$data['data'] = $curent_groups_infos;
            parent::$data['group_image'] = $curent_groups_infos->group->image;
            parent::$data['student_notes'] = Teacher_Evaluate_Student::with('students')
                ->where('group_id', $group_id)
                ->whereNotNull('notes')
                ->where('notes', '!=', '')
                ->orderBy('created_at', 'desc')
                ->get();
            return view('frontend.teachers.teacher_grope_info', parent::$data);
        }

        // No students seated in this group yet → friendly notice (loaded into the #Info tab as HTML)
        return '<div class="empty-state">'
             . '<i class="fa fa-users"></i>'
             . '<p style="font-weight:600;">No students are enrolled in this group yet.</p>'
             . '</div>';
    }

    //////////////////////////////////////////////
    public function postTeacherGroueStudents(Request $request, $group_id, $teacher_id) {

        try {
            $group_id = Crypt::decrypt($group_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect('/student');
        }
        try {
            $teacher_id = Crypt::decrypt($teacher_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect()->back();
        }
        $curent_groups_students = GroupStudents::whereHas('student', function ($query) {
                    $query->where('delaying', 0);
                })->where('group_id', $group_id)->get();
        if ($curent_groups_students != null) {
            parent::$data['data'] = $curent_groups_students;
            parent::$data['group_id'] = $group_id;

            return view('frontend.teachers.student_marks', parent::$data);
        } else {

            return response()->json(['message' => self::NOT_FOUND, 'status' => 404], 404);
        }
    }

    //////////////////////////////////////////////
    public function removeStudent(Request $request, $group_id, $student_id) {
        try {
            $group_id = Crypt::decrypt($group_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect('/teacher');
        }
        try {
            $student_id = Crypt::decrypt($student_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect()->back();
        }
        $removed = GroupStudents::where('group_id', $group_id)->where('student_id', $student_id)->delete();

        if ($removed) {
            $request->session()->flash('success', 'Student removed successfully!');

            return redirect()->back();
        } else {

            $request->session()->flash('danger', 'Student removed error!');
        }
    }

    //////////////////////////////////////////////
    public function getGroupExamDates(Request $request, $teacher_id) {
        $obj = new Groups();
        $teacherGropesIds = $obj->getGroupteacher($teacher_id);

        $ExamDates = GroupExamDates::whereIn('group_id', $teacherGropesIds)->get();
        if ($ExamDates != null) {
            parent::$data['data'] = $ExamDates;
            return view('frontend.teachers.group_exam', parent::$data);
        } else {
            return response()->json(['message' => self::NOT_FOUND, 'status' => 404], 404);
        }
    }

    //////////////////////////////////////////////
    public function postEvaluate(Request $request) {
        $questionIds = $request->input('question_ids');
        $evaluation_sort = $request->input('evaluation_sort');
        $group_id = $request->get('g_id');
        $student_id = $request->get('s_id');
        $teacher_id = Auth::guard('teachers')->user()->id;
        $note = $request->get('note');
        $progress = $request->get('progress');
        $total = $request->get('evaluate1_total');
        try {
            $group_id = Crypt::decrypt($group_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect('/student');
        }
        try {
            $student_id = Crypt::decrypt($student_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect()->back();
        }
        $add = Teacher_Evaluate_Student::create([
                    'teacher_id' => $teacher_id,
                    'student_id' => $student_id,
                    'group_id' => $group_id,
                    'progress' => $progress,
                    'notes' => $note,
                    'total' => $total,
                    'evaluation_sort' => $evaluation_sort,
        ]);
        if ($add) {
            $evaluate_id = $add->id;
            foreach ($questionIds as $questionId) {
                $answers = $request->input("evaluate_degree.{$questionId}");
                foreach ($answers as $answer) {
                    $add = Teacher_Evaluate_Answer::create([
                                'question_id' => $questionId,
                                'answer' => $answer,
                                'evaluate_id' => $evaluate_id,
                    ]);
                }
            }
        }
        if ($add) {
            $x = DB::table('group_students')
                    ->where('student_id', $student_id)->where('group_id', $group_id)
                    ->update(['has_evaluation' => 1, 'evaluation_at' => Carbon::now(), 'progress' => $progress]);
            DB::table('groups')
                    ->where('teacher_id', $teacher_id)->where('id', $group_id)
                    ->update([
                        'progress' => $progress,
                        'progress_at' => Carbon::now(),
            ]);
            return redirect(route('teacher.showGroueStudents', ['group_id' => Crypt::encrypt($group_id), 'teacher_id' => Crypt::encrypt($teacher_id)]))->with('success', 'Evaluation saved successfully!');
        } else {
            return redirect(route('teacher.showGroueStudents', ['group_id' => Crypt::encrypt($group_id), 'teacher_id' => Crypt::encrypt($teacher_id)]))->with('danger', 'Evaluation saved error!');
        }
    }

    public function evaluate(Group $group) {
        $group->update(['has_evaluation' => 1]);

        return redirect()->back();
    }
}
