<?php

namespace App\Http\Controllers\Admin;

use session;
use App\Models\SMS;
use App\Models\Groups;
use App\Models\Students;
use Illuminate\Http\Request;
use App\Models\GroupStudents;
use App\Models\Pending_Data;
use App\Mail\SendMailToStudents;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
////////////////////////////////////
use Illuminate\Support\Facades\Crypt;
use App\Models\Students_Admin_Messages;
use Illuminate\Support\Facades\Validator;
use App\Mail\SendMailToStudentsHasBirthday;
use App\Notifications\adminStudentsNotification;
use App\Notifications\newAdminCreatedNotification;
use Illuminate\Contracts\Encryption\DecryptException;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class StudentsController extends AdminController
{

    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بتجاح";
    const DONE_ADD = "نجاح، تم اضافة الطالب للمجموعة";
    const DONE_UPDATE = "نجاح، تم تعديل مجموعة الطالب";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const PASSWORD_SUCCESS = "نجاح، تم تغيير كلمة المرور بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً،لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";

    //////////////////////////////////////////////
    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'students';
    }

    //////////////////////////////////////////////
    public function getIndex()
    {
        parent::$data['teachers'] = \App\Models\Teachers::whereNull('deleted_at')->orderBy('name')->get();
        parent::$data['groups']   = \App\Models\Groups::whereNull('deleted_at')->orderBy('name')->get();
        return view('admin.students.view', parent::$data);
    }

    public function getStudentDetails(Request $request)
    {
        $id = $request->get('id');
        $student = Students::with(['gropes.group', 'parent'])->find($id);
        if (!$student) return "Student not found";

        return view('admin.students.parts.student_modal_content', compact('student'))->render();
    }


    //////////////////////////////////////////////
    public function getDelayIndex()
    {
        return view('admin.students.delayingStudent', parent::$data);
    }

    //////////////////////////////////////////////
    public function getIndexAsk_update()
    {
        parent::$data['active_menu'] = 'ask_update';
        parent::$data['current_route'] = 'ask_update';
        return view('admin.ask_update.view', parent::$data);
    }

    //////////////////////////////////////////////
    public function getAsk_updateList(Request $request)
    {
        $title = $request->get('title', NULL);

        $students = new Pending_Data();
        $info = $students->getSearchStudentsAsk_update($title);
        $datatable = Datatables::of($info);

        $datatable->editColumn('title', function ($row) {
            return $row->name;
        });
        $datatable->editColumn('dob', function ($row) {
            return (!empty($row->dob) ? $row->dob : '<i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>');
        });
        $datatable->editColumn('job', function ($row) {
            return (!empty($row->job) ? $row->job : '<i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>');
        });

        //        $datatable->editColumn('status', function ($row) {
        //            $data['id'] = $row->id;
        //            $data['status'] = $row->status;
        //
        //            return view('admin.students.parts.status', $data)->render();
        //        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.ask_update.parts.actions', $data)->render();
        });
        // $datatable->addColumn('select', function ($row) {
        //     $data['x'] = 1;
        //     $data['mobile']  = $row->mobile;
        //     $data['email'] = $row->email;
        //     $data['id'] = $row->id;
        //     return view('admin.' . $this->path . '.parts.general', $data)->render();
        // });
        $datatable->editColumn('checkbox', function ($row) {
            if (!empty($row->mobile)) {
                $mobile = $row->mobile;
                $email = $row->email;
                $id = $row->id;
                return '<div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                            <div class="form-check form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input select-checkbox checkboxes h-20px w-20px" name="mobile[' . $id . ']" value="' . $mobile . '" data-mob="' . $mobile . '" data-email="' . $email . '" data-id="' . $id . '"/>
                            </div>
                        </div>';
            }
            return '';
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getList(Request $request)
    {
        $filters = [
            'title'              => $request->get('title'),
            'activeS'            => $request->get('activeS'),
            'delaying'           => $request->get('delaying'),
            'gender'             => $request->get('gender'),
            'teacher_id'         => $request->get('teacher_id'),
            'group_id'           => $request->get('group_id'),
            'date_from'          => $request->get('date_from'),
            'date_to'            => $request->get('date_to'),
            'is_today'           => $request->get('is_today'),
            'is_activated_today' => $request->get('is_activated_today'),
            'branch_id'          => $request->get('branch_id'),
        ];

        $info = Students::searchReportQuery($filters);
        $datatable = Datatables::of($info);

        $datatable->editColumn('name', function ($row) {
            $default = asset('assets/media/avatars/blank.png'); 
            $avatar = ($row->image && file_exists(public_path($row->image))) 
                      ? asset($row->image) 
                      : (($row->img && file_exists(public_path($row->img))) 
                         ? asset($row->img) 
                         : $default);
                         
            $email = $row->email ?: 'لا يوجد بريد';
            
            // Gender icon logic (Exactly as in Memberships)
            $g = strtolower((string)$row->gender);
            $genderIcon = '';
            if ($g == 'male' || $g == 1) {
                $genderIcon = '<i class="bi bi-gender-male text-primary fs-4 ms-2" title="ذكر"></i>';
            } elseif ($g == 'female' || $g == 2 || $g == 0) {
                $genderIcon = '<i class="bi bi-gender-female text-danger fs-4 ms-2" title="أنثى"></i>';
            }

            return '
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-3 cursor-pointer" onclick="showStudentModal('.$row->id.')">
                        <img src="'.$avatar.'" alt="'.$row->name.'">
                    </div>
                    <div class="d-flex justify-content-start flex-column text-start">
                        <div class="d-flex align-items-center">
                            <a href="javascript:;" onclick="showStudentModal('.$row->id.')" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">'.$row->name.'</a>
                            '.$genderIcon.'
                        </div>
                        <span class="text-gray-400 fw-semibold d-block fs-7">'.$email.'</span>
                    </div>
                </div>';

        });

        $datatable->editColumn('company_name', function ($row) {
            return (!empty($row->company_name) ? $row->company_name : 'N/A');
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.students.parts.status', $data)->render();
        });

        $datatable->editColumn('delayed', function ($row) {
            $data['id'] = $row->id;
            $data['delaying'] = $row->delaying;

            return view('admin.students.parts.delayed', $data)->render();
        });
        $datatable->editColumn('dob', function ($row) {
            $dob = $row->dob;
            $dob = date('Y-m-d', strtotime($dob));
            if (isset($dob) != '') {

                return $dob;
            } else {
                return '
                <i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>';
            }
        });
        $datatable->editColumn('checkbox', function ($row) {
            if (!empty($row->mobile)) {
                $mobile = $row->mobile;
                $email = $row->email;
                $id = $row->id;
                return '<div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                            <div class="form-check form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input select-checkbox checkboxes h-20px w-20px" name="mobile[' . $id . ']" value="' . $mobile . '" data-mob="' . $mobile . '" data-email="' . $email . '" data-id="' . $id . '"/>
                            </div>
                        </div>';
            }
            return '';
        });
        $datatable->editColumn('email', function ($row) {
            $email = $row->email;

            if (isset($email) != '') {

                return $email;
            } else {
                return '
                <i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>';
            }
        });
        $datatable->editColumn('job', function ($row) {
            $jop = $row->job;

            if (isset($jop) != '') {

                return $jop;
            } else {
                return '
                <i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>';
            }
        });
        $datatable->editColumn('note', function ($row) {
            $student_id = $row->id;
            $note = $row->note;
            // {{ Crypt::encrypt($id) }}
            return '
                <a  onclick="addnote(' . $student_id . ')" id="addNoteBtn" data-data="' . $note . '" class="btn btn-primary btn-sm  addNoteBtn" >
                    <i class="bi bi-journal-check"></i>  ملاحظة </a>';
        });

        $datatable->addColumn('branch_name', function ($row) {
            if ($row->branch) {
                $colors = ['info', 'primary', 'success', 'warning'];
                $color  = $colors[$row->branch->id % count($colors)];
                return '<span class="badge badge-light-' . $color . ' fw-bold px-3 py-2">'
                    . '<i class="bi bi-geo-fill me-1"></i>'
                    . e($row->branch->name_ar)
                    . '</span>';
            }
            return '<span class="badge badge-light-secondary fw-bold px-3 py-2 text-muted">—</span>';
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.students.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getDelayList(Request $request)
    {
        $title = $request->get('title', NULL);
        $activeS = $request->get('activeS', NULL);

        $students = new Students();
        $info = $students->getSearchDelayStudents($title, $activeS);

        $datatable = Datatables::of($info);
        $datatable->editColumn('updated_at', function ($row) {
            $dob = $row->updated_at;
            $dob = date('Y-m-d', strtotime($dob));
            if (isset($dob) != '') {

                return $dob;
            } else {
                return '
                <i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>';
            }
        });
        $datatable->editColumn('email', function ($row) {
            $email = $row->email;

            if (isset($email) != '') {

                return $email;
            } else {
                return '
                <i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>';
            }
        });
        $datatable->editColumn('job', function ($row) {
            $jop = $row->job;

            if (isset($jop) != '') {

                return $jop;
            } else {
                return '
                <i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>';
            }
        });
        $datatable->editColumn('delay_cusess', function ($row) {
            $student_id = $row->id;
            $delay_cusess = $row->delay_cusess;
            // {{ Crypt::encrypt($id) }}
            return '
                <a  onclick="showdelay_cusess(' . $student_id . ')" id="addNoteBtn" data-data="' . $delay_cusess . '" class="btn btn-primary btn-sm  addNoteBtn" >
                    <i class="bi bi-journal-check"></i> عرض /اضافة  </a>';
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.students.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getAdd()
    {


        $groups = new Groups();
        $info = $groups->getAllGroups();
        parent::$data['grope'] = $info;
        return view('admin.students.add', parent::$data);
    }

    ////////////////////////////////////////////
    public function postAdd(Request $request)
    {
        $name = $request->get('name');
        $mobile = $request->get('mobile');
        $dob = $request->get('dob');
        $job = $request->get('job');
        $email = $request->get('email');
        $join_date = $request->get('join_date');
        $exam_date = $request->get('exam_date');
        $exam_degree = $request->get('exam_degree');
        $status = (int) $request->get('status');
        $newGropeId = $request->get('grope');

        // Default join_date to today if admin did not set it
        if (empty($join_date)) {
            $join_date = now()->toDateString();
        }

        // Derive program_type from age (kids if <=15, otherwise adult).
        // Falls back to admin-provided value, then 'adult' as a safe default.
        $programType = $request->get('program_type', 'adult');
        if (!empty($dob)) {
            try {
                $studentAge = \Carbon\Carbon::parse($dob)->age;
                $programType = ($studentAge <= 15) ? 'kids' : 'adult';
            } catch (\Exception $e) {
                // keep provided/default programType
            }
        }

        $validator = Validator::make([
            'name' => $name,
            'mobile' => $mobile,
            'status' => $status
        ], [
            'name' => 'required',
            'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|unique:students|digits:10',
            'status' => 'required|numeric|in:0,1'
        ]);
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            session()->flash('danger', $validator->messages());
            return redirect(route('students.add'))->withInput();
        } else {
            $username = $this->split_myString($mobile);
            $password = $this->split_myString($mobile);
            $students = new Students();
            $gender = $request->get('gender', 1);
            // NOTE: addStudent signature accepts more fields than passed here historically.
            // Persist program_type + join_date directly so age-based routing is enforced even
            // if the legacy positional call drops them.
            $students->program_type = $programType;
            $students->join_date = $join_date;
            $add = $students->addStudent($name, $username, Hash::make($password), $mobile, $dob, $job, $email, $join_date, $exam_date, $exam_degree, $status, 0, $gender);
            // Safety net — re-stamp join_date + program_type after addStudent in case
            // the positional signature reset them.
            if ($add && $students->id) {
                $branchId = app(\App\Services\BranchContext::class)->isScoped()
                    ? auth()->guard('admin')->user()->branch_id
                    : ($request->get('branch_id') ?: null);
                $students->newQuery()->where('id', $students->id)->update([
                    'join_date'    => $join_date,
                    'program_type' => $programType,
                    'branch_id'    => $branchId,
                ]);
            }
            if ($add) {
                $student_id = $students->id;
                if (isset($newGropeId) && $student_id != null) {
                    $opj = new GroupStudents();
                    $group_id = $newGropeId;
                    $student_fee_total = $request->student_fee_total ? $request->student_fee_total : 0;
                    $student_book_total = $request->student_book_total ? $request->student_book_total : 0;
                    $addToGrope = $opj->addGroupStudent($student_id, $student_fee_total, $student_book_total, $group_id);
                } else {
                    session()->flash('danger', 'يرجي اختيار مجموعة لهذا الطالب..!');
                    return redirect(route('students.add'));
                }

                session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                session()->flash('success', self::DONE_ADD);

                return redirect(route('students.view'));
            } else {
                session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('students.add'))->withInput();
            }
        }
    }

    //////////////////////////////////////////////
    public function getEdit(Request $request, string $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('news.view'));
        }
        //////////////////////////////////////////////
        $students = new Students();
        $info = $students->getStudent($id);
        if ($info) {
            parent::$data['info'] = $info;

            $groups = new Groups();
            $info = $groups->getAllGroups();
            parent::$data['grope'] = $info;
            $grope_Student = GroupStudents::where('student_id', $id)->first();
            parent::$data['grope_Student'] = $grope_Student;
            return view('admin.students.edit', parent::$data);
        } else {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('students.view'));
        }
    }

    ////////////////////////////////////////////////
    public function postEdit(Request $request, string $id)
    {
        try {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('pages.view'));
        }
        /////////////////////////////
        $students = new Students();
        $info = $students->getStudent($id);
        if ($info) {
            $name = $request->get('name');
            $mobile = $request->get('mobile');
            $dob = $request->get('dob');
            $job = $request->get('job');
            $email = $request->get('email');
            $join_date = $request->get('join_date');
            $exam_date = $request->get('exam_date');
            $exam_degree = $request->get('exam_degree');
            $status = (int) $request->get('status');
            $newGropeId = $request->get('grope');

            // Preserve existing join_date if admin cleared the field, otherwise fall back to today
            if (empty($join_date)) {
                $join_date = $info->join_date ?: now()->toDateString();
            }

            // Re-evaluate program_type from (possibly updated) DOB
            $programType = $info->program_type;
            $dobForAge = !empty($dob) ? $dob : $info->dob;
            if (!empty($dobForAge)) {
                try {
                    $studentAge = \Carbon\Carbon::parse($dobForAge)->age;
                    $programType = ($studentAge <= 15) ? 'kids' : 'adult';
                } catch (\Exception $e) {
                    // keep existing programType
                }
            }

            $validator = Validator::make([
                'name' => $name,
                'mobile' => $mobile,
                'status' => $status
            ], [
                'name' => 'required',
                'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|unique:teachers|digits:10',
                'status' => 'required|numeric|in:0,1'
            ]);

            //////////////////////////////////////////////////////////
            if ($validator->fails()) {
                session()->flash('danger', $validator->messages());
                return redirect(route('students.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                $username = $this->split_myString($mobile);
                $password = $this->split_myString($mobile);
                $update = $students->updateStudent($info, $name, $username, Hash::make($password), $mobile, $dob, $job, $email, $join_date, $exam_date, $exam_degree, $status);
                // Re-stamp join_date + age-derived program_type + branch_id after legacy update call
                $updateData = [
                    'join_date'    => $join_date,
                    'program_type' => $programType,
                ];
                $branchId = $request->get('branch_id');
                if (!app(\App\Services\BranchContext::class)->isScoped()) {
                    $updateData['branch_id'] = $branchId ?: null;
                }
                $students->newQuery()->where('id', $id)->update($updateData);
                if ($update) {

                    session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('students.view'));
                } else {
                    session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('students.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('students.view'));
        }
    }

    ////////////////////////////////////////////////
    public function postDelete(Request $request)
    {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $students = new Students();
        $info = $students->getStudent($id);
        if ($info) {
            $parentId = $info->parent_id;
            $studentId = $info->id;
            $delete = $students->deleteStudent($info);
            if ($delete) {
                // Cascade: soft-delete the student's placement tests so admin lists clear out
                \App\Models\PlacementTests::where('student_id', $studentId)->delete();

                // Cascade: remove parent only if no other (non-trashed) children remain
                if ($parentId) {
                    $siblingsLeft = Students::where('parent_id', $parentId)
                        ->whereNull('deleted_at')
                        ->count();
                    if ($siblingsLeft === 0) {
                        \App\Models\Parents::where('id', $parentId)->delete();
                    }
                }
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            } else {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

    //////////////////////////////////////////////
    public function postStatus(Request $request)
    {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $students = new Students();
        $info = $students->getStudent($id);
        if ($info) {
            $status = $info->status;

            if ($status == 0) {
                $update = $students->updateStatus($id, 1);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $students->updateStatus($id, 0);
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
    public function postDelaying(Request $request)
    {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        /////////////////////////////////////
        $students = new Students();
        $info = $students->getStudent($id);
        if ($info) {
            $delaying = $info->delaying;

            if ($delaying == 1) {
                $update = $students->updateDailling($id, 0);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $students->updateDailling($id, 1);
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
    public function getPassword(Request $request, string $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('students.view'));
        }
        $students = new Students();
        $info = $students->getStudent($id);
        if ($info) {
            parent::$data['info'] = $info;
            return view('admin.students.password', parent::$data);
        } else {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('students.view'));
        }
    }

    //////////////////////////////////////////////
    public function postPassword(Request $request, string $id)
    {
        try {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('students.view'));
        }
        $students = new Students();
        $info = $students->getStudent($id);
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
                session()->flash('danger', $validator->messages());
                return redirect(route('students.password', ['id' => $encrypted_id]))->withInput();
            } else {
                $update = $students->updatePassword($id, Hash::make($password));
                if ($update) {
                    session()->flash('success', self::PASSWORD_SUCCESS);
                    return redirect(route('students.view'));
                } else {
                    session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('students.password', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('students.view'));
        }
    }

    /////////////////////////////////////////
    function split_myString(string $str)
    {
        $myString = str_split($str, 4);
        return $myString[1] . $myString[2];
    }

    /////////////////////////////////////////
    public function getStudentClassEdit(Request $request, string $student_id, string $group_id)
    {
        try {
            $encrypted_id = $group_id;
            $group_id = Crypt::decrypt($group_id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('students.view'));
        }
        try {
            $encrypted_student_id = $student_id;
            $student_id = Crypt::decrypt($student_id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('students.view'));
        }

        $groups = new Groups();
        $info = $groups->getAllActiveGroups();
        parent::$data['grope'] = $info;
        parent::$data['group_id'] = $group_id;
        parent::$data['student_id'] = $student_id;

        $group_students = new GroupStudents();
        $data = $group_students->checkStudentGroupExist($student_id, $group_id);
        if (!$data) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        parent::$data['group_students'] = $data;
        $data['btn_class'] = parent::$data['btn_class'];
        return view('admin.students.edit_student_grope', parent::$data);
    }

    /////////////////////////////////////////
    public function getStudentAddGrope(Request $request, string $student_id)
    {
        // try {
        //     $group_id = Crypt::decrypt($group_id);
        // } catch (DecryptException $e) {
        //     session()->flash('danger', self::NOT_FOUND);
        //     return redirect(route('students.view'));
        // }
        try {

            $student_id = Crypt::decrypt($student_id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('students.view'));
        }
        $group_students = new GroupStudents();
        $students = Students::where('id', '=', $student_id)->first();
        // $data = $group_students->checkFirstStudentGroup($student_id);
        // $data = $group_students->checkStudentGroupExist($student_id,$group_id);
        $result = $group_students->getallStudentrow($student_id);
        // $result =GroupStudents::pluck('group_id')->toArray();
        $ids = array_values($result);
        // dd($ids);group_students
        $info = Groups::whereNotIn('id', $ids)->whereNull('deleted_at')->where('status', 1)->get();
        // $groups = new Groups();
        // $info = $groups->getAllActiveGroups();
        // $info = $groups->getAllActiveGroupsExcept($result);

        parent::$data['grope'] = $info;
        // parent::$data['group_id'] = $group_id;
        parent::$data['student_id'] = $student_id;
        parent::$data['group_students'] = $students;
        $data['btn_class'] = parent::$data['btn_class'];
        return view('admin.students.add_new_grope', parent::$data);
    }

    ///////////////////////////////////////////////////
    public function postAddGrope(Request $request)
    {
        // $studentId = $request->get('student_id');

        try {
            $student_id = Crypt::decrypt($student_id = $request->get('student_id'));
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        $new_grope = $request->get('new_grope');
        // dd($student_id);

        $validator = Validator::make([
            'new_grope' => $new_grope,
            'student_id' => $student_id,
            // 'group_id' => $group_id,
        ], [
            'new_grope' => 'required|numeric',
            'student_id' => 'required|numeric',
            // 'group_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            session()->flash('danger', $validator->messages());
            return redirect()->back()->withInput();
        } else {

            $group = \App\Models\Groups::find($new_grope);
            if (!$group) {
                session()->flash('danger', self::NOT_FOUND);
                return redirect()->back()->withInput();
            }

            // Route through the EnrollmentService so ALL business rules apply consistently:
            //   duplicate · time-conflict · outstanding-balance · program-fee billing · credit auto-apply.
            // Multi-program enrollment is allowed here (no same-program restriction).
            try {
                $adminId = optional(Auth::guard('admin')->user())->id;
                $result  = app(\App\Services\Enrollment\EnrollmentService::class)
                    ->enroll((int) $student_id, $group, $adminId);

                $msg = 'تم تشعيب الطالب في «' . ($group->name ?? '') . '».';
                if (($result['credit_applied'] ?? 0) > 0.009) {
                    $msg .= ' خُصم من رصيده الدائن ' . number_format($result['credit_applied'], 2) . ' ILS.';
                }
                if (!empty($result['no_fee'])) {
                    session()->flash('warning', 'تنبيه: هذا البرنامج لا توجد له رسوم مُعرّفة، لذلك لم تُرصَّد رسوم على الطالب. اضبط رسوم البرنامج من إعدادات الرسوم.');
                }
                session()->flash('success', $msg);
                return redirect(route('students.gropes', $student_id));
            } catch (\App\Exceptions\EnrollmentException $e) {
                session()->flash('danger', implode(' | ', $e->errors));
                return redirect()->back()->withInput();
            } catch (\Exception $e) {
                session()->flash('danger', self::EXECUTION_ERROR . ' (' . $e->getMessage() . ')');
                return redirect()->back()->withInput();
            }
        }
    }

    ///////////////////////////////////////////////////
    public function postChangeGrope(Request $request)
    {
        try {
            $encrypted_id = $request->id;
            $id = Crypt::decrypt($encrypted_id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        try {
            $encrypted = $request->group_id;
            $group_id = Crypt::decrypt($encrypted);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        try {
            $encryptedd = $request->student_id;
            $student_id = Crypt::decrypt($encryptedd);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        $group_new_id = $request->grope;
        $group_students = new GroupStudents();
        $data = $group_students->checkStudentGroupExist($student_id, $group_new_id);
        // dd($data);

        if ($data) {
            session()->flash('danger', "عذرا الطالب موجود بالفعل بهذه المجموعة");
            return redirect()->back()->withInput();
        } else {
            $group_students = new GroupStudents();
            $update = $group_students->changeGropeStudent($id, $group_new_id);
            if ($update) {
                session()->flash('success', self::DONE_UPDATE);
                return redirect(route('groups.view'));
            } else {
                session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('groups.view'))->withInput();
            }
            return view('admin.students.edit_student_grope', parent::$data);
        }
    }

    ////////////////////////////////////////////////
    public function getIndexOfGropes(string $id)
    {
        $student = new Students();
        $student = $student->getStudent($id);
        parent::$data['student_name'] = $student->name;
        parent::$data['student_id'] = $student->id;
        return view('admin.students.groups', parent::$data);
    }

    ////////////////////////////////////////////////
    public function getListOfGropes(Request $request, string $id)
    {
        // return response()->json(['data' => "asasasasa"]);

        $student = new Students();
        $student = $student->getStudent($id);
        session()->put('student_name', $student->name);
        session()->put('student_id', $student->id);
        $title = $request->get('title', NULL);
        $mygroups = new GroupStudents();

        $info = $mygroups->getAdminMyGroup($id, $title);
        $datatable = Datatables::of($info);

        // return response()->json([
        //     'test'=>$info
        // ]);
        $datatable->editColumn('status', function ($row) {
            $x = $data['status'] = $row->student->status;
            $data['id'] = $row->student_id;
            return
                view('admin.students.parts.status6', $data)->render();
        });

        $datatable->editColumn('calender', function ($row) {
            $data['id'] = $row->id;
            $dayes = $row->days;
            if (isset($dayes) != '') {

                return '
                <a  class="btn btn-default btn-sm"  style="color: #ffffff ; width: 132px; background-color: #d3640c;">  ' . $dayes . ' </a>';
            } else {
                return '
                <i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>';
            }
            //  view('admin.students.parts.calender', $data)->render();
        });
        $datatable->editColumn('time', function ($row) {
            $data['id'] = $row->id;
            $times = $row->times;
            if (isset($times) != '') {

                return '
                <a  class="btn btn-default btn-sm"  style="color: #ffffff ; width: 132px; background-color: #3fc3ee;">  ' . $times . ' </a>';
            } else {
                return '
                <i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>';
            }
            //  view('admin.students.parts.calender', $data)->render();
        });

        $datatable->editColumn('statusOfGroup', function ($row) {
            $data['id'] = $row->id;
            // dd($row->id);
            $data['status'] = $row->statusOfGroup;
            return
                view('admin.students.parts.status5', $data)->render();
        });

        $datatable->addColumn('actions', function ($row) {
            $data['row_id'] = $row->row_id;
            $data['grope_id'] = $row->id;
            $data['student_id'] = $row->student_id;
            // $data['student_id'] = session()->get('student_id');
            $data['btn_class'] = parent::$data['btn_class'];

            return
                view('admin.students.parts.actions3', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    ////////////////////////////////////////////////
    public function postAddNote(Request $request)
    {
        $id = $request->get('id');
        $note = $request->get('note');
        $Students = Students::find($id);
        $Students->note = $note;
        $add = $Students->save();
        if ($add) {
            return view('admin.students.delayingStudent', parent::$data);
        } else {
            return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
        }
    }

    ////////////////////////////////////////////////
    public function postAddDelayCusess(Request $request)
    {
        $id = $request->get('id');
        $note = $request->get('note');
        $Students = Students::find($id);
        $Students->delay_cusess = $note;
        $add = $Students->save();
        if ($add) {
            return view('admin.students.view', parent::$data);
        } else {
            return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
        }
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
            return response()->json(['status' => 'error', 'message' => 'Error Decode : ' . $id]);
        }

        $auth = Auth::user()->name;
        $student = Students::find($id);

        if ($student) {
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('email-attachments', 'public');
            }

            $campaignService = new \App\Services\EmailCampaignService();
            $campaign = $campaignService->launchCampaign([
                'subject' => $title,
                'message' => $message,
                'sender_name' => Auth::guard('admin')->user()->name ?? 'Oxford English Centre',
                'attachment' => $filePath,
                'recipients' => [['name' => $student->name, 'email' => $student->email]],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم جدولة إرسال الإشعار بنجاح',
                'campaign_id' => $campaign->id,
                'total_recipients' => 1,
                'redirect_url' => route('admin.email_campaigns.index')
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'هذا الطالب غير موجود أو غير نشط'
            ]);
        }
    }

    /////////////////////////////
    //     public function sendCustomEmail(Request $request)
    //     {
    //         $title = $request->input('title');
    //         $message = $request->input('message');
    //         $image = $request->file('file');
    //         $emailsString = $request->input('emails');
    //         // Convert the string of emails to an array
    //         $emails = explode(',', $emailsString);
    //         // Store the uploaded image in a temporary folder
    //         $imagePath = $image->store('temp', 'public');
    // // DD($imagePath);
    //         foreach ($emails as $email) {
    //             Mail::to($email)->send(new SendMailToStudents($imagePath, $title, $message, $email));
    //         }
    //         return redirect()->back()->with('success', 'Email sent successfully!');
    //     }
    // public function sendCustomEmail(Request $request)
    // {
    //     $title = $request->input('title');
    //     $message = $request->input('message');
    //     $image = $request->file('file');
    //     $emails = $request->input('emails');
    //     // Check if a file was uploaded
    //     if ($image) {
    //         // Store the uploaded image in a temporary folder
    //         $image_url =  $request->image->getClientOriginalName();
    //         $image_url =  rand(223423, 23423444) . $image_url;
    //         $base_url = url('attachments/sliders/' . $image_url);
    //         $slider->image   = $base_url;
    //         $request->image->move(public_path('attachments/sliders'), $image_url);
    //     } else {
    //         // If no file was uploaded, set the image path to null
    //         $imagePath = null;
    //     }
    //     if (is_array($emails)) {
    //         foreach ($emails as $email) {
    //             Mail::to($email)->send(new SendMailToStudents($title, $message, $email, $imagePath));
    //         }
    //     } else {
    //         Mail::to($emails)->send(new SendMailToStudents($title, $message, $emails, $imagePath));
    //     }
    //     // If an image was uploaded, delete it from the temporary folder
    //     if ($imagePath) {
    //         Storage::disk('public')->delete($imagePath);
    //     }
    //     return redirect()->back()->with('success', 'Email sent successfully!');
    // }

    public function SendCustomEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title'   => 'required|string|max:255',
                'message' => 'required|string',
                'emails'  => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
            }

            $emailsInput = $request->input('emails');
            if (is_array($emailsInput)) {
                $emails = collect($emailsInput)->flatten()->map(fn($email) => trim((string)$email))->filter()->unique()->values()->toArray();
            } else {
                $emails = array_filter(array_map('trim', explode(',', (string)$emailsInput)));
            }

            if (empty($emails)) {
                return response()->json(['status' => 'error', 'message' => 'لا توجد ايميلات صالحة للإرسال'], 400);
            }

            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('email-attachments', 'public');
            }

            $campaignService = new \App\Services\EmailCampaignService();
            $campaign = $campaignService->launchCampaign([
                'subject' => $request->title,
                'message' => $request->message,
                'sender_name' => Auth::guard('admin')->user()->name ?? 'Oxford English Centre',
                'attachment' => $filePath,
                'recipients' => $emails,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم بدء حملة الإرسال بنجاح',
                'campaign_id' => $campaign->id,
                'redirect_url' => route('admin.email_campaigns.show', $campaign->id)
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }


    public function SendCustomEmail2(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title'   => 'required|string|max:255',
                'message' => 'required|string',
                'emails'  => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
            }

            $emailsInput = $request->input('emails');
            if (is_array($emailsInput)) {
                $emails = $emailsInput;
            } else {
                $emails = array_filter(array_map('trim', explode(',', (string)$emailsInput)));
            }

            if (empty($emails)) {
                return response()->json(['status' => 'error', 'message' => 'لا توجد ايميلات صالحة للإرسال'], 400);
            }

            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('email-attachments', 'public');
            }

            $campaignService = new \App\Services\EmailCampaignService();
            $campaign = $campaignService->launchCampaign([
                'subject' => $request->title,
                'message' => $request->message,
                'sender_name' => Auth::guard('admin')->user()->name ?? 'Oxford English Centre',
                'attachment' => $filePath,
                'recipients' => $emails,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم بدء حملة تهنئة أعياد الميلاد بنجاح',
                'campaign_id' => $campaign->id,
                'redirect_url' => route('admin.email_campaigns.show', $campaign->id)
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }


    public function StudentsSendMessage(Request $request)
    {
        // dd($request->json()->all());
        $jsonPayload = $request->json()->all();
        $title = $jsonPayload['title'];
        $body = $jsonPayload['body'];
        $id = Auth::guard('students')->user()->id;
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first()
            ], 422);
        } else {
            $opj = new Students_Admin_Messages();
            $add = $opj->SaveMessages($title, $body, $id);
            if ($add) {

                return response()->json(['status' => 'success', 'message' => 'Done Send successfully !']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'something error!!']);
            }
        }
    }

    public function destroyStudentsMessages(Request $request)
    {
        // dd($request->json()->all());

        $id = $request->id;
        $model = Students_Admin_Messages::findOrFail($id);
        $delete = $model->delete();
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

    /////////////////////////////
    public function SMSGruop(Request $request)
    {
        $groupIds = $request->input('selectedMobiles');
        $message = $request->input('note');

        $groupStudents = GroupStudents::whereIn('group_id', $groupIds)
            ->join('students', 'group_students.student_id', '=', 'students.id')
            ->whereNotNull('students.mobile')
            ->select('students.mobile')
            ->get();

        $phoneNumbers = $groupStudents->pluck('mobile')->toArray();
        $token = '9ko9p0ntxp57wxd6';

        $successResponses = [];
        $errorResponses = [];

        foreach ($phoneNumbers as $phoneNumber) {
            $params = [
                'token' => $token,
                'to' => $phoneNumber,
                'body' => $message,
                'priority' => '1',
                'referenceId' => '',
                'msgId' => '',
                'mentions' => '',
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.ultramsg.com/instance62198/messages/chat',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_HTTPHEADER => ['content-type: application/x-www-form-urlencoded'],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                $errorResponses[] = [
                    'phoneNumber' => $phoneNumber,
                    'status' => 'error',
                    'message' => 'حدث مشكلة اثناء عملية الارسال',
                ];
            } else {
                $responseData = json_decode($response, true);

                if ($responseData && isset($responseData['status']) && $responseData['status'] === 'success') {
                    $successResponses[] = [
                        'phoneNumber' => $phoneNumber,
                        'status' => 'success',
                        'message' => 'Message sent successfully',
                    ];
                } else {
                    $errorResponses[] = [
                        'phoneNumber' => $phoneNumber,
                        'status' => 'error',
                        'message' => 'حدث مشكلة اثناء عملية الارسال',
                    ];
                }
            }
        }

        return response()->json([
            'success' => $successResponses,
            'errors' => $errorResponses,
        ]);
    }

    // public function SMSGruop(Request $request)
    // {
    //     $groupIds = $request->input('selectedMobiles');
    //     $message = $request->input('note');
    //     $groupStudents = GroupStudents::whereIn('group_id', $groupIds)
    //         ->join('students', 'group_students.student_id', '=', 'students.id')
    //         ->whereNotNull('students.mobile')
    //         ->select('students.mobile')
    //         ->get();
    //     $groupStudentMobiles = $groupStudents->pluck('mobile')->toArray();
    //     // dd($groupStudentMobiles);
    //     $obj = new SMS();
    //     foreach ($groupStudentMobiles as $numbers) {
    //         $ANSWER = $obj->sendSMS($numbers, $message);
    //     }
    //     return response()->json(['success' => true]);
    //     if ($ANSWER) {
    //         return response()->json(['status' => 'error', 'message' => 'نجح الارسال']);
    //         // dd($ANSWER);
    //     } else {
    //         return response()->json(['status' => 'error', 'message' => 'حدث مشكلة اثناء عملية الارسال']);
    //     }
    // }

    public function generat_pdf(Request $request, $id)
    {

        // $customData = [
        //     'name' => 'John Doe',
        //     'mark' => 'Excellent',
        // ];
        // $html = view('admin.certificates.levels', $customData)->render();
        // $mpdf = new Mpdf();
        // $mpdf->WriteHTML($html);
        // header('Content-Type: application/pdf');
        // header('Content-Disposition: inline; filename="certificate2.pdf"');
        // $mpdf->Output('filename.pdf', 'D');
        $customData = [
            'name' => 'John Doe',
            'mark' => 'Excellent',
        ];

        $html = view('admin.certificates.levels', $customData)->render();
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        echo $html;
        exit();
        $pdfContent = $mpdf->Output('', 'S'); // Get the PDF content as a string

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="certificate2.pdf"');
    }

    public function postAcceptAskUpdate(Request $request)
    {
        //        dd(request()->ajax());
        $id = $request->input('id');
        try {

            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            session()->flash('danger', self::NOT_FOUND);
            return redirect('/student');
        }
        $student = Students::find($id);
        if (request()->ajax() && $student) {
            $url = request()->url();

            if (strpos($url, 'admin/ask_update/accept') !== false) {

                $student->update(['ask_update' => 2]);
                return response()->json(['success' => true]);
            } elseif (strpos($url, 'admin/ask_update/refuse') !== false) {
                $student->update(['ask_update' => 0]);
                return response()->json(['success' => true]);
            }
        } else {
            return response()->json(['success' => false]);
        }
    }
}
