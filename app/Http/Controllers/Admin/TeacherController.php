<?php

namespace App\Http\Controllers\Admin;

use App\Models\Teachers;
use Illuminate\Http\Request;
use App\Notifications\newAdminCreatedNotification;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
////////////////////////////////////
use App\Models\Teachers_Admin_Messages;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\SMS;
// use AdminController;

class TeacherController extends AdminController {

    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بتجاح";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const PASSWORD_SUCCESS = "نجاح، تم تغيير كلمة المرور بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً،لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";

    //////////////////////////////////////////////
    public function __construct() {
        parent::__construct();
        parent::$data['active_menu'] = 'teachers';
    }

    //////////////////////////////////////////////
    public function getIndex() {
        return view('admin.teachers.view', parent::$data);
    }

    //////////////////////////////////////////////
    public function getList(Request $request) {
        $title = $request->get('title', NULL);
        $activeT = $request->get('activeT',NULL);
        $teachers = new Teachers();
        $info = $teachers->getSearchTeachers($title,$activeT);
        $datatable = Datatables::of($info);

        $datatable->editColumn('company_name', function ($row) {
            return (!empty($row->company_name) ? $row->company_name : 'N/A');
        });

        $datatable->editColumn('status', function ($row) {
            $data['X'] =1;
            $data['id'] = $row->id;
            $data['status'] = $row->status;
            return view('admin.teachers.parts.status', $data)->render();
        });
        $datatable->editColumn('evaluations', function ($row) {
            $data['X'] =2;
            $data['id'] = $row->id;
            $data['status'] = $row->evaluations;
            return view('admin.teachers.parts.status', $data)->render();
        });
        $datatable->editColumn('checkbox', function ($row) {
            if (!empty($row->mobile)) {
                $mobile = $row->mobile;
                $id = $row->id;
                return '<div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                            <div class="form-check form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input select-checkbox checkboxes h-20px w-20px" name="mobile[' . $id . ']" value="' . $mobile . '" data-mob="' . $mobile . '" />
                            </div>
                        </div>';
            }
            return '';
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.teachers.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getAdd() {
        return view('admin.teachers.add', parent::$data);
    }

    ////////////////////////////////////////////
    public function postAdd(Request $request) {
        $name = $request->get('name');
        $mobile = $request->get('mobile');
        $dob = $request->get('dob');
        $email = $request->get('email');
        $join_date = $request->get('join_date');
        $cv = $request->get('cv');
        $image = $request->get('image');
        $status = (int) $request->get('status');
        $username = $this->split_myString($mobile);
        $password = $this->split_myString($mobile);
        $validator = Validator::make([
                    'name' => $name,
                    'mobile' => $mobile,
                    'email' => $email,
                    'status' => $status,
                    'image' => $image
                        ], [
                    'name' => 'required',
                    'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|unique:teachers|digits:10',
                    'email' => 'required|email',
                    'status' => 'required|numeric|in:0,1'
        ]);
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('teachers.add'))->withInput();
        } else {
            $teachers = new Teachers();
            $add = $teachers->addTeacher($name, $username, Hash::make($password), $mobile, $dob, $email, $join_date, $cv, $status, $image);
            if ($add) {
                $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route('teachers.view'));
            } else {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('teachers.add'))->withInput();
            }
        }
    }

    //////////////////////////////////////////////
    public function getEdit(Request $request, $id) {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('news.view'));
        }
        //////////////////////////////////////////////
        $teachers = new Teachers();
        $info = $teachers->getTeacher($id);
        if ($info) {
            parent::$data['info'] = $info;
            return view('admin.teachers.edit', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teachers.view'));
        }
    }

    ////////////////////////////////////////////////
    public function postEdit(Request $request, $id) {
        try {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('pages.view'));
        }
        /////////////////////////////
        $teachers = new Teachers();
        $info = $teachers->getTeacher($id);
        if ($info) {
            $name = $request->get('name');
            $mobile = $request->get('mobile');
            $dob = $request->get('dob');
            $email = $request->get('email');
            $join_date = $request->get('join_date');
            $cv = $request->get('cv');
            $image = $request->get('image');
            $status = (int) $request->get('status');
            $username = $this->split_myString($mobile);
            $password = $this->split_myString($mobile);
            
            $validator = Validator::make([
                        'name' => $name,
                        'mobile' => $mobile,
                        'email' => $email,
                'image' => $image,
                        'status' => $status
                            ], [
                        'name' => 'required',
                        'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|digits:10|unique:teachers,mobile,' . $id,
                        'email' => 'required|email',
                        'status' => 'required|numeric|in:0,1'
            ]);

            //////////////////////////////////////////////////////////
            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('teachers.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                $update = $teachers->updateTeacher($info, $name, $username, Hash::make($password), $mobile, $dob, $email, $join_date, $cv, $status, $image);
                if ($update) {
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('teachers.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('teachers.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teachers.view'));
        }
    }

    ////////////////////////////////////////////////
    public function postDelete(Request $request) {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $teachers = new Teachers();
        $info = $teachers->getTeacher($id);
        if ($info) {
            $delete = $teachers->deleteTeacher($info);
            if ($delete) {
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            } else {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

    //////////////////////////////////////////////
    public function postEvaluations(Request $request) {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $teachers = new Teachers();
        $info = $teachers->getTeacher($id);
        if ($info) {
            $status = $info->evaluations;
            if ($status == 0) {
                $update = $teachers->updateEvaluations($id, 1);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $teachers->updateEvaluations($id, 0);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }
    //////////////////////////////////////////////
    public function postStatus(Request $request) {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $teachers = new Teachers();
        $info = $teachers->getTeacher($id);
        if ($info) {
            $status = $info->status;
            if ($status == 0) {
                $update = $teachers->updateStatus($id, 1);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $teachers->updateStatus($id, 0);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

    //////////////////////////////////////////////
    // AJAX: check username uniqueness (exclude optional id)
    public function checkUsername(Request $request)
    {
        $username = $request->get('username');
        $exclude = $request->get('exclude');
        if (!$username) return response()->json(['exists' => false]);
        $q = Teachers::where('username', $username);
        if ($exclude) {
            try { $excludeId = Crypt::decrypt($exclude); $q->where('id', '!=', $excludeId); } catch (\Exception $e) {}
        }
        $exists = $q->exists();
        return response()->json(['exists' => $exists]);
    }

    //////////////////////////////////////////////
    public function getPassword(Request $request, $id) {
        try {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teachers.view'));
        }
        $teachers = new Teachers();
        $info = $teachers->getTeacher($id);
        if ($info) {
            parent::$data['info'] = $info;
            return view('admin.teachers.password', parent::$data);
        }
        else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teachers.view'));
        }
    }
    //////////////////////////////////////////////
    public function postPassword(Request $request, $id){
        try {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teachers.view'));
        }
        $teachers = new Teachers();
        $info = $teachers->getTeacher($id);
        if ($info) {
            $password = $request->get('password');
            $password_confirmation = $request->get('password_confirmation');

            $validator = Validator::make([
                'password' => $password,
                'password_confirmation' => $password_confirmation
            ], [
                'password' => 'required|between:6,16|confirmed',
                'password_confirmation' => 'required|between:6,16'
            ]);
            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('teachers.password', ['id' => $encrypted_id]))->withInput();
            } else {
                $update = $teachers->updatePassword($id, Hash::make($password));
                if ($update) {
                    $request->session()->flash('success', self::PASSWORD_SUCCESS);
                    return redirect(route('teachers.view'));
                }
                else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('teachers.password', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teachers.view'));
        }
    }
 
    /////////////////////////////////////////
    function split_myString($str) {
        $myString = str_split($str, 4);
        return  $myString[1] . $myString[2];
    }
    /////////////////////////////
    public function SendMessage(Request $request)
    {
        $message = $request->get('message');
        $title = $request->get('title');
        $id = $request->get('id');

        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $auth = Auth::user()->name;
        $teacher = Teachers::find($id);

        if ($teacher) {
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('email-attachments', 'public');
            }

            $campaignService = new \App\Services\EmailCampaignService();
            $campaignService->launchCampaign([
                'subject' => $title,
                'message' => $message,
                'sender_name' => $auth,
                'attachment' => $filePath,
                'recipients' => [['name' => $teacher->name, 'email' => $teacher->email]],
            ]);

            return response()->json([
                'status' => 'success', 
                'message' => 'تم جدولة إرسال الرسالة بنجاح'
            ]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'هذا المعلم غير موجود أو غير نشط']);
        }
    }

        /////////////////////////////
    public function SMS(Request $request)
    {
        $selectedMobiles = $request->input('selectedMobiles');
        $message = $request->input('note');
        $obj = new SMS();
        foreach ($selectedMobiles as $numbers) {

            $ANSWER =$obj->sendSMS($numbers, $message);
        }

        return response()->json(['success' => true]);
    
        if ($ANSWER) {
            return response()->json(['status' => 'error', 'message' => 'نجح الارسال']);
            // dd($ANSWER);
        } else {
            return response()->json(['status' => 'error', 'message' => 'حدث مشكلة اثناء عملية الارسال']);
        }
    }
 
    public function destroyTeachersMessages(Request $request)
    {
        // dd($request->json()->all());

        $id  = $request->id;
        $model = Teachers_Admin_Messages::findOrFail($id);
        $delete =  $model->delete();
        if ($delete) {

            return response()->json([
                'message' => 'Messages has been deleted successfully.',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Something Error !! ',
            ], 404);
        }
    }
}
