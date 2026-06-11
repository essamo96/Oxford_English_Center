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
        parent::$data['payment_methods'] = \App\Models\PaymentMethods::where('is_active', 1)
            ->orderBy('name')->get(['id', 'name']);
        return view('admin.financial.pending_orders', parent::$data);
    }

    /**
     * Get the data for pending financial orders DataTable.
     */
    public function getPendingList(Request $request)
    {
        // Fetch fees that are not yet verified (audit_status is pending)
        $query = GroupStudentsFees::with(['student', 'group.program', 'program'])
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

        // Branch filter
        $branchId = app(\App\Services\BranchContext::class)->getId()
                 ?? ($request->get('branch_id') ?: null);
        if ($branchId) {
            $query->whereHas('student', fn($q) => $q->where('branch_id', $branchId));
        }

        return DataTables::of($query)
            ->addColumn('student', function ($row) {
                return $row->student ? $row->student->name : 'N/A';
            })
            ->addColumn('type', function ($row) {
                return $row->student_paid_type;
            })
            ->addColumn('program_group', function ($row) {
                // The program the applicant was branched into (stored on the fee row at
                // registration; falls back to the student record for legacy data), plus the
                // group/level if the student has already been seated.
                $program = $row->program
                    ?: ($row->group && $row->group->program ? $row->group->program : null)
                    ?: ($row->student && $row->student->program_id
                        ? \App\Models\Programs::find($row->student->program_id) : null);

                $programTitle = $program ? $program->title : null;
                $groupName    = $row->group ? $row->group->name : null;

                if (!$programTitle && !$groupName) {
                    return '<span class="badge badge-light-secondary fs-8"><i class="bi bi-dash-circle me-1"></i>لم يُحدَّد بعد</span>';
                }

                $html = '<div class="d-flex flex-column align-items-center gap-1">';
                if ($programTitle) {
                    $html .= '<span class="badge badge-light-primary fw-bold fs-8 px-3 py-2">
                                <i class="bi bi-mortarboard-fill me-1"></i>'.e($programTitle).'
                              </span>';
                }
                if ($groupName) {
                    $html .= '<span class="badge badge-light-info fs-9 px-3">
                                <i class="bi bi-people-fill me-1"></i>'.e($groupName).'
                              </span>';
                } else {
                    $html .= '<span class="text-muted fs-9 fst-italic">لم يُسكَّن في مجموعة بعد</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->editColumn('receipt', function ($row) {
                if ($row->payment_receipt) {
                    $url = asset('uploads/' . $row->payment_receipt);
                    $ext = strtolower(pathinfo($row->payment_receipt, PATHINFO_EXTENSION));
                    $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $student = $row->student ? e($row->student->name) : '';
                    if ($isImg) {
                        return '<span class="receipt-thumb d-inline-block position-relative" data-receipt-url="'.$url.'" data-receipt-type="image" data-student="'.$student.'" title="عرض الإيصال" style="cursor:pointer;">
                                    <img src="'.$url.'" alt="receipt" style="width:62px;height:62px;object-fit:cover;border-radius:10px;border:1px solid #eee;box-shadow:0 2px 6px rgba(0,0,0,0.08);">
                                    <span class="position-absolute top-50 start-50 translate-middle badge rounded-circle bg-dark bg-opacity-50 p-2"><i class="bi bi-zoom-in text-white fs-6"></i></span>
                                </span>';
                    }
                    return '<button type="button" class="btn btn-sm btn-light-info receipt-thumb" data-receipt-url="'.$url.'" data-receipt-type="pdf" data-student="'.$student.'"><i class="bi bi-file-earmark-pdf me-1"></i> عرض الإيصال</button>';
                }
                return '<span class="badge badge-light-danger">لا يوجد</span>';
            })
            // add class d-flex align-items-center to student name column for better vertical alignment with the receipt thumbnail
            ->editColumn('student', function ($row) {
                $name = $row->student ? e($row->student->name) : 'N/A';
                return '<div class="d-flex align-items-center justify-content-center gap-2 text-center">
                            <i class="bi bi-person-fill text-primary fs-5"></i>
                            <span class="fw-bold text-dark">'.$name.'</span>
                        </div>';
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
            ->addColumn('branch_name', function ($row) {
                $branch = $row->student->branch ?? null;
                if ($branch) {
                    $colors = ['info', 'primary', 'success', 'warning'];
                    $color  = $colors[$branch->id % count($colors)];
                    return '<span class="badge badge-light-' . $color . ' fw-bold px-2 py-1">'
                        . '<i class="bi bi-geo-fill me-1"></i>'
                        . e($branch->name_ar)
                        . '</span>';
                }
                return '<span class="badge badge-light-secondary text-muted px-2 py-1">—</span>';
            })
            ->addColumn('actions', function ($row) {
                // Pick the program the applicant actually chose at registration time.
                // Stored directly on the fee row now; fall back to the student record for legacy data.
                $pId   = $row->program_id ?: ($row->student->program_id ?? 0);
                $gId   = $row->group_id ?: 0; // group the admin seated the student in (pre-selected in the modal)
                $gName = $row->group ? e($row->group->name) : ''; // shown in the "already seated" notice
                $pmId  = $row->payment_method_id ?: 0; // stored payment method (pre-selected in the modal)
                $sId   = $row->student ? $row->student->id : 0;
                $type  = (string) $row->student_paid_type;
                return '<button data-id="'.$row->id.'" data-claimed="'.$row->student_fee_paid.'" data-total="'.$row->total_due_amount.'" data-program="'.$pId.'" data-group="'.$gId.'" data-group-name="'.$gName.'" data-payment="'.$pmId.'" data-student="'.$sId.'" data-type="'.e($type).'" class="btn btn-sm btn-success btn-verify">تأكيد</button>
                        <button data-id="'.$row->id.'" class="btn btn-sm btn-danger btn-refund">رفض</button>';
            })
            ->rawColumns(['receipt','student', 'actions', 'created_at', 'program_group', 'branch_name'])
            ->make(true);
    }

    /**
     * AJAX: compute the price-difference summary when admin changes the program in the modal.
     *
     * Inputs (query string):
     *   fee_id           – the pending fee row being verified
     *   new_program_id   – the program the admin wants to move them to
     *   verified_amount  – the amount the admin will actually verify (defaults to student_fee_paid)
     *
     * Returns:
     *   original_fee     – total_due_amount from the registration row
     *   new_fee          – sum of fees for the chosen program (excluding placement_test)
     *   delta            – new_fee - original_fee  (positive ⇒ student owes more)
     *   verified         – the actual amount admin will count
     *   outstanding      – new_fee - verified      (positive ⇒ remaining, negative ⇒ credit)
     *   credit_amount    – how much excess (if any) the student has already paid
     *   recommendation   – textual hint for the admin (which action to take)
     */
    public function computeProgramSwapDiff(Request $request)
    {
        $request->validate([
            'fee_id'          => 'required|exists:group_students_fees,id',
            'new_program_id'  => 'required|exists:programs,id',
            'verified_amount' => 'nullable|numeric|min:0',
        ]);

        $fee = GroupStudentsFees::find($request->fee_id);
        $verified = $request->filled('verified_amount')
            ? (float) $request->verified_amount
            : (float) ($fee->student_fee_paid ?? 0);

        $originalFee = (float) ($fee->total_due_amount ?? 0);

        // New program's course fee (exclude placement_test — that's billed separately)
        $newFee = (float) \App\Models\FeeSettings::where('program_id', $request->new_program_id)
            ->where('type', '!=', 'placement_test')
            ->sum('amount');

        $delta       = $newFee - $originalFee;        // > 0 → owes more, < 0 → originally over-priced
        $outstanding = $newFee - $verified;           // > 0 → remaining, < 0 → credit
        $credit      = $outstanding < 0 ? abs($outstanding) : 0.0;

        // Build a recommendation for the admin
        if (abs($outstanding) < 0.01) {
            $reco = ['type' => 'balanced',  'message' => '✅ المبلغ المدفوع يطابق الرسوم الجديدة تماماً.'];
        } elseif ($outstanding > 0) {
            $reco = ['type' => 'remaining', 'message' => '⚠️ متبقّي على الطالب ' . number_format($outstanding, 2) . ' ILS بعد التغيير.'];
        } else {
            $reco = ['type' => 'credit',    'message' => 'ℹ️ الطالب دفع زيادة ' . number_format($credit, 2) . ' ILS — يمكنك إيداعها كرصيد له أو ردّها.'];
        }

        return response()->json([
            'success'       => true,
            'original_fee'  => $originalFee,
            'new_fee'       => $newFee,
            'delta'         => $delta,
            'verified'      => $verified,
            'outstanding'   => max(0, $outstanding),
            'credit_amount' => $credit,
            'recommendation'=> $reco,
        ]);
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
     * Financial breakdown for a pending fee row — used by the verify modal to show how much of
     * the due was already covered by the student's CREDIT balance, the minimum payment, and how
     * much cash is still required to confirm the seating.
     */
    public function getPendingFinancials($feeId)
    {
        $fee = GroupStudentsFees::with('group')->find($feeId);
        if (!$fee) {
            return response()->json(['success' => false], 404);
        }

        $studentId = $fee->student_id;
        $groupId   = $fee->group_id;

        // Total due: prefer the group's enrolment fee, else the row's own due
        $totalDue = (float) $fee->total_due_amount;
        if ($groupId) {
            $gs = \App\Models\GroupStudents::where('student_id', $studentId)
                ->where('group_id', $groupId)->first();
            if ($gs && $gs->student_fee_total > 0) $totalDue = (float) $gs->student_fee_total;
        }

        // Already paid for this enrolment (the credit auto-applied at branching time)
        $ledger    = $this->financialService->getStudentLedger($studentId, $groupId);
        $paidSoFar = $ledger ? (float) $ledger['total_paid'] : 0.0;
        $remaining = $ledger ? (float) $ledger['remaining_balance'] : max(0, $totalDue - $paidSoFar);

        // Minimum required payment for the program (credit already counts toward it)
        $programId  = $fee->program_id ?: optional($fee->group)->program_id;
        $program    = $programId ? \App\Models\Programs::find($programId) : null;
        $minPayment = $program ? (float) $program->computeMinimumDue($totalDue) : 0.0;

        // Minimum CASH the student must still pay now = minimum − what credit already covered
        $minCashNow = max(0, round($minPayment - $paidSoFar, 2));

        // Credit still available on the student's ledger (not yet consumed)
        $creditAvailable = (float) GroupStudentsFees::where('student_id', $studentId)
            ->where('audit_status', 'verified')
            ->where('transaction_type', 'credit')
            ->sum('transaction_amount');

        // "Seated" = the student has an ACTIVE group membership for this group. Only then is the
        // program locked. A row that merely carries a program (registration, or branched without
        // an active seat) must NOT lock the program.
        $isSeated = $groupId
            ? \App\Models\GroupStudents::where('student_id', $studentId)
                ->where('group_id', $groupId)->whereNull('deleted_at')->exists()
            : false;

        return response()->json([
            'success'          => true,
            'total_due'        => round($totalDue, 2),
            'paid_from_credit' => round($paidSoFar, 2),
            'remaining'        => round(max(0, $remaining), 2),
            'min_payment'      => round($minPayment, 2),
            'min_cash_now'     => $minCashNow,
            'credit_available' => round(max(0, $creditAvailable), 2),
            'is_seated'        => $isSeated,
            'group_name'       => optional($fee->group)->name,
            'has_credit'       => $paidSoFar > 0.009,
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
            // When admin moves the student to a CHEAPER program and over-payment exists:
            //   'keep'   → store as credit balance on the student's ledger
            //   'refund' → record a refund transaction
            'credit_action'        => 'nullable|in:keep,refund',
            // Optional proof image the admin attaches when refunding the surplus to the student
            'refund_receipt'       => 'nullable|image|max:5120',
            // Payment method + the payment notice the admin attaches when confirming
            'payment_method_id'    => 'nullable|exists:payment_methods,id',
            'payment_receipt'      => 'nullable|image|max:5120',
        ]);

        // Admin (users.id) performing this confirmation — stored on every row we touch
        // so the ledger shows who approved each financial movement.
        $adminId = optional(\Illuminate\Support\Facades\Auth::guard('admin')->user())->id;

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
                // Use the SAME formula the swap-diff panel shows the admin (every fee except
                // the placement test) and apply it UNCONDITIONALLY — even when the new program
                // is free / has no fees configured. In that case newTotal becomes 0, so the
                // whole verified amount is correctly recognised as a credit owed to the student.
                $newProgramFee = (float) \App\Models\FeeSettings::where('program_id', $request->change_program_to)
                    ->where('type', '!=', 'placement_test')
                    ->sum('amount');
                $newTotal = $newProgramFee;
                $fee->total_due_amount = $newTotal;
                $fee->notes = trim(($fee->notes ?? '') . ' | تغيير البرنامج بواسطة الأدمن إلى program_id='.$request->change_program_to);
            }

            // Prior verified payments already on this group's ledger (e.g. credit auto-applied at
            // branching). The cash entered now is ADDED to them — so the remaining is computed as
            // total − (prior payments + this cash), not just total − cash.
            $priorPaid = 0.0;
            if ($fee->group_id) {
                $priorPaid = (float) GroupStudentsFees::where('student_id', $fee->student_id)
                    ->where('group_id', $fee->group_id)
                    ->where('id', '!=', $fee->id)
                    ->where('audit_status', 'verified')
                    ->where(function ($q) {
                        $q->where('transaction_type', 'payment')->orWhereNull('transaction_type');
                    })
                    ->sum('transaction_amount');
            }

            $fee->admin_verified_amount = $request->verified_amount;
            $fee->transaction_amount    = $request->verified_amount;
            $fee->transaction_type      = 'payment';
            $fee->remaining_amount      = max(0, $newTotal - $priorPaid - (float) $request->verified_amount);
            $fee->status                = 'confirmed';
            $fee->audit_status          = 'verified';
            $fee->verified_by           = $adminId;
            if ($request->payment_method_id) {
                $fee->payment_method_id = $request->payment_method_id;
            }
            if ($request->hasFile('payment_receipt')) {
                $file = $request->file('payment_receipt');
                $filename = 'pay_' . time() . '_' . $fee->student_id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/receipts'), $filename);
                $fee->payment_receipt = 'receipts/' . $filename;
            }
            if ($request->group_id) {
                $fee->group_id = $request->group_id;
            }
            if ($request->change_program_to) {
                $fee->program_id = $request->change_program_to;
            }
            $fee->save();

            // -------- Over-payment handling (only when program was swapped to a cheaper one) --------
            $extraEmailNote = null;
            if ($request->change_program_to && !$isPlacementTestFee) {
                $verified = (float) $request->verified_amount;
                $overPay  = $verified - $newTotal;          // > 0 means student paid extra
                if ($overPay > 0.01) {
                    $action = $request->credit_action ?: 'keep';
                    if ($action === 'refund') {
                        // Optional refund-settlement proof the admin uploads (image of the
                        // refund notice handed to the student).
                        $refundReceiptPath = null;
                        if ($request->hasFile('refund_receipt')) {
                            $file = $request->file('refund_receipt');
                            $filename = 'refund_' . time() . '_' . $fee->student_id . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('uploads/receipts'), $filename);
                            $refundReceiptPath = 'receipts/' . $filename;
                        }
                        // Record a refund row. Keep the original payment intact so the ledger
                        // naturally nets to (paid 1350) - (refund 500) = 850 = newTotal.
                        GroupStudentsFees::create([
                            'student_id'        => $fee->student_id,
                            'group_id'          => $fee->group_id,
                            'program_id'        => $fee->program_id,
                            'student_fee_paid'  => 0,
                            'transaction_amount'=> $overPay,
                            'transaction_type'  => 'refund',
                            'total_due_amount'  => 0,
                            'remaining_amount'  => 0,
                            'student_paid_type' => 'Program Swap Refund',
                            'payment_receipt'   => $refundReceiptPath,
                            'status'            => 'confirmed',
                            'audit_status'      => 'verified',
                            'verified_by'       => $adminId,
                            'notes'             => 'استرداد فرق سعر بعد تغيير البرنامج (' . number_format($overPay, 2) . ' ILS)',
                        ]);
                        $extraEmailNote = 'تم تسجيل استرداد ' . number_format($overPay, 2) . ' ILS كفرق سعر.';
                    } else {
                        // 'keep' → store as credit so it offsets future fees automatically
                        GroupStudentsFees::create([
                            'student_id'        => $fee->student_id,
                            'group_id'          => null,
                            'program_id'        => null,
                            'student_fee_paid'  => 0,
                            'transaction_amount'=> $overPay,
                            'transaction_type'  => 'credit',
                            'total_due_amount'  => 0,
                            'remaining_amount'  => 0,
                            'student_paid_type' => 'Credit Balance',
                            'status'            => 'confirmed',
                            'audit_status'      => 'verified',
                            'verified_by'       => $adminId,
                            'notes'             => 'رصيد دائن للطالب بعد تغيير البرنامج (' . number_format($overPay, 2) . ' ILS)',
                        ]);
                        $extraEmailNote = 'يوجد رصيد دائن لك بقيمة ' . number_format($overPay, 2) . ' ILS سيُحسم من رسوم لاحقة.';
                    }
                }
            }

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

                // --------- ACTIVATION ---------
                //   Placement-test path → confirm payment only, NEVER activate.
                //   All other paths → activate the account.
                // NOTE: notification emails are NOT sent here — they are dispatched by a
                // separate lightweight request (admin.financial.send_notifications) right
                // after this response returns, so confirming the payment stays instant and
                // the SMTP delay happens in the background with a live status on screen.
                $student->is_verified = 1;
                if (!$isPlacementTestFee) {
                    $student->status = 1;
                }
                $student->save();
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
            if (!empty($extraEmailNote)) {
                $msg .= ' — ' . $extraEmailNote;
            }

            return response()->json([
                'status'  => 'success',
                'message' => $msg,
                // Tell the front-end to fire the background notification request
                'notify'  => (bool) $student,
                'fee_id'  => $fee->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء المعالجة: ' . $e->getMessage()]);
        }
    }

    /**
     * Send the confirmation notification emails for an already-verified fee.
     *
     * Called by the pending-orders screen immediately AFTER verifyPayment returns, so the
     * confirm action itself is instant and the (slow) SMTP delivery happens separately with
     * a live progress indicator on the page. Returns a per-email report.
     */
    public function sendConfirmationNotifications(Request $request)
    {
        $request->validate(['id' => 'required|exists:group_students_fees,id']);

        $fee = GroupStudentsFees::find($request->id);
        $student = $fee ? Students::find($fee->student_id) : null;
        if (!$student) {
            return response()->json(['success' => false, 'reports' => []]);
        }

        $isPlacementTestFee = (bool) preg_match('/Placement\s*Test/i', (string) $fee->student_paid_type)
            || str_contains((string) $fee->student_paid_type, 'اختبار');

        // Recipient resolution — for an under-age applicant the correspondence goes to the
        // guardian's inbox and the student is CC'd when they have their own email.
        $student->loadMissing('parent');
        $age = null;
        if ($student->dob) {
            try { $age = \Carbon\Carbon::parse($student->dob)->age; } catch (\Exception $e) {}
        }
        $isChild       = ($student->program_type === 'kids') || ($age !== null && $age <= 15);
        $guardianEmail = optional($student->parent)->email;
        $toGuardian    = $isChild && !empty($guardianEmail);
        $primaryEmail  = $toGuardian ? $guardianEmail : $student->email;

        $ccEmails = [];
        if ($toGuardian && !empty($student->email) && $student->email !== $guardianEmail) {
            $ccEmails[] = $student->email;
        }

        // Absolute path of the uploaded receipt (attached to the payment email)
        $receiptAbsPath = null;
        if ($fee->payment_receipt) {
            $candidate = public_path('uploads/' . $fee->payment_receipt);
            if (is_file($candidate)) {
                $receiptAbsPath = $candidate;
            }
        }

        $sendMail = function ($mailable) use ($primaryEmail, $ccEmails) {
            if (empty($primaryEmail)) return false;
            $mailable->to($primaryEmail);
            if (!empty($ccEmails)) {
                $mailable->cc($ccEmails);
            }
            $mailable->send(app('mailer'));
            return true;
        };

        $reports = [];

        // Email 1 — payment confirmation (invoice) with the verified amount + receipt
        try {
            if ($sendMail(new \App\Mail\PaymentVerifiedMail($student, $fee, $receiptAbsPath))) {
                $reports[] = ['ok' => true, 'label' => $toGuardian ? 'إشعار الدفع + الإيصال → ولي الأمر' : 'إشعار الدفع + الإيصال'];
            } else {
                $reports[] = ['ok' => false, 'label' => 'لا يوجد بريد للطالب/ولي الأمر'];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment confirmation email failed: ' . $e->getMessage());
            $reports[] = ['ok' => false, 'label' => 'تعذّر إرسال إشعار الدفع'];
        }

        // Email 2 — account activation (non-placement only)
        if (!$isPlacementTestFee && !empty($primaryEmail)) {
            try {
                if ($sendMail(new \App\Mail\WelcomeStudentMail($student))) {
                    $reports[] = ['ok' => true, 'label' => 'إشعار تفعيل الحساب'];
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Activation email failed: ' . $e->getMessage());
                $reports[] = ['ok' => false, 'label' => 'تعذّر إرسال إشعار التفعيل'];
            }
        }

        return response()->json(['success' => true, 'reports' => $reports]);
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

            // Carry the program forward so the paid installment is tied to the SAME program
            // (needed when later seating the student into a group of that program).
            $programId = $baseFee->program_id
                ?: optional(Groups::find($baseFee->group_id))->program_id;

            $this->financialService->recordTransaction([
                'student_id'        => $baseFee->student_id,
                'group_id'          => $baseFee->group_id, // may be null — that's fine
                'program_id'        => $programId,
                'amount'            => $request->amount,
                'verified_amount'   => $request->amount,
                'audit_status'      => 'verified',
                'verified_by'       => optional(\Illuminate\Support\Facades\Auth::guard('admin')->user())->id,
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
        $query = GroupStudentsFees::with(['student', 'group.program', 'program', 'verifiedBy', 'paymentMethod'])
            ->select('group_students_fees.*', 'group_students.student_fee_total as total_invoice')
            ->join('students', 'group_students_fees.student_id', '=', 'students.id')
            ->leftJoin('group_students', function($join) {
                $join->on('group_students.student_id', '=', 'group_students_fees.student_id')
                     ->on('group_students.group_id', '=', 'group_students_fees.group_id');
            })
            ->leftJoin('groups as fg', 'fg.id', '=', 'group_students_fees.group_id')
            ->orderBy('group_students_fees.created_at', 'desc'); // newest entries first (incl. credit/refund)
            
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

        // NEW: Has-outstanding filter (remaining_amount > 0).
        // Credit and refund rows are special (no due amount) — always include them so
        // admin sees the credit/refund event in the list even when filtering for outstanding.
        if ($request->only_outstanding == 1) {
            $query->where(function ($q) {
                $q->where('group_students_fees.remaining_amount', '>', 0)
                  ->orWhereIn('group_students_fees.transaction_type', ['credit', 'refund']);
            });
        }

        // NEW: filter by specific program — match the seated group's program OR the fee row's
        // own program_id (the latter is updated when an admin swaps the student's program).
        if ($request->program_id) {
            $query->where(function ($q) use ($request) {
                $q->where('fg.program_id', $request->program_id)
                  ->orWhere('group_students_fees.program_id', $request->program_id);
            });
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
                // Request/transaction type folded under the name to save a whole column
                $paidType = $row->student_paid_type ?: '—';
                return '<div class="d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <span class="fw-bold text-dark">'.e($row->student->name).'</span>'.$pType.'
                            </div>
                            <span class="text-muted fs-8 mt-1"><i class="bi bi-tag me-1"></i>'.e($paidType).'</span>
                        </div>';
            })
            ->editColumn('program_level', function ($row) {
                // Credit & refund rows carry no program/group of their own. Keep their label
                // but ALSO resolve the student's actual enrollment so the admin sees where the
                // student was branched into.
                if (in_array($row->transaction_type, ['credit', 'refund'], true)) {
                    $badge = $row->transaction_type === 'credit'
                        ? '<span class="badge badge-light-primary fw-bold"><i class="bi bi-piggy-bank-fill me-1"></i>رصيد دائن للطالب</span>'
                        : '<span class="badge badge-light-danger fw-bold"><i class="bi bi-arrow-counterclockwise me-1"></i>استرداد رسوم</span>';

                    [$prog, $grp] = $this->resolveStudentEnrollment($row->student_id);
                    $enroll = '';
                    if ($prog || $grp) {
                        $enroll = '<div class="text-muted fs-8 mt-1"><i class="bi bi-mortarboard-fill me-1"></i>'
                                . e($prog ?: '—') . ($grp ? ' / <span class="fw-semibold">'.e($grp).'</span>' : '')
                                . '</div>';
                    }
                    return '<div class="d-flex flex-column align-items-start">'.$badge.$enroll.'</div>';
                }
                // Prefer the fee row's OWN program (it is updated when an admin swaps the
                // student's program) and fall back to the seated group's program for legacy rows.
                $program = $row->program
                    ? $row->program->title
                    : (($row->group && $row->group->program) ? $row->group->program->title : '—');
                $level = $row->group ? $row->group->name : '—';
                return $program . ' / ' . $level;
            })
            ->addColumn('date_info', function ($row) {
                // Combined "date + who confirmed" cell to reduce column crowding
                $by = $row->verifiedBy
                    ? '<span class="badge badge-light-dark fs-9 fw-bold"><i class="bi bi-person-check-fill text-success me-1"></i>'.e($row->verifiedBy->name).'</span>'
                    : '<span class="text-muted fs-9"><i class="bi bi-person-dash me-1"></i>—</span>';
                $dt = '<span class="text-muted fs-8">—</span>';
                if ($row->created_at) {
                    try {
                        $c = \Carbon\Carbon::parse($row->created_at);
                        $dt = '<span class="fw-bold text-dark fs-8"><i class="bi bi-calendar-event text-info me-1"></i>'.$c->format('Y-m-d').'</span>
                               <span class="text-muted fs-9">'.$c->format('h:i A').'</span>';
                    } catch (\Exception $e) {}
                }
                return '<div class="d-flex flex-column align-items-start gap-1">'.$dt.$by.'</div>';
            })
            ->editColumn('admin_verified_amount', function ($row) {
                // Sign + colour by transaction type
                $amount = (float) $row->transaction_amount;
                if ($row->transaction_type === 'refund') {
                    return '<span class="text-danger fw-bold fs-6">− '.number_format($amount, 2).' ILS</span>';
                }
                if ($row->transaction_type === 'credit') {
                    return '<span class="text-primary fw-bold fs-6">⊕ '.number_format($amount, 2).' ILS</span>';
                }
                return '<span class="text-success fw-bold fs-6">+ '.number_format($amount, 2).' ILS</span>';
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
            ->addColumn('tx_type_class', function ($row) {
                // Used by the front-end createdRow callback for row highlighting
                return $row->transaction_type ?: 'payment';
            })
            ->rawColumns(['student', 'program_level', 'admin_verified_amount', 'remaining_amount', 'audit_status', 'date_info', 'actions'])
            ->make(true);
    }

    /**
     * Resolve the program / group a student was actually branched into.
     * Used to enrich credit & refund ledger rows (which carry no program/group themselves).
     * Returns [programTitle|null, groupName|null].
     */
    private function resolveStudentEnrollment($studentId)
    {
        $programTitle = null;
        $groupName    = null;

        // 1) Prefer an active group seat (GroupStudents → group → program)
        $gs = \App\Models\GroupStudents::with('group.program')
            ->where('student_id', $studentId)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->first();
        if ($gs && $gs->group) {
            $groupName    = $gs->group->name;
            $programTitle = optional($gs->group->program)->title;
        }

        // 2) Fallback: the latest payment fee row that carries a program
        if (!$programTitle) {
            $feeRow = GroupStudentsFees::with(['program', 'group.program'])
                ->where('student_id', $studentId)
                ->where('transaction_type', 'payment')
                ->whereNotNull('program_id')
                ->orderByDesc('id')
                ->first();
            if ($feeRow) {
                $programTitle = $feeRow->program
                    ? $feeRow->program->title
                    : (($feeRow->group && $feeRow->group->program) ? $feeRow->group->program->title : null);
                if (!$groupName && $feeRow->group) {
                    $groupName = $feeRow->group->name;
                }
            }
        }

        return [$programTitle, $groupName];
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
