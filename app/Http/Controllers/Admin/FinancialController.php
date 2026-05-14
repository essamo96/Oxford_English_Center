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
                    return '<a href="'.$url.'" target="_blank" class="btn btn-sm btn-light-info">عرض الإيصال</a>';
                }
                return '<span class="badge badge-light-danger">لا يوجد</span>';
            })
            ->addColumn('actions', function ($row) {
                $pId = $row->student ? $row->student->program_id : 0;
                return '
                    <button onclick="verifyPayment('.$row->id.', '.$row->student_fee_paid.', '.$row->total_due_amount.', '.$pId.')" class="btn btn-sm btn-success">تأكيد</button>
                    <button onclick="refundPayment('.$row->id.')" class="btn btn-sm btn-danger">رفض</button>
                ';
            })
            ->rawColumns(['receipt', 'actions'])
            ->make(true);
    }

    /**
     * Verify the payment and activate the student.
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:group_students_fees,id',
            'verified_amount' => 'required|numeric|min:0',
            'group_id' => 'nullable|exists:groups,id',
            'program_id' => 'nullable|exists:programs,id',
        ]);
    
        DB::beginTransaction();
        try {
            $fee = GroupStudentsFees::find($request->id);
            $fee->admin_verified_amount = $request->verified_amount;
            $fee->transaction_amount = $request->verified_amount;
            $fee->transaction_type = 'payment';
            $fee->remaining_amount = $fee->total_due_amount - $request->verified_amount;
            $fee->status = 'confirmed';
            $fee->audit_status = 'verified';
            if ($request->group_id) {
                $fee->group_id = $request->group_id;
            }
            $fee->save();
    
            $student = Students::find($fee->student_id);
            if ($student) {
                // 1. Assign to group if provided
                if ($request->group_id) {
                    // Check if already in group
                    $exists = \App\Models\GroupStudents::where('student_id', $student->id)
                        ->where('group_id', $request->group_id)
                        ->exists();
                    if (!$exists) {
                        // Fetch program fee for this group
                        $group = \App\Models\Groups::find($request->group_id);
                        $programFee = \App\Models\FeeSettings::where('program_id', $group->program_id)
                            ->where('type', 'course_fee')
                            ->value('amount') ?: $fee->total_due_amount;

                        \App\Models\GroupStudents::create([
                            'student_id' => $student->id,
                            'group_id' => $request->group_id,
                            'student_fee_total' => $programFee,
                            'status' => 1
                        ]);
                    }
                    
                    // Activate student only if assigned to a group
                    $student->is_verified = 1;
                    $student->status = 1; 
                } else {
                    // If no group assigned, just mark as verified but maybe keep inactive?
                    // User said: "Don't activate student directly... allow specifying group"
                    $student->is_verified = 1;
                }
                $student->save();
            }
    
            // 3. Sync placement tests if applicable
            if (str_contains($fee->student_paid_type, 'Test')) {
                $test = \App\Models\PlacementTests::where('student_id', $fee->student_id)
                    ->where('status', 'pending')
                    ->first();
                if ($test) {
                    $test->paid_amount = $request->verified_amount;
                    $test->remaining_amount = $test->total_amount - $request->verified_amount;
                    $test->status = 'payment_confirmed';
                    $test->payment_status = ($test->remaining_amount <= 0) ? 'paid' : 'partially_paid';
                    $test->save();
                }
            }
    
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'تم تأكيد الدفعة بنجاح. ' . ($request->group_id ? 'تم تفعيل الطالب وتسكينه في المجموعة.' : 'تم تأكيد الدفعة (انتظار التسكين).')]);
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
            'id' => 'required|exists:group_students_fees,id', // This is a reference to any record in the ledger for this account
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $baseFee = GroupStudentsFees::findOrFail($request->id);
            $ledger = $this->financialService->getStudentLedger($baseFee->student_id, $baseFee->group_id);

            if (!$ledger) {
                return response()->json(['status' => 'error', 'message' => 'لم يتم العثور على سجل مالي لهذا الطالب!']);
            }

            if ($request->amount > $ledger['remaining_balance']) {
                return response()->json(['status' => 'error', 'message' => 'المبلغ المدخل أكبر من المتبقي (' . $ledger['remaining_balance'] . ')!']);
            }

            $this->financialService->recordTransaction([
                'student_id' => $baseFee->student_id,
                'group_id' => $baseFee->group_id,
                'amount' => $request->amount,
                'verified_amount' => $request->amount,
                'audit_status' => 'verified',
                'type' => 'payment',
                'notes' => 'دفعة يدوية من الإدارة',
            ]);

            return response()->json(['status' => 'success', 'message' => 'تم تسجيل الدفعة في السجل المالي بنجاح.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    public function invoicesLedger()
    {
        parent::$data['active_menu'] = 'financial_invoices';
        
        $stats = $this->financialService->getGlobalStats();
        
        parent::$data['total_collected'] = $stats['total_collected'];
        parent::$data['total_remaining'] = $stats['total_remaining'];
        parent::$data['pending_amount']  = $stats['pending_amount'];

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
            });
            
        // Apply Filters
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
                $rem = $ledger ? $ledger['remaining_balance'] : 0;
                $color = $rem > 0 ? 'danger' : 'success';
                return '<span class="text-'.$color.' fw-bold">'.number_format($rem, 2) . ' ILS</span>';
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
                
                // Ledger Button (New)
                if ($row->group_id) {
                    $btns .= '<a href="'.route('admin.financial.ledger', ['studentId' => $row->student_id, 'groupId' => $row->group_id]).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" title="السجل المالي">
                                <i class="ki-duotone ki-book-open fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                              </a>';
                }

                $btns .= '<button onclick="showStudentModal('.$row->student_id.')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="عرض الملف">
                            <i class="ki-duotone ki-user fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                          </button>';
                
                if ($row->audit_status == 'verified') {
                    $btns .= '<button onclick="recordPayment('.$row->id.', 0)" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm" title="تسديد دفعة">
                                <i class="ki-duotone ki-dollar fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                              </button>';
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
            'program_id' => 'required|exists:programs,id',
            'amount'     => 'required|numeric|min:0',
            'type'       => 'required|in:' . implode(',', $typeSlugs),
            'level_name' => 'nullable|string|max:50',
        ]);

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

        return redirect()->back()->with('success', 'تم تحديث إعدادات الرسوم بنجاح.');
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
        
        $fees = $query->get();

        return response()->json([
            'success' => true,
            'fees'    => $fees
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
