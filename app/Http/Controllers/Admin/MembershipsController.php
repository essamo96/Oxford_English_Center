<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;
////////////////////////////////////
use App\Models\Students;
use App\Models\ExamDegree;
use App\Models\Countries;
use App\Models\Courses;
use App\Models\Classes;
use Illuminate\Support\Facades\App;
use App\Models\CoursesStudents;
use App\Models\ClassesStudents;
use App\Models\EmailCampaignLog;
use App\Models\Students_Admin_Messages;
use App\Models\Teachers_Admin_Messages;
use App\Models\Teachers;
use App\Models\Pending_Data;
use App\Mail\NewStudentEmail;
use Illuminate\Support\Facades\Mail;

class MembershipsController extends AdminController
{
    /** @var mixed */
    public $mysettings;
    /** @var mixed */
    public $social;
    /** @var string */
    public $path;


    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بتجاح";
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
        $count_disabled_students = MembershipsController::getCountOfStudentMembershipRequests();
        $this->mysettings = parent::$data['mysettings'];
        $this->social = parent::$data['social'];
        parent::$data['active_menu'] = 'dashboard';
        parent::$data['count_disabled_students'] =  $count_disabled_students;
        $this->path = 'dashboard';
    }
    public function getIndex()
    {
        Students::where('seen', 0)->update(['seen' => 1]);
        return view('admin.' . $this->path . '.membership', parent::$data);
    }

    public function postStatus(Request $request)
    {
        try {
            $id = $request->get('id');

            // 1. فك التشفير
            try {
                $id = Crypt::decrypt($id);
            } catch (DecryptException $e) {
                Log::warning('Decryption error:', ['error' => $e->getMessage()]);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'خطأ في فك التشفير'
                ], 400);
            }

            // 2. البحث عن الطالب
            $student = Students::find($id);
            if (!$student) {
                return response()->json([
                    'status'  => 'error',
                    'message' => self::NOT_FOUND
                ], 404);
            }

            // 3. التحقق من حالة الطالب
            $currentStatus = (int) $student->status;

            if (!in_array($currentStatus, [0, 1])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'حالة غير صحيحة'
                ], 400);
            }

            // --- حالة التفعيل (Status 0 -> 1) ---
            if ($currentStatus === 0) {
                // تحديث الحالة
                $student->status = 1;
                if (!$student->save()) {
                    throw new \Exception(self::EXECUTION_ERROR);
                }

                $mailSent = false;
                $mailError = null;
                $campaign = null;

                // إرسال البريد الترحيبي عبر Campaign Service للتتبع
                if ($student->email) {
                    try {
                        $username      = $student->username ?: $this->extractLoginFromMobile($student->mobile);
                        $plainPassword = $this->extractLoginFromMobile($student->mobile);

                        $campaignService = new \App\Services\EmailCampaignService();
                        $campaign = $campaignService->launchCampaign([
                            'subject'     => 'مرحباً بك في Oxford English Centre',
                            'message'     => "أهلاً {$student->name}،\n\nتم تفعيل حسابك بنجاح.\n\nبيانات الدخول:\n• اسم المستخدم: {$username}\n• كلمة المرور: {$plainPassword}\n\nيمكنك تسجيل الدخول من خلال التطبيق أو الموقع الإلكتروني.",
                            'sender_name' => 'Oxford English Centre',
                            'recipients'  => [['name' => $student->name, 'email' => $student->email]],
                        ]);

                        Log::info('Welcome campaign started for: ' . $student->email);
                        $mailSent = true;
                    } catch (\Exception $mailException) {
                        Log::error('Email campaign failed for student ' . $student->id . ':', [
                            'email' => $student->email,
                            'error' => $mailException->getMessage(),
                        ]);
                        $mailError = $mailException->getMessage();
                    }
                }

                return response()->json([
                    'status'         => 'success',
                    'message'        => self::ACTIVATION_SUCCESS . ($mailSent ? ' - تم إرسال الإيميل ✓' : ''),
                    'type'           => 'yes',
                    'mail_sent'      => $mailSent,
                    'mail_error'     => $mailError,
                    'campaign_id'    => $campaign ? $campaign->id : null,
                    'total_recipients' => 1,
                    'redirect_url'   => $campaign ? route('admin.email_campaigns.index') : null,
                ]);
            }

            // --- حالة التعطيل (Status 1 -> 0) ---
            $student->status = 0;
            if (!$student->save()) {
                throw new \Exception(self::EXECUTION_ERROR);
            }

            return response()->json([
                'status'  => 'success',
                'message' => self::DISABLE_SUCCESS,
                'type'    => 'no',
            ]);

        } catch (\Exception $mainException) {
            // التقاط أي خطأ غير متوقع
            Log::critical('General Error in postStatus: ' . $mainException->getMessage(), [
                'file' => $mainException->getFile(),
                'line' => $mainException->getLine(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => self::EXECUTION_ERROR
            ], 500);
        }
    }

    public function postBulkStatus(Request $request)
    {
        try {
            $ids = $request->get('ids');
            if (empty($ids) || !is_array($ids)) {
                return response()->json(['status' => 'error', 'message' => 'لم يتم تحديد أي طلاب'], 400);
            }

            $recipients = [];
            $activatedCount = 0;

            foreach ($ids as $encryptedId) {
                try {
                    $id = Crypt::decrypt($encryptedId);
                    $student = Students::find($id);
                    if ($student && $student->status == 0) {
                        $student->status = 1;
                        if ($student->save()) {
                            $activatedCount++;
                            if ($student->email) {
                                $username      = $student->username ?: $this->extractLoginFromMobile($student->mobile);
                                $plainPassword = $this->extractLoginFromMobile($student->mobile);
                                
                                $message = "Dear {$student->name},<br><br>Your account has been successfully activated at Oxford English Centre.<br><br><strong>Login Credentials:</strong><br>• Username: {$username}<br>• Password: {$plainPassword}<br><br>You can now log in via our mobile application or our website.";
                                
                                $recipients[] = [
                                    'name' => $student->name,
                                    'email' => $student->email,
                                    'message' => $message
                                ];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if ($activatedCount == 0) {
                return response()->json(['status' => 'error', 'message' => 'لم يتم تفعيل أي طلبات جديدة (تأكد من تحديد طلاب غير مفعلين)'], 400);
            }

            $campaign = null;
            if (!empty($recipients)) {
                $campaignService = new \App\Services\EmailCampaignService();
                $campaign = $campaignService->launchCampaign([
                    'subject'     => 'Welcome to Oxford English Centre',
                    'message'     => 'Account Activation Notification',
                    'sender_name' => 'Oxford English Centre',
                    'recipients'  => $recipients,
                ]);
            }

            return response()->json([
                'status'           => 'success',
                'message'          => "تم تفعيل {$activatedCount} طلاب بنجاح" . (!empty($recipients) ? " وبدء حملة إرسال بيانات الدخول" : ""),
                'campaign_id'      => $campaign ? $campaign->id : null,
                'total_recipients' => count($recipients),
                'redirect_url'     => $campaign ? route('admin.email_campaigns.show', $campaign->id) : null,
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk Membership Activation Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR], 500);
        }
    }

    public function getmembershiplist(Request $request)
    {

        $filters = [
            'search'     => $request->get('search'),
            'date_from'  => $request->get('date_from'),
            'date_to'    => $request->get('date_to'),
            'gender'     => $request->get('gender'),
            'is_today'   => $request->get('is_today'),
        ];

        // Custom logic for today filter if provided
        $query = Students::askJoinQuery($filters);
        
        if ($filters['is_today'] == 1) {
            $query->whereDate('created_at', \Carbon\Carbon::today());
        }

        $datatable = Datatables::of($query);

        // Format Name column (with requested Metronic 8 template + Gender icon)
        $datatable->editColumn('name', function ($row) {
            $default = asset('uploads/default.jpg');
            $avatar = ($row->image && file_exists(public_path($row->image))) 
                      ? asset($row->image) 
                      : (($row->img && file_exists(public_path($row->img))) 
                         ? asset($row->img) 
                         : $default);
                         
            $email = $row->email ?: 'لا يوجد بريد';
            
            // Gender icon logic
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
                    <div class="d-flex justify-content-start flex-column">
                        <div class="d-flex align-items-center">
                            <a href="javascript:;" onclick="showStudentModal('.$row->id.')" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">'.$row->name.'</a>

                            '.$genderIcon.'
                        </div>
                        <span class="text-gray-400 fw-semibold d-block fs-7">'.$email.'</span>
                    </div>
                </div>';
        });

        // Format DOB column
        $datatable->editColumn('dob', function ($row) {
            return !empty($row->dob)
                ? '<span class="fw-bold"><i class="bi bi-calendar-event me-1"></i>' . $row->dob . '</span>'
                : '<span class="badge badge-light-warning">غير محدد</span>';
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.dashboard.parts.status_askmembership', $data)->render();
        });

        // Add actions column
        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.students.parts.actions', $data)->render();
        });

            // Add dedicated checkbox column with alignment and height fix
        $datatable->editColumn('checkbox', function ($row) {
            if (!empty($row->mobile)) {
                $mobile = $row->mobile;
                $email = $row->email;
                $id = Crypt::encrypt($row->id);
                return '<div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                            <div class="form-check form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input select-checkbox checkboxes h-20px w-20px" name="mobile[' . $row->id . ']" value="' . $mobile . '" data-mob="' . $mobile . '"  data-email="' . $email . '"  data-id="' . $id . '"/>
                            </div>
                        </div>';
            }
            return '';
        });

        $datatable->rawColumns(['name', 'status', 'actions', 'checkbox', 'dob']);

        return $datatable->make(true);
    }

    /**
     * Validate date format
     *
     * @param string $date
     * @return bool
     */
    private function isValidDate($date)
    {
        return !empty($date) && strtotime($date) !== false;
    }

    /**
     * Generate a safe 7-digit login from the mobile number.
     */
    private function extractLoginFromMobile(string $mobile)
    {
        $mobileDigits = preg_replace('/\D+/', '', (string) $mobile);

        if (strlen($mobileDigits) >= 7) {
            return substr($mobileDigits, -7);
        }

        return $mobileDigits;
    }
    public function viewaskcourses()
    {
        return view('admin.dashboard.askcourses', parent::$data);
    }
    ///////////////////////////
    public function askcourseslist(Request $request)
    {
        $length = $request->get('length');
        $start = $request->get('start');
        $title = $request->get('title');

        $info = CoursesStudents::with('student')->with('course')->where('status', 0)->whereNull('deleted_at')->get();

        $datatable = Datatables::of($info);
        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.dashboard.parts.status_askcourses', $data)->render();
        });
        $datatable->editColumn('student_id', function ($row) {
            $date = $row->student->name_ar;
            return $date;
        });
        $datatable->editColumn('course_id', function ($row) {
            $date = $row->course->name_ar;
            return $date;
        });

        $datatable->editColumn('created_at', function ($row) {
            $date = explode(' ', $row->created_at);
            return $date[0];
        });
        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['title_ar'] = $row->title_ar;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.dashboard.parts.actions', $data)->render();
        });
        $datatable->setRowAttr([
            'align' => ' cinter',
        ]);
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }
    ///////////////////////////

    public static function getCountOfStudentMembershipRequests()
    {
        $opj = new Students();
        $count_disabled_students = $opj->getAllnewStudentsCount();
        return $count_disabled_students;
    }
    public function indexStudentsMessages(Request $request)
    {
        parent::$data['active_menu'] = 'mempership';
        $search = $request->get('search');
        
        // Fetch students who have messages, optionally filtered by name or message content
        $query = Students::whereHas('messages');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhereHas('messages', function($mq) use ($search) {
                      $mq->where('content', 'like', "%$search%");
                  });
            });
        }

        $students = $query->withCount([
                'messages as unread_count' => function($q) { $q->where('seen', 0); },
                'messages'
            ])
            ->get();
            
        // Get last message for each student for the contact list
        foreach($students as $student) {
            $student->last_message = Students_Admin_Messages::where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        parent::$data['students'] = $students->sortByDesc(function($s) {
            return $s->last_message ? $s->last_message->created_at : 0;
        });
        
        if ($request->ajax()) {
            return view('admin.dashboard.parts.contact_list', ['students' => parent::$data['students']])->render();
        }

        return view('admin.dashboard.studentMesages', parent::$data);
    }

    public function getStudentChatHistory(string $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Invalid ID']);
        }

        $student = Students::findOrFail($id);
        
        // Mark as seen
        Students_Admin_Messages::where('student_id', $id)->where('seen', 0)->update(['seen' => 1]);

        // Incoming from student
        $incoming = Students_Admin_Messages::where('student_id', $id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($msg) {
                return [
                    'type' => 'incoming',
                    'content' => $msg->content,
                    'title' => $msg->title,
                    'created_at' => $msg->created_at->toDateTimeString(),
                    'human_date' => $msg->created_at->diffForHumans(),
                    'id' => $msg->id
                ];
            });

        // Outgoing to student
        $outgoing = EmailCampaignLog::where('recipient_email', $student->email)
            ->with(['campaign.admin'])
            ->get()
            ->map(function($log) {
                $admin = $log->campaign && $log->campaign->admin ? $log->campaign->admin : null;
                return [
                    'type' => 'outgoing',
                    'content' => $log->campaign ? $log->campaign->message : 'N/A',
                    'title' => $log->campaign ? $log->campaign->subject : 'Reply',
                    'created_at' => $log->created_at->toDateTimeString(),
                    'human_date' => $log->created_at->diffForHumans(),
                    'id' => 'c-' . $log->id,
                    'sender' => $admin ? $admin->name : ($log->campaign && $log->campaign->sender_name ? $log->campaign->sender_name : 'Admin'),
                    'admin_role' => $admin ? $admin->role : 'مجبر',
                    'admin_image' => ($admin && property_exists($admin, 'image') && $admin->image) ? asset($admin->image) : null,
                    'admin_initial' => $admin ? mb_substr($admin->name, 0, 1) : 'A'
                ];
            });

        $history = $incoming->concat($outgoing)->sortBy('created_at')->values();

        return response()->json([
            'status' => 'success',
            'student' => [
                'id' => $student->id,
                'encrypted_id' => Crypt::encrypt($student->id),
                'name' => $student->name,
                'email' => $student->email,
                'mobile' => $student->mobile,
                'status' => $student->status == 1 ? 'نشط' : 'غير نشط',
                'status_class' => $student->status == 1 ? 'success' : 'danger',
                'image' => $student->image ? url($student->image) : null,
                'initial' => mb_substr($student->name, 0, 1)
            ],
            'history' => $history
        ]);
    }

    public function deleteMessage(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type'); // 'incoming' or 'outgoing'

        if ($type == 'incoming') {
            $msg = Students_Admin_Messages::find($id);
            if ($msg) {
                $msg->delete();
                return response()->json(['status' => 'success', 'message' => 'تم حذف الرسالة']);
            }
        } else {
            // Outgoing is in EmailCampaignLog (sometimes prefixed with 'c-')
            $logId = str_replace('c-', '', $id);
            $log = EmailCampaignLog::find($logId);
            if ($log) {
                $log->delete();
                return response()->json(['status' => 'success', 'message' => 'تم حذف الرسالة']);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'الرسالة غير موجودة']);
    }

    public function editMessage(Request $request)
    {
        $id = $request->get('id');
        $content = $request->get('content');
        $type = $request->get('type'); // 'incoming' or 'outgoing'

        if ($type == 'incoming') {
            $msg = Students_Admin_Messages::find($id);
            if ($msg) {
                $msg->content = $content;
                $msg->save();
                return response()->json(['status' => 'success', 'message' => 'تم تعديل الرسالة']);
            }
        } else {
            $logId = str_replace('c-', '', $id);
            $log = EmailCampaignLog::find($logId);
            if ($log && $log->campaign) {
                $log->campaign->message = $content;
                $log->campaign->save();
                return response()->json(['status' => 'success', 'message' => 'تم تعديل الرسالة']);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'فشل في تعديل الرسالة']);
    }
    public function indexTeachersMessages(Request $request)
    {
        parent::$data['active_menu'] = 'teacher_messages';
        $search = $request->get('search');
        
        $query = Teachers::whereHas('messages');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhereHas('messages', function($mq) use ($search) {
                      $mq->where('content', 'like', "%$search%");
                  });
            });
        }

        $teachers = $query->withCount([
                'messages as unread_count' => function($q) { $q->where('seen', 0); },
                'messages'
            ])
            ->get();
            
        foreach($teachers as $teacher) {
            $teacher->last_message = Teachers_Admin_Messages::where('teacher_id', $teacher->id)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        parent::$data['teachers'] = $teachers->sortByDesc(function($s) {
            return $s->last_message ? $s->last_message->created_at : 0;
        });
        
        if ($request->ajax()) {
            return view('admin.dashboard.parts.teacher_contact_list', ['teachers' => parent::$data['teachers']])->render();
        }

        return view('admin.dashboard.teacherMesages', parent::$data);
    }

    public function getTeacherChatHistory(string $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Invalid ID']);
        }

        $teacher = Teachers::findOrFail($id);
        
        // Mark as seen
        Teachers_Admin_Messages::where('teacher_id', $id)->where('seen', 0)->update(['seen' => 1]);

        // Incoming from teacher
        $incoming = Teachers_Admin_Messages::where('teacher_id', $id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($msg) {
                return [
                    'type' => 'incoming',
                    'content' => $msg->content,
                    'title' => $msg->title,
                    'created_at' => $msg->created_at->toDateTimeString(),
                    'human_date' => $msg->created_at->diffForHumans(),
                    'id' => $msg->id
                ];
            });

        // Outgoing to teacher
        $outgoing = EmailCampaignLog::where('recipient_email', $teacher->email)
            ->with(['campaign.admin'])
            ->get()
            ->map(function($log) {
                $admin = $log->campaign && $log->campaign->admin ? $log->campaign->admin : null;
                return [
                    'type' => 'outgoing',
                    'content' => $log->campaign ? $log->campaign->message : 'N/A',
                    'title' => $log->campaign ? $log->campaign->subject : 'Reply',
                    'created_at' => $log->created_at->toDateTimeString(),
                    'human_date' => $log->created_at->diffForHumans(),
                    'id' => 'c-' . $log->id,
                    'sender' => $admin ? $admin->name : ($log->campaign && $log->campaign->sender_name ? $log->campaign->sender_name : 'Admin'),
                    'admin_role' => $admin ? $admin->role : 'إدارة',
                    'admin_image' => ($admin && property_exists($admin, 'image') && $admin->image) ? asset($admin->image) : null,
                    'admin_initial' => $admin ? mb_substr($admin->name, 0, 1) : 'A'
                ];
            });

        $history = $incoming->concat($outgoing)->sortBy('created_at')->values();

        return response()->json([
            'status' => 'success',
            'teacher' => [
                'id' => $teacher->id,
                'encrypted_id' => Crypt::encrypt($teacher->id),
                'name' => $teacher->name,
                'email' => $teacher->email,
                'mobile' => $teacher->mobile,
                'status' => $teacher->status == 1 ? 'نشط' : 'غير نشط',
                'status_class' => $teacher->status == 1 ? 'success' : 'danger',
                'image' => $teacher->image ? url($teacher->image) : null,
                'initial' => mb_substr($teacher->name, 0, 1)
            ],
            'history' => $history
        ]);
    }
}
