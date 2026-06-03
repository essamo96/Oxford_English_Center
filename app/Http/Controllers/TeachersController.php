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
