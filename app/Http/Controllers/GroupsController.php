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
use App\Models\Absent_Teacher;
use Illuminate\Http\Request;
use App\Models\GroupStudents;
use App\Models\Evaluate_Items;
use App\Models\GroupExamDates;
use App\Models\Absent_Student;
use App\Models\TeacherLibrary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Teacher_Evaluate_Student;
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
    public function getAttendance($group_id) {
        if (ctype_digit($group_id)) {
            $groupStudents = new GroupStudents();
            $groups = new Groups();
            parent::$data['group_info'] = $groups->getGroup($group_id);
            $group_students = $groupStudents->getGroupStudentsDegrees($group_id);
            parent::$data['data'] = $group_students;
            // dd($group_students);
            return view('frontend.teachers.absent_student', parent::$data);
        }
        echo '<center><h1>Sorry, the data could not be found</h1>';
    }

    //////////////////////////////////////////////
    public function postAttendance(Request $request, $group_id) {
        $attendanceStatus = $request['attendance'];
        $teacher_id = Auth::guard('teachers')->user()->id;
        $currentDate = Carbon::today();
        if ($attendanceStatus) {
            $isadd = Absent_Teacher::where('teacher_id', $teacher_id)->where('group_id', $group_id)->
              whereDate('days', $currentDate)->first();
            if(isset($isadd)){
                   $request->session()->flash('danger', 'تم اخذ الحضور والغياب لهذه المجموعة حاول مرة اخري في موعد اخر');
                return redirect()->route('teacher.group.attendance', $group_id);
            }
            foreach ($attendanceStatus as $student_id => $status) {
                $obj = new Absent_Student;
                $obj->student_id = $student_id;
                $obj->group_id = $group_id;
                $obj->teacher_id = $teacher_id;
                $obj->status = $status;
                $obj->days = $currentDate;
                $save = $obj->save();
            }
            $obj2 = new Absent_Teacher();
            $obj2->group_id = $group_id;
            $obj2->teacher_id = $teacher_id;
            $obj2->status = $status;
            $obj2->days = $currentDate;
            $save2 = $obj2->save();

            if ($save && $save2) {

                $x = DB::table('groups')
                                ->where('teacher_id', $teacher_id)->where('id', $group_id)->first();
                $attendance = $x->attendance;
                DB::table('groups')
                        ->where('teacher_id', $teacher_id)->where('id', $group_id)
                        ->update(['attendance' => $attendance + 1]);
                $request->session()->flash('success', self::SAVE_SUCCESS);
                return redirect()->route('teacher.group.attendance', $group_id);
            } else {

                $request->session()->flash('danger', 'حدث خطأ اثناء الاضافة');
                return redirect()->route('teacher.group.attendance', $group_id);
            }
        } else {

            $request->session()->flash('danger', 'حدث خطأ اثناء الاضافة');
            return redirect()->route('teacher.group.attendance', $group_id);
        }
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
        echo 'حدث خطا';
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
        echo 'حدث خطا';
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
        $curent_groups_infos = GroupStudents::with('group')->where('group_id', $group_id)->where('student_id', $student_id)->first();
        if ($curent_groups_infos != null) {
            parent::$data['data'] = $curent_groups_infos;
            parent::$data['group_image'] = $curent_groups_infos->group->image;

            return view('frontend.students.student_grope_info', parent::$data);
        } else {

            return response()->json(['message' => self::NOT_FOUND, 'status' => $status]);
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
            return view('frontend.teachers.teacher_grope_info', parent::$data);
        } else {

            return response()->json(['message' => self::NOT_FOUND, 'status' => $status]);
        }
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

            return response()->json(['message' => self::NOT_FOUND, 'status' => $status]);
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
            return response()->json(['message' => self::NOT_FOUND, 'status' => $status]);
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
