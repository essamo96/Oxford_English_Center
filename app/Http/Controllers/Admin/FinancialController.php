<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\GroupStudentsFees;
use App\Models\Students;
use App\Models\Groups;
use App\Models\FeeSettings;
use App\Services\FinancialService;
use Illuminate\Support\Facades\DB;

class FinancialController extends AdminController
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        parent::__construct();
        parent::$data['active_menu'] = 'financial';
        $this->financialService = $financialService;
    }

    /**
     * Display the list of pending financial orders.
     */
    public function pendingOrders()
    {
        return view('admin.financial.pending_orders', parent::$data);
    }

    /**
     * Get the data for pending financial orders DataTable.
     */
    public function getPendingList(Request $request)
    {
        // Fetch fees that are not yet verified (audit_status is pending)
        $query = GroupStudentsFees::with(['student', 'group'])
            ->where('audit_status', 'pending')
            ->orderBy('created_at', 'desc');

        // NEW: filter — only show students who took the placement test AND were graded
        if ($request->placement_graded == 1) {
            $query->whereHas('student.placementTests', function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('score')
                       ->orWhere('status', 'completed');
                });
            });
        }

        // NEW: filter — only placement-test fee rows
        if ($request->placement_test_only == 1) {
            $query->where('student_paid_type', 'like', '%Placement Test%');
        }

        return DataTables::of($query)
            ->addColumn('student', function ($row) {
                return $row->student ? $row->student->name : 'N/A';
            })
            ->addColumn('type', function ($row) {
                return $row->student_paid_type;
            })
            ->editColumn('receipt', function ($row) {
                if ($row->payment_receipt) {
                    $url = asset('uploads/' . $row->payment_receipt);
                    $ext = strtolower(pathinfo($row->payment_receipt, PATHINFO_EXTENSION));
                    $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    if ($isImg) {
                        return '<a href="'.$url.'" target="_blank" title="فتح بحجم كامل">
                                    <img src="'.$url.'" alt="receipt" style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #eee;box-shadow:0 2px 5px rgba(0,0,0,0.06);cursor:pointer;">
                                </a>';
                    }
                    return '<a href="'.$url.'" target="_blank" class="btn btn-sm btn-light-info"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>';
                }
                return '<span class="badge badge-light-danger">لا يوجد</span>';
            })
            ->editColumn('created_at', function ($row) {
                if (!$row->created_at) return '<span class="text-muted">—</span>';
                try {
                    $c = \Carbon\Carbon::parse($row->created_at);
                    $date = $c->format('Y-m-d');
                    $time = $c->format('h:i A');
                    $rel  = $c->locale('ar')->diffForHumans();
                    return '<div class="d-flex flex-column align-items-center gap-1">
                                <span class="fw-bold text-dark fs-7"><i class="bi bi-calendar-event text-info me-1"></i>'.$date.'</span>
                                <span class="text-muted fs-8"><i class="bi bi-clock me-1"></i>'.$time.'</span>
                                <span class="badge badge-light-info fs-9 fst-italic">'.$rel.'</span>
                            </div>';
                } catch (\Exception $e) {
                    return $row->created_at;
                }
            })
            ->addColumn('actions', function ($row) {
                $pId = $row->student ? $row->student->program_id : 0;
                $sId = $row->student ? $row->student->id : 0;
                $type = (string) $row->student_paid_type;
                // Use data-attributes and classes to avoid inline JS string-escaping issues
                return '<button data-id="'.$row->id.'" data-claimed="'.$row->student_fee_paid.'" data-total="'.$row->total_due_amount.'" data-program="'.$pId.'" data-student="'.$sId.'" data-type="'.e($type).'" class="btn btn-sm btn-success btn-verify">تأكيد</button>
                        <button data-id="'.$row->id.'" class="btn btn-sm btn-danger btn-refund">رفض</button>';
            })
            ->rawColumns(['receipt', 'actions', 'created_at'])
            ->make(true);
    }

    /**
     * Return basic student + guardian details for the pending-orders verify modal.
     */
    public function getPendingStudentDetails($studentId)
    {
        $student = \App\Models\Students::with('parent')->find($studentId);
        if (!$student) {
            return response()->json(['success' => false], 404);
        }

        $age = null;
        if ($student->dob) {
            try { $age = \Carbon\Carbon::parse($student->dob)->age; } catch (\Exception $e) {}
        }

        $isChild = ($student->program_type === 'kids') || ($age !== null && $age <= 15);

        return response()->json([
            'success'    => true,
            'student'    => [
                'id'           => $student->id,
                'name'         => $student->name,
                'name_en'      => $student->name_en,
                'mobile'       => $student->mobile,
                'email'        => $student->email,
                'dob'          => $student->dob,
                'age'          => $age,
                'program_type' => $student->program_type,
                'is_child'     => $isChild,
            ],
            'parent'     => $student->parent ? [
                'name'         => $student->parent->name,
                'phone'        => $student->parent->phone,
                'email'        => $student->parent->email,
                'relationship' => $student->parent->relationship,
            ] : null,
        ]);
    }

    /**
     * Verify the payment and activate the student.
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'id'                   => 'required|exists:group_students_fees,id',
            'verified_amount'      => 'required|numeric|min:0',
            'group_id'             => 'nullable|exists:groups,id',
            'program_id'           => 'nullable|exists:programs,id',
            // Optional: admin changing the student's chosen program
            'change_program_to'    => 'nullable|exists:programs,id',
        ]);

        DB::beginTransaction();
        try {
            $fee = GroupStudentsFees::find($request->id);

            // Detect placement-test fee (independent of course enrollment)
            $isPlacementTestFee = (bool) preg_match('/Placement\s*Test/i', (string) $fee->student_paid_type)
                || str_contains((string) $fee->student_paid_type, 'اختبار');

            $student = Students::find($fee->student_id);

            // -------- OPTIONAL: change-program flow --------
            // Admin chose a different program than what student originally picked.
            // Recompute total_due from FeeSettings of the new program and credit the
            // verified_amount toward it (so remaining = new_total - verified).
            $newTotal = (float) $fee->total_due_amount;
            if ($request->change_program_to && !$isPlacementTestFee) {
                $newProgramFee = (float) \App\Models\FeeSettings::where('program_id', $request->change_program_to)
                    ->whereIn('type', ['course', 'course_fee'])
                    ->sum('amount');
                if ($newProgramFee > 0) {
                    $newTotal = $newProgramFee;
                    $fee->total_due_amount = $newTotal;
                    $fee->notes = trim(($fee->notes ?? '') . ' | تغيير البرنامج بواسطة الأدمن إلى program_id='.$request->change_program_to);
                }
            }

            $fee->admin_verified_amount = $request->verified_amount;
            $fee->transaction_amount    = $request->verified_amount;
            $fee->transaction_type      = 'payment';
            $fee->remaining_amount      = max(0, $newTotal - (float) $request->verified_amount);
            $fee->status                = 'confirmed';
            $fee->audit_status          = 'verified';
            if ($request->group_id) {
                $fee->group_id = $request->group_id;
            }
            $fee->save();

            $emailReports = [];

            if ($student) {
                // Assignment to group only when not placement-test
                if ($request->group_id && !$isPlacementTestFee) {
                    $exists = \App\Models\GroupStudents::where('student_id', $student->id)
                        ->where('group_id', $request->group_id)
                        ->whereNull('deleted_at')->exists();
                    if (!$exists) {
                        $group = \App\Models\Groups::find($request->group_id);
                        $programFee = (float) \App\Models\FeeSettings::where('program_id', $group->program_id)
                            ->whereIn('type', ['course', 'course_fee'])
                            ->sum('amount') ?: (float) $fee->total_due_amount;

                        \App\Models\GroupStudents::create([
                            'student_id'        => $student->id,
                            'group_id'          => $request->group_id,
                            'student_fee_total' => $programFee,
                            'status'            => 1,
                        ]);
                    }
                }

                // --------- ACTIVATION + EMAILS ---------
                // Rule:
                //   Placement-test path → confirm payment only, NEVER activate.
                //   All other paths → activate + send 2 emails (activation + payment confirmation).
                if ($isPlacementTestFee) {
                    // Mark fee verified but keep status=0 (not active)
                    $student->is_verified = 1;
                    $student->save();

                    // Single email: payment confirmation only
                    try {
                        \Illuminate\Support\Facades\Mail::to($student->email)
                            ->send(new \App\Mail\PaymentVerifiedMail($student, $fee));
                        $emailReports[] = 'تم إرسال تأكيد الدفع';
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Payment confirmation email failed: ' . $e->getMessage());
                    }
                } else {
                    // Activate the account
                    $student->is_verified = 1;
                    $student->status      = 1;
                    $student->save();

                    // Email 1: payment confirmation
                    try {
                        \Illuminate\Support\Facades\Mail::to($student->email)
                            ->send(new \App\Mail\PaymentVerifiedMail($student, $fee));
                        $emailReports[] = 'تم إرسال تأكيد الدفع';
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Payment confirmation email failed: ' . $e->getMessage());
                    }
                    // Email 2: account activation (Welcome / activation notice)
                    try {
                        \Illuminate\Support\Facades\Mail::to($student->email)
                            ->send(new \App\Mail\WelcomeStudentMail($student));
                        $emailReports[] = 'تم إرسال إشعار تفعيل الحساب';
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Activation email failed: ' . $e->getMessage());
                    }
                }
            }

            // Sync placement_tests record when relevant
            if ($isPlacementTestFee || str_contains((string) $fee->student_paid_type, 'Test')) {
                $test = \App\Models\PlacementTests::where('student_id', $fee->student_id)
                    ->where('status', 'pending')
                    ->first();
                if ($test) {
                    $test->paid_amount        = $request->verified_amount;
                    $test->remaining_amount   = max(0, $test->total_amount - $request->verified_amount);
                    $test->status             = 'payment_confirmed';
                    $test->payment_status     = ($test->remaining_amount <= 0) ? 'paid' : 'partially_paid';
                    $test->save();
                }
            }

            DB::commit();

            // Compose user-facing success message
            $msg = $isPlacementTestFee
                ? 'تم تأكيد دفعة اختبار تحديد المستوى. لم يُفعَّل حساب الطالب (الاختبار لا يُفعّل الحساب).'
                : ($request->group_id
                    ? 'تم تأكيد الدفعة وتفعيل الحساب وتسكين الطالب في المجموعة.'
                    : 'تم تأكيد الدفعة وتفعيل الحساب (لم يُسكَّن في مجموعة بعد).');
            if (!empty($emailReports)) {
                $msg .= ' (' . implode(' · ', $emailReports) . ')';
            }

            return response()->json(['status' => 'success', 'message' => $msg]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء المعالجة: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject/Refund the payment.
     */
    public function refundPayment(Request $request)
    {
        // Implementation for rejection logic (e.g. notify student)
        return response()->json(['status' => 'info', 'message' => 'سيتم تنفيذ منطق الرفض قريباً.']);
    }

    /**
     * Manage fee settings (index page).
     */
    public function feeSettings()
    {
        parent::$data['active_menu'] = 'financial_fees';
        parent::$data['programs'] = \App\Models\Programs::where('status', 1)->get();
        parent::$data['fees']     = FeeSettings::with('program')->orderBy('program_id')->orderBy('type')->get();
        parent::$data['fee_types'] = \App\Models\FeeType::all();
        return view('admin.financial.fee_settings', parent::$data);
    }

    /**
     * Display the financial ledger (Student Invoices).
     */
    public function postRecordPayment(Request $request)
    {
        $request->validate([
            'id'     => 'required|exists:group_students_fees,id',
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $baseFee = GroupStudentsFees::findOrFail($request->id);
            // Works for BOTH group-based fees AND placement-test / pre-group fees (group_id null)
            $ledger  = $this->financialService->getStudentLedger($baseFee->student_id, $baseFee->group_id);

            if (!$ledger) {
                return response()->json(['status' => 'error', 'message' => 'لم يتم العثور على سجل مالي لهذا الطالب!']);
            }

            if ((float) $request->amount > (float) $ledger['remaining_balance'] + 0.001) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'المبلغ المدخل أكبر من المتبقي (' . number_format($ledger['remaining_balance'], 2) . ' ILS)!',
                ]);
            }

            $this->financialService->recordTransaction([
                'student_id'        => $baseFee->student_id,
                'group_id'          => $baseFee->group_id, // may be null — that's fine
                'amount'            => $request->amount,
                'verified_amount'   => $request->amount,
                'audit_status'      => 'verified',
                'type'              => 'payment',
                'notes'             => 'دفعة يدوية من الإدارة',
                'paid_type'         => $baseFee->student_paid_type ?: 'Manual Payment',
                'payment_method_id' => $request->payment_method_id ?? $baseFee->payment_method_id,
            ]);

            // If this was a placement-test ledger and it's now fully paid, mark the test paid
            if (!$baseFee->group_id) {
                $fresh = $this->financialService->getStudentLedger($baseFee->student_id, null);
                if ($fresh && $fresh['remaining_balance'] <= 0) {
                    \App\Models\PlacementTests::where('student_id', $baseFee->student_id)
                        ->where('payment_status', '!=', 'paid')
                        ->update(['payment_status' => 'paid', 'remaining_amount' => 0]);
                }
            }

            return response()->json(['status' => 'success', 'message' => 'تم تسجيل الدفعة في السجل المالي بنجاح.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    /**
     * Return every invoice/transaction for a student across all ledgers.
     * Rendered into the "All Invoices" modal on the invoices ledger page.
     */
    public function getStudentInvoices($studentId)
    {
        $student = Students::find($studentId);
        if (!$student) {
            return response('<div class="alert alert-danger m-5">الطالب غير موجود</div>', 404);
        }

        $data = $this->financialService->getStudentAllInvoices($studentId);
        return view('admin.financial.parts.student_invoices_modal', [
            'student' => $student,
            'data'    => $data,
        ])->render();
    }

    public function invoicesLedger()
    {
        parent::$data['active_menu'] = 'financial_invoices';

        $stats = $this->financialService->getGlobalStats();

        parent::$data['total_collected'] = $stats['total_collected'];
        parent::$data['total_remaining'] = $stats['total_remaining'];
        parent::$data['pending_amount']  = $stats['pending_amount'];

        // For the filter UI
        parent::$data['programs_filter'] = \App\Models\Programs::where('status', 1)
            ->orderBy('title')->get(['id', 'title']);
        parent::$data['levels_filter']   = \App\Models\FeeSettings::whereNotNull('level_name')
            ->where('level_name', '!=', '')->distinct()->orderBy('level_name')
            ->pluck('level_name');

        return view('admin.financial.invoices', parent::$data);
    }

    /**
     * Get data for invoices ledger DataTable.
     */
    public function getInvoicesLedgerList(Request $request)
    {
        $query = GroupStudentsFees::with(['student', 'group.program', 'paymentMethod'])
            ->select('group_students_fees.*', 'group_students.student_fee_total as total_invoice')
            ->join('students', 'group_students_fees.student_id', '=', 'students.id')
            ->leftJoin('group_students', function($join) {
                $join->on('group_students.student_id', '=', 'group_students_fees.student_id')
                     ->on('group_students.group_id', '=', 'group_students_fees.group_id');
            })
            ->leftJoin('groups as fg', 'fg.id', '=', 'group_students_fees.group_id');
            
        // Apply Filters (all combined with AND)
        if ($request->program_type) {
            $query->where('students.program_type', $request->program_type);
        }

        if ($request->search_text) {
            $search = $request->search_text;
            $query->where(function($q) use ($search) {
                $q->where('students.name', 'like', "%$search%")
                  ->orWhere('students.mobile', 'like', "%$search%")
                  ->orWhere('students.email', 'like', "%$search%");
            });
        }

        // NEW: Has-outstanding filter (remaining_amount > 0)
        if ($request->only_outstanding == 1) {
            $query->where('group_students_fees.remaining_amount', '>', 0);
        }

        // NEW: filter by specific program (joins through fg → program)
        if ($request->program_id) {
            $query->where('fg.program_id', $request->program_id);
        }

        // NEW: filter by level (current_level)
        if ($request->level) {
            $query->where('students.current_level', $request->level);
        }

        return DataTables::of($query)
            ->editColumn('student', function ($row) {
                if (!$row->student) return 'N/A';
                $pType = $row->student->program_type == 'kids' 
                    ? '<span class="badge badge-light-success fs-8 fw-bold ms-2">KIDS</span>'
                    : '<span class="badge badge-light-primary fs-8 fw-bold ms-2">ADULT</span>';
                return $row->student->name . $pType;
            })
            ->editColumn('program_level', function ($row) {
                $program = ($row->group && $row->group->program) ? $row->group->program->title : 'N/A';
                $level = $row->group ? $row->group->name : 'N/A';
                return $program . ' / ' . $level;
            })
            ->editColumn('admin_verified_amount', function ($row) {
                $prefix = $row->transaction_type == 'refund' ? '-' : '';
                return '<span class="fw-bold">'.$prefix . number_format($row->transaction_amount, 2) . ' ILS</span>';
            })
            ->editColumn('remaining_amount', function ($row) {
                $ledger = $this->financialService->getStudentLedger($row->student_id, $row->group_id);
                // Fallbacks: (1) ledger when present, (2) row's stored remaining_amount,
                // (3) total_due - student_fee_paid for legacy rows.
                if ($ledger) {
                    $rem = $ledger['remaining_balance'];
                } elseif (!is_null($row->remaining_amount)) {
                    $rem = (float) $row->remaining_amount;
                } else {
                    $rem = (float) ($row->total_due_amount ?? 0) - (float) ($row->student_fee_paid ?? 0);
                }
                $rem = max(0, $rem);
                $color = $rem > 0 ? 'danger' : 'success';
                $iconCls = $rem > 0 ? 'bi-exclamation-circle' : 'bi-check-circle';
                return '<span class="text-'.$color.' fw-bold"><i class="bi '.$iconCls.' me-1"></i>'.number_format($rem, 2) . ' ILS</span>';
            })
            ->editColumn('audit_status', function ($row) {
                $statusMap = [
                    'pending'  => ['label' => 'قيد التدقيق', 'class' => 'badge-light-warning'],
                    'verified' => ['label' => 'مؤكد',      'class' => 'badge-light-success'],
                    'rejected' => ['label' => 'مرفوض',     'class' => 'badge-light-danger'],
                ];
                $s = $statusMap[$row->audit_status] ?? ['label' => $row->audit_status, 'class' => 'badge-light-secondary'];
                return '<span class="badge '.$s['class'].'">'.$s['label'].'</span>';
            })
            ->addColumn('actions', function ($row) {
                $btns = '<div class="d-flex justify-content-end gap-2">';

                // All-invoices modal (works for every student, group or not)
                $btns .= '<button type="button" onclick="showAllInvoices('.$row->student_id.')" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm" title="كل فواتير الطالب">
                            <i class="bi bi-receipt-cutoff fs-3"></i>
                          </button>';

                // Detailed group ledger (only when a group exists)
                if ($row->group_id) {
                    $btns .= '<a href="'.route('admin.financial.ledger', ['studentId' => $row->student_id, 'groupId' => $row->group_id]).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" title="السجل المالي للمجموعة">
                                <i class="ki-duotone ki-book-open fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                              </a>';
                }

                $btns .= '<button type="button" onclick="showStudentModal('.$row->student_id.')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="عرض الملف">
                            <i class="ki-duotone ki-user fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                          </button>';

                // Payment button — works for BOTH group ledgers and placement / pre-group ledgers
                if ($row->audit_status == 'verified') {
                    $ledger    = $this->financialService->getStudentLedger($row->student_id, $row->group_id);
                    $remaining = $ledger ? (float) $ledger['remaining_balance']
                                         : max(0, (float) ($row->total_due_amount ?? 0) - (float) ($row->student_fee_paid ?? 0));
                    if ($remaining > 0) {
                        $btns .= '<button type="button" onclick="recordPayment('.$row->id.', '.number_format($remaining, 2, '.', '').')" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm" title="تسديد دفعة">
                                    <i class="ki-duotone ki-dollar fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                  </button>';
                    }
                }

                $btns .= '</div>';
                return $btns;
            })
            ->rawColumns(['student', 'admin_verified_amount', 'remaining_amount', 'audit_status', 'actions'])
            ->make(true);
    }

    public function ledger(Request $request)
    {
        $studentId = $request->studentId;
        $groupId   = $request->groupId;

        $ledger = $this->financialService->getStudentLedger($studentId, $groupId);

        if (!$ledger) {
            return redirect()->back()->with('error', 'السجل المالي غير موجود لهذا الطالب.');
        }

        parent::$data['active_menu'] = 'financial_invoices';
        parent::$data['ledger'] = $ledger;
        parent::$data['student'] = Students::find($studentId);

        return view('admin.financial.ledger', parent::$data);
    }

    /**
     * Update or create fee setting.
     */
    public function updateFeeSetting(Request $request)
    {
        $typeSlugs = \App\Models\FeeType::pluck('slug')->toArray();

        $request->validate([
            'program_id'         => 'required|exists:programs,id',
            'amount'             => 'required|numeric|min:0',
            'type'               => 'required|in:' . implode(',', $typeSlugs),
            'level_name'         => 'nullable|string|max:50',
            'min_payment_percent'=> 'nullable|numeric|min:0|max:100',
            'min_payment_fixed'  => 'nullable|numeric|min:0',
        ]);

        // Save the fee row WITHOUT min-payment fields (those live on the program now)
        FeeSettings::updateOrCreate(
            [
                'program_id' => $request->program_id,
                'type'       => $request->type,
                'level_name' => $request->level_name ?: null,
            ],
            [
                'amount'   => $request->amount,
                'currency' => 'ILS',
            ]
        );

        // Persist min-payment thresholds at the PROGRAM level
        \App\Models\Programs::where('id', $request->program_id)->update([
            'min_payment_percent' => $request->filled('min_payment_percent') ? $request->min_payment_percent : null,
            'min_payment_fixed'   => $request->filled('min_payment_fixed')   ? $request->min_payment_fixed   : null,
        ]);

        return redirect()->back()->with('success', 'تم تحديث إعدادات الرسوم بنجاح.');
    }

    /**
     * API: return a program's min-payment settings (used by the admin fees form
     * to pre-fill the inputs when an admin selects a program).
     */
    public function getProgramMinPayment($programId)
    {
        $program = \App\Models\Programs::find($programId);
        if (!$program) {
            return response()->json(['success' => false], 404);
        }
        return response()->json([
            'success'             => true,
            'min_payment_percent' => $program->min_payment_percent,
            'min_payment_fixed'   => $program->min_payment_fixed,
        ]);
    }

    /**
     * Delete a fee setting.
     */
    public function deleteFeeSetting($id)
    {
        $fee = FeeSettings::findOrFail($id);
        $fee->delete();
        return redirect()->back()->with('success', 'تم حذف نوع الرسوم بنجاح.');
    }

    /**
     * Get groups/levels for a given program (AJAX).
     * Returns distinct group names to use as level selectors.
     */
    public function getGroupsByProgram($programId)
    {
        $groups = Groups::where('program_id', $programId)
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->where('status', 1)
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->get()
            ->map(function ($g) {
                return ['level' => $g->name];
            });

        return response()->json($groups);
    }

    /**
     * Get all fee types for management (AJAX).
     */
    public function getFeeTypes()
    {
        return response()->json(\App\Models\FeeType::all());
    }

    /**
     * Store or update a fee type.
     */
    public function storeFeeType(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'slug'    => 'required|string|max:50|unique:fee_types,slug,' . $request->id,
            'icon'    => 'nullable|string|max:50',
            'class'   => 'nullable|string|max:50',
        ]);

        \App\Models\FeeType::updateOrCreate(
            ['id' => $request->id],
            [
                'name_ar' => $request->name_ar,
                'slug'    => $request->slug,
                'icon'    => $request->icon ?: 'bi-tag',
                'class'   => $request->class ?: 'badge-light-primary',
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'تم حفظ نوع الرسوم بنجاح.']);
    }

    /**
     * Delete a fee type.
     */
    public function deleteFeeType($id)
    {
        $type = \App\Models\FeeType::findOrFail($id);
        if (FeeSettings::where('type', $type->slug)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'لا يمكن حذف هذا النوع لوجود رسوم مرتبطة به.']);
        }
        $type->delete();
        return response()->json(['status' => 'success', 'message' => 'تم حذف نوع الرسوم.']);
    }

    /**
     * API: Get all fees associated with a program.
     */
    public function getFee(Request $request)
    {
        $programId = $request->program_id;
        $level     = $request->level_name;

        $query = \App\Models\FeeSettings::where('program_id', $programId);
        $fees  = $query->get();

        $program = \App\Models\Programs::find($programId);

        return response()->json([
            'success'             => true,
            'fees'                => $fees,
            'min_payment_percent' => $program ? $program->min_payment_percent : null,
            'min_payment_fixed'   => $program ? $program->min_payment_fixed   : null,
        ]);
    }

    /**
     * Get actual group objects for a program.
     */
    public function getActualGroupsByProgram($programId)
    {
        $groups = Groups::where('program_id', $programId)
            ->where('status', 1)
            ->select('id', 'name', 'start_date')
            ->orderBy('id', 'desc')
            ->get();
            
        return response()->json($groups);
    }
}
