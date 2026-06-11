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
use App\Models\GroupStudentsFees;
use App\Services\FinancialService;
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
    /** @var FinancialService */
    protected $financialService;


    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بتجاح";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const PASSWORD_SUCCESS = "نجاح، تم تغيير كلمة المرور بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً،لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";

    //////////////////////////////////////////////
    public function __construct(FinancialService $financialService)
    {
        parent::__construct();
        $this->financialService = $financialService;
        $count_disabled_students = MembershipsController::getCountOfStudentMembershipRequests();
        $this->mysettings = parent::$data['mysettings'];
        $this->social = parent::$data['social'];
        parent::$data['active_menu'] = 'dashboard';
        parent::$data['count_disabled_students'] =  $count_disabled_students;
        $this->path = 'dashboard';
    }
    public function getIndex()
    {
        $updated = Students::where('seen', 0)->update(['seen' => 1]);
        if ($updated > 0) {
            try {
                broadcast(new \App\Events\CountersUpdated());
            } catch (\Throwable $e) {
                Log::error('Broadcast CountersUpdated failed in MembershipsController@getIndex: ' . $e->getMessage());
            }
        }
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

                try {
                    broadcast(new \App\Events\CountersUpdated());
                } catch (\Throwable $e) {
                    Log::error('Broadcast CountersUpdated failed in MembershipsController@postStatus: ' . $e->getMessage());
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

            try {
                broadcast(new \App\Events\CountersUpdated());
            } catch (\Throwable $e) {
                Log::error('Broadcast CountersUpdated failed in MembershipsController@postStatus: ' . $e->getMessage());
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

            try {
                broadcast(new \App\Events\CountersUpdated());
            } catch (\Throwable $e) {
                Log::error('Broadcast CountersUpdated failed in MembershipsController@postBulkStatus: ' . $e->getMessage());
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
            'search'          => $request->get('search'),
            'date_from'       => $request->get('date_from'),
            'date_to'         => $request->get('date_to'),
            'gender'          => $request->get('gender'),
            'program_type'    => $request->get('program_type'),
            'enrollment_type' => $request->get('enrollment_type'), // 'test' | 'course'
            'anomaly'         => $request->get('anomaly'),         // 'underage_adult'
            'is_today'        => $request->get('is_today'),
        ];

        // Custom logic for today filter if provided
        $query = Students::askJoinQuery($filters);

        // Branch filter — auto from BranchScope or explicit from UI (super admin)
        $branchId = app(\App\Services\BranchContext::class)->getId()
                 ?? ($request->get('branch_id') ?: null);
        if ($branchId) {
            $query->where('students.branch_id', $branchId);
        }

        // Program-type filter: combine stored value with age-based truth so
        // mis-stored records (e.g. a child saved as adult) still filter correctly.
        if ($filters['program_type']) {
            $cutoffKidsDob = \Carbon\Carbon::today()->subYears(15)->toDateString(); // born after this date → ≤15 (kids)
            if ($filters['program_type'] === 'kids') {
                $query->where(function ($q) use ($cutoffKidsDob) {
                    $q->where('students.program_type', 'kids')
                      ->orWhere(function ($q2) use ($cutoffKidsDob) {
                          $q2->whereNotNull('students.dob')
                             ->where('students.dob', '>=', $cutoffKidsDob);
                      });
                });
            } elseif ($filters['program_type'] === 'adult') {
                $query->where(function ($q) use ($cutoffKidsDob) {
                    $q->where(function ($q2) use ($cutoffKidsDob) {
                        $q2->where('students.program_type', 'adult')
                           ->where(function ($q3) use ($cutoffKidsDob) {
                               $q3->whereNull('students.dob')
                                  ->orWhere('students.dob', '<', $cutoffKidsDob);
                           });
                    });
                });
            }
        }

        // Enrollment type filter (Placement Test vs Direct Enrollment)
        if (!empty($filters['enrollment_type'])) {
            $query->where('students.enrollment_type', $filters['enrollment_type']);
        }

        // Anomaly filter: applicants who picked Adult on the form but are ≤15
        // (auto-routed to kids internally but original intent was adult)
        if (($filters['anomaly'] ?? '') === 'underage_adult') {
            $cutoffKidsDob = \Carbon\Carbon::today()->subYears(15)->toDateString();
            $query->where('students.requested_program_type', 'adult')
                  ->whereNotNull('students.dob')
                  ->where('students.dob', '>=', $cutoffKidsDob);
        }

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

            // Program Type badge — derive from actual age so it stays correct
            // even if program_type wasn't set or was set incorrectly during storage.
            $age = null;
            if (!empty($row->dob)) {
                try { $age = \Carbon\Carbon::parse($row->dob)->age; } catch (\Exception $e) {}
            }
            $effectiveType = $row->program_type;
            if (!is_null($age)) {
                $effectiveType = ($age <= 15) ? 'kids' : 'adult';
            }
            // Heal stored value if it diverges from age-based reality
            if (!is_null($age) && $effectiveType !== $row->program_type) {
                \App\Models\Students::where('id', $row->id)->update(['program_type' => $effectiveType]);
            }
            $pType = $effectiveType === 'kids'
                ? '<span class="badge badge-light-success fs-8 fw-bold"><i class="bi bi-emoji-smile me-1"></i>KIDS</span>'
                : '<span class="badge badge-light-primary fs-8 fw-bold"><i class="bi bi-person-fill me-1"></i>ADULT</span>';

            // Enrollment-type badge (Placement Test vs Direct Enrollment)
            $eType = '';
            if ($row->enrollment_type === 'test') {
                $eType = '<span class="badge badge-light-warning fs-8 fw-bold ms-1" title="اختبار تحديد المستوى"><i class="bi bi-clipboard-check me-1"></i>Placement</span>';
            } elseif ($row->enrollment_type === 'course') {
                $eType = '<span class="badge badge-light-info fs-8 fw-bold ms-1" title="تسجيل مباشر"><i class="bi bi-mortarboard-fill me-1"></i>Direct</span>';
            }

            // Anomaly flag: applicant picked "adult" on the form but is ≤15
            $anomaly = '';
            if ($row->requested_program_type === 'adult' && !is_null($age) && $age <= 15) {
                $anomaly = '<span class="badge badge-light-danger fs-8 fw-bold ms-1" title="حاول التسجيل ككبار وعمره ≤ 15 — تم إعادة توجيهه للأطفال تلقائياً"><i class="bi bi-exclamation-triangle-fill me-1"></i>Under-15 → Adult</span>';
            }

            return '
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-3 cursor-pointer" onclick="showStudentModal('.$row->id.')">
                        <img src="'.$avatar.'" alt="'.$row->name.'">
                    </div>
                    <div class="d-flex justify-content-start flex-column">
                        <div class="d-flex align-items-center flex-wrap gap-1">
                            <a href="javascript:;" onclick="showStudentModal('.$row->id.')" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">'.$row->name.'</a>
                            '.$genderIcon.'
                            <span class="ms-2">'.$pType.'</span>
                            '.$eType.'
                            '.$anomaly.'
                        </div>
                        <span class="text-gray-400 fw-semibold d-block fs-7">'.$email.'</span>
                    </div>
                </div>';
        });

        // Format DOB column — show date + computed age badge
        $datatable->editColumn('dob', function ($row) {
            if (empty($row->dob)) {
                return '<span class="badge badge-light-warning">غير محدد</span>';
            }
            $age = null;
            try { $age = \Carbon\Carbon::parse($row->dob)->age; } catch (\Exception $e) {}
            $dobDisplay = '<div class="d-flex flex-column align-items-center gap-1">';
            $dobDisplay .= '<span class="fw-bold text-dark fs-7"><i class="bi bi-calendar-event text-info me-1"></i>' . $row->dob . '</span>';
            if (!is_null($age)) {
                $ageClass = $age <= 15 ? 'badge-light-success' : 'badge-light-primary';
                $dobDisplay .= '<span class="badge ' . $ageClass . ' fw-bold fs-8"><i class="bi bi-stopwatch me-1"></i>' . $age . ' سنة</span>';
            }
            $dobDisplay .= '</div>';
            return $dobDisplay;
        });

        // Format join_date column
        $datatable->addColumn('join_date_fmt', function ($row) {
            $join = $row->join_date ?: ($row->created_at ? $row->created_at->format('Y-m-d') : null);
            if (!$join) {
                return '<span class="badge badge-light-warning">غير محدد</span>';
            }
            try {
                $c = \Carbon\Carbon::parse($join);
                $date = $c->format('Y-m-d');
                $human = $c->locale('ar')->diffForHumans();
                return '<div class="d-flex flex-column align-items-center gap-1">
                            <span class="fw-bold text-dark fs-7"><i class="bi bi-calendar-check text-success me-1"></i>' . $date . '</span>
                            <span class="text-muted fs-8 fst-italic">' . $human . '</span>
                        </div>';
            } catch (\Exception $e) {
                return '<span class="fw-bold">' . $join . '</span>';
            }
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.dashboard.parts.status_askmembership', $data)->render();
        });

        // Branch column
        $datatable->addColumn('branch_name', function ($row) {
            if ($row->branch) {
                $colors = ['info', 'primary', 'success', 'warning'];
                $color  = $colors[$row->branch->id % count($colors)];
                return '<span class="badge badge-light-' . $color . ' fw-bold px-3 py-2">'
                    . '<i class="bi bi-geo-fill me-1"></i>'
                    . e($row->branch->name_ar)
                    . '</span>';
            }
            return '<span class="badge badge-light-secondary text-muted px-3 py-2">—</span>';
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

        $datatable->rawColumns(['name', 'status', 'actions', 'checkbox', 'dob', 'join_date_fmt', 'branch_name']);

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
        $updated = Students_Admin_Messages::where('student_id', $id)->where('seen', 0)->update(['seen' => 1]);
        if ($updated > 0) {
            try {
                broadcast(new \App\Events\CountersUpdated());
            } catch (\Throwable $e) {
                Log::error('Broadcast CountersUpdated failed in MembershipsController@getStudentChatHistory: ' . $e->getMessage());
            }
        }

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
        $updated = Teachers_Admin_Messages::where('teacher_id', $id)->where('seen', 0)->update(['seen' => 1]);
        if ($updated > 0) {
            try {
                broadcast(new \App\Events\CountersUpdated());
            } catch (\Throwable $e) {
                Log::error('Broadcast CountersUpdated failed in MembershipsController@getTeacherChatHistory: ' . $e->getMessage());
            }
        }

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

    public function getStudentFinancials(Request $request)
    {
        $id = $request->get('id');
        $student = Students::findOrFail($id);
        
        // Fetch all fee records for this student
        $fees = GroupStudentsFees::with(['group.program', 'paymentMethod'])
            ->where('student_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // We can also get a summarized ledger if they have any group assignment
        $ledgers = [];
        $groupEnrollments = \App\Models\GroupStudents::where('student_id', $id)->get();
        foreach($groupEnrollments as $enrollment) {
            $ledgers[] = $this->financialService->getStudentLedger($id, $enrollment->group_id);
        }

        return view('admin.dashboard.parts.financial_details', [
            'student' => $student,
            'fees' => $fees,
            'ledgers' => $ledgers
        ]);
    }
}
