<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Crypt;
use Image;
use App\Models\Fees;
use App\Models\Students;
use App\Models\Pending_Data;
use App\Models\Teachers;
use App\Models\Questions;
use Illuminate\Http\Request;
use App\Models\GroupStudents;
use App\Models\GroupExamDates;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentsController extends Controller {

    const SEND_SUCCESS_MESSAGE = "تم إرسال الرسالة";
    const INSERT_SUCCESS_MESSAGE = 'site.add_student_success';
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const PASSWORD_SUCCESS = "نجاح، تم تغيير كلمة المرور بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً،لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";

    public function __construct() {
        parent::__construct();
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

        parent::$data['user'] = $user = Auth::guard('students')->user();
        $groups_array_string = implode(',', $group);
        $teacher_ids = Teachers::whereNull('deleted_at')->where('evaluations', 1)->get();

        $student = Auth::guard('students')->user();
        $count = $student->unreadNotifications->count();
        parent::$data['count'] = $count;
        parent::$data['groups_array'] = $groups_array_string;
        parent::$data['teacherStudentEvaluation'] = $teacher_ids;
        return view('frontend.students.index', parent::$data);
    }

    //////////////////////////////////////////////
    public function getEvaluate($id) {
        try {

            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
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
            $request->session()->flash('danger', self::NOT_FOUND);
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

    public function getAjaxGroupFees($group_id) {
        $student_fee = new Fees();
        $user_id = Auth::guard('students')->user()->id;
        $groupStudents = new GroupStudents();
        $data['fees'] = $student_fee->getMyGroupFees($user_id, $group_id);
        return view('frontend.students.ajax_fees', $data);
    }

    //////////////////////////////////////////////
    public function getDegrees($group_id) {
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
            $request->session()->flash('danger','User has a pending request' ); 
            return redirect(route('student.view'));
        }
        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'mobile' => ['required', Rule::unique('students')->ignore(Auth::guard('students')->user()->id)],
                    'dob' => 'require   d',
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
            $request->session()->flash('success', self::UPDATE_SUCCESS);
        } else {
            $request->session()->flash('danger', self::EXECUTION_ERROR);
        }

        return redirect(route('student.view'));
    }

//    public function postEdit(Request $request) {
//        // dd($_POST);
//        $student = new Students();
//        $user_id = Auth::guard('students')->user()->id;
//
//        $info = $student->getStudent($user_id);
//        if ($info) {
//            $mobile = $request->get('mobile');
//            $name = $request->get('name');
//            $dob = $request->get('dob');
//            $job = $request->get('job');
//            $email = $request->get('email');
//
//            if ($request->hasFile('fileToUpload')) {
//                $image = $request->file('fileToUpload');
//                //print_r("file found");die;
//                $destinationPath = 'uploads/Images/profile/';
//                $filename = 'image_' . strtotime(date("Y-m-d H:i:s")) . '.' . $image->getClientOriginalExtension();
//                Image::make($image)->resize(200, 200)->save($destinationPath . $filename);
//                $imageUrl = $destinationPath . $filename;
//                $update = $student->updateImage($user_id, $imageUrl);
//            }
//
//            $validator = Validator::make([
//                        'name' => $name,
//                        'mobile' => $mobile,
//                        'dob' => $dob,
//                        'job' => $job,
//                        'email' => $email,
//                            ], [
//                        'name' => 'required',
//                        'mobile' => ['required', Rule::unique('students')->ignore($user_id)],
//                        'dob' => 'required',
//                        'job' => 'required',
//                        'email' => 'required|email',
//            ]);
//
//            if ($validator->fails()) {
//                $request->session()->flash('danger', $validator->messages());
//                return redirect(route('student.view'));
//            } else {
//                $update = $student->updateStudentFrontEnd($info, $dob, $job, $email, $name, $mobile);
//                if ($update) {
//                    $update->update(['ask_update' => 0]);
//                    $request->session()->flash('success', self::UPDATE_SUCCESS);
//                    return redirect(route('student.view'));
//                } else {
//                    $request->session()->flash('danger', self::EXECUTION_ERROR);
//                    return redirect(route('student.view', ['id' => $encrypted_id]))->withInput();
//                }
//            }
//        }
//    }
//////////////////////////////////////////////
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
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
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
                    $request->session()->flash('danger', $validator->messages());
                    return redirect(route('student.view'))->withInput();
                } else {
                    $update = $student->updatePassword($user_id, Hash::make($password));
                    if ($update) {
                        $request->session()->flash('success', self::PASSWORD_SUCCESS);
                        return redirect(route('student.view'));
                    } else {
                        $request->session()->flash('danger', self::EXECUTION_ERROR);
                        return redirect(route('student.view'))->withInput();
                    }
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('student.view'));
        }
    }

/////////////////////////////
    public function indexStudentNotify(Request $request) {
        $student = Auth::guard('students')->user();
        $notifys = $student->notifications;

        parent::$data['notifys'] = $notifys;
        return view('frontend.students.studentNotify', parent::$data);
    }

//////////////////////////////////////////////
    public function getStudentGroueMarks(Request $request) {
        $student_id = $request->get('student_id');
        try {
            $student_id = Crypt::decrypt($student_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect()->back();
        }
// DD($student_id);
        $groups_student = GroupStudents::where('student_id', $student_id)->whereNull('deleted_at')->get();
// DD($groups_student);
        if ($groups_student != null) {
            parent::$data['data'] = $groups_student;
            return view('frontend.students.student_marks', parent::$data);
        } else {

            return response()->json(['message' => self::NOT_FOUND, 'status' => $status]);
        }
    }

//////////////////////////////////////////////
    public function getStudentGroueProgress(Request $request) {
        $student_id = $request->get('student_id');
        try {
            $student_id = Crypt::decrypt($student_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect()->back();
        }
        $groups_student = GroupStudents::where('student_id', $student_id)->whereNull('deleted_at')->get();
        if ($groups_student != null) {
            parent::$data['data'] = $groups_student;
            return view('frontend.students.group_progress', parent::$data);
        } else {
            return response()->json(['message' => self::NOT_FOUND, 'status' => $status]);
        }
    }
}
