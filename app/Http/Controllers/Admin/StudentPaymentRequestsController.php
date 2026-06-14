<?php

namespace App\Http\Controllers\Admin;

use App\Events\CountersUpdated;
use App\Http\Controllers\Admin\AdminController;
use App\Models\StudentPaymentSubmission;
use App\Notifications\PaymentStatusUpdatedNotification;
use App\Services\BranchContext;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class StudentPaymentRequestsController extends AdminController
{
    public function __construct(protected FinancialService $financialService)
    {
        parent::__construct();
        parent::$data['active_menu'] = 'student_payments';
    }

    /**
     * Admin list page.
     */
    public function index()
    {
        return view('admin.financial.payment_requests', parent::$data);
    }

    /**
     * DataTable JSON endpoint.
     */
    public function getList(Request $request)
    {
        $query = StudentPaymentSubmission::with(['student.branch', 'group.program', 'reviewedBy'])
            ->orderByDesc('created_at');

        // Branch isolation
        $branchId = app(BranchContext::class)->getId()
                 ?? ($request->get('branch_id') ?: null);
        if ($branchId) {
            $query->whereHas('student', fn($q) => $q->where('branch_id', $branchId));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending'); // default: show pending only
        }

        return DataTables::of($query)
            ->addColumn('student_info', function ($row) {
                $s    = $row->student;
                $name = $s ? e($s->name) : 'N/A';
                $num  = $s ? e($s->student_number ?? '') : '';
                return '<div class="d-flex flex-column gap-1">
                            <span class="fw-bold text-dark"><i class="bi bi-person-fill text-primary me-1"></i>'.$name.'</span>
                            <span class="text-muted fs-8">'.$num.'</span>
                        </div>';
            })
            ->addColumn('branch_col', function ($row) {
                $branch = $row->student?->branch;
                if ($branch) {
                    $colors = ['info','primary','success','warning'];
                    $c      = $colors[$branch->id % count($colors)];
                    return '<span class="badge badge-light-'.$c.' fw-bold"><i class="bi bi-geo-fill me-1"></i>'.e($branch->name_ar).'</span>';
                }
                return '<span class="badge badge-light-secondary">—</span>';
            })
            ->addColumn('group_col', function ($row) {
                if ($row->group) {
                    $prog = $row->group->program?->title ?? '';
                    return '<div class="d-flex flex-column gap-1">
                                <span class="badge badge-light-primary fs-8">'.e($prog).'</span>
                                <span class="badge badge-light-info fs-9">'.e($row->group->name).'</span>
                            </div>';
                }
                return '<span class="text-muted fst-italic fs-8">خارج المجموعة</span>';
            })
            ->addColumn('amount_col', function ($row) {
                return '<span class="fw-bold text-success fs-6">₪ '.number_format((float)$row->amount_paid, 2).'</span>';
            })
            ->addColumn('receipt_col', function ($row) {
                $url = asset('uploads/' . $row->receipt_file);
                $ext = strtolower(pathinfo($row->receipt_file, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                    return '<span class="receipt-thumb" data-receipt-url="'.$url.'" data-receipt-type="image" style="cursor:pointer;">
                                <img src="'.$url.'" style="width:56px;height:56px;object-fit:cover;border-radius:8px;border:1px solid #eee;" alt="receipt">
                            </span>';
                }
                return '<a href="'.$url.'" target="_blank" class="btn btn-sm btn-light-info"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>';
            })
            ->addColumn('status_col', function ($row) {
                $map = ['pending'=>['warning','بانتظار المراجعة'],'approved'=>['success','مقبول'],'rejected'=>['danger','مرفوض']];
                [$color, $label] = $map[$row->status] ?? ['secondary', $row->status];
                return '<span class="badge badge-light-'.$color.' fw-bold">'.$label.'</span>';
            })
            ->addColumn('date_col', function ($row) {
                return '<span class="text-muted fs-8">'.optional($row->created_at)->format('Y-m-d H:i').'</span>';
            })
            ->addColumn('actions', function ($row) {
                if ($row->status !== 'pending') {
                    return '<span class="text-muted fs-8">تمت المعالجة</span>';
                }
                return '<button class="btn btn-sm btn-success btn-approve me-1" data-id="'.$row->id.'" data-amount="'.$row->amount_paid.'">
                            <i class="bi bi-check-lg me-1"></i>قبول
                        </button>
                        <button class="btn btn-sm btn-danger btn-reject" data-id="'.$row->id.'">
                            <i class="bi bi-x-lg me-1"></i>رفض
                        </button>';
            })
            ->rawColumns(['student_info','branch_col','group_col','amount_col','receipt_col','status_col','date_col','actions'])
            ->make(true);
    }

    /**
     * Approve a student payment submission.
     */
    public function approve(Request $request, int $id)
    {
        $request->validate(['admin_notes' => 'nullable|string|max:500']);

        $submission = StudentPaymentSubmission::with('student')->findOrFail($id);

        abort_if($submission->status !== 'pending', 422, 'هذا الطلب تمت معالجته مسبقاً.');

        // Copy the receipt to approved/ folder so it's never removed by cancelSubmission.
        $approvedReceipt = $submission->receipt_file;
        if ($submission->receipt_file) {
            $src = public_path('uploads/' . $submission->receipt_file);
            $dir = public_path('uploads/receipts/approved');
            if (!is_dir($dir)) { mkdir($dir, 0755, true); }
            $dest = $dir . '/' . basename($submission->receipt_file);
            if (file_exists($src) && !file_exists($dest)) { @copy($src, $dest); }
            $approvedReceipt = 'receipts/approved/' . basename($submission->receipt_file);
        }

        DB::transaction(function () use ($submission, $request, $approvedReceipt) {
            // Mark submission as approved
            $submission->update([
                'status'      => 'approved',
                'admin_notes' => $request->admin_notes,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // If submission is linked to an existing pending fee record, update it directly
            // instead of creating a duplicate verified record.
            if ($submission->fee_id) {
                \App\Models\GroupStudentsFees::where('id', $submission->fee_id)->update([
                    'audit_status'         => 'verified',
                    'student_fee_paid'     => $submission->amount_paid,
                    'admin_verified_amount'=> $submission->amount_paid,
                    'transaction_amount'   => $submission->amount_paid,
                    'transaction_type'     => 'payment',
                    'student_paid_type'    => 'payment',
                    'payment_method_id'    => $submission->payment_method_id,
                    'payment_receipt'      => $approvedReceipt,
                    'verified_by'          => Auth::id(),
                    'updated_at'           => now(),
                ]);
            } else {
                // No linked fee record — create a new verified transaction (legacy fallback)
                $this->financialService->recordTransaction([
                    'student_id'      => $submission->student_id,
                    'group_id'        => $submission->group_id,
                    'program_id'      => $submission->program_id,
                    'type'            => 'payment',
                    'amount'          => $submission->amount_paid,
                    'verified_amount' => $submission->amount_paid,
                    'audit_status'    => 'verified',
                    'verified_by'     => Auth::id(),
                    'notes'           => $submission->notes ?? 'دفعة مُقدَّمة من الطالب عبر البوابة الإلكترونية',
                    'receipt'         => $approvedReceipt,
                    'paid_type'       => 'payment',
                ]);
            }
        });

        // Notify student (database — queued, async)
        $submission->student->notify(new PaymentStatusUpdatedNotification($submission, 'approved'));

        // Real-time broadcast to student channel — fired synchronously here (NOT inside the
        // queued notification) so the student gets the Pusher event immediately.
        try {
            broadcast(new \App\Events\StudentNotificationBroadcast(
                $submission->student_id,
                [
                    'type'          => 'payment_status_updated',
                    'status'        => 'approved',
                    'title'         => 'تمت الموافقة على دفعتك',
                    'message'       => 'تمت الموافقة على دفعتك بمبلغ ' . number_format((float) $submission->amount_paid, 2) . ' بنجاح.',
                    'amount_paid'   => (float) $submission->amount_paid,
                    'group_id'      => $submission->group_id,
                    'submission_id' => $submission->id,
                    'admin_notes'   => $submission->admin_notes,
                ]
            ));
        } catch (\Throwable) {}

        // Update admin sidebar counters
        try {
            broadcast(new CountersUpdated($submission->student?->branch_id));
        } catch (\Throwable) {}

        return response()->json(['ok' => true, 'message' => 'تم قبول الدفعة وتسجيلها بنجاح.']);
    }

    /**
     * Reject a student payment submission.
     */
    public function reject(Request $request, int $id)
    {
        $request->validate(['admin_notes' => 'required|string|max:500']);

        $submission = StudentPaymentSubmission::with('student')->findOrFail($id);

        abort_if($submission->status !== 'pending', 422, 'هذا الطلب تمت معالجته مسبقاً.');

        $submission->update([
            'status'      => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Notify student (database — queued, async)
        $submission->student->notify(new PaymentStatusUpdatedNotification($submission, 'rejected'));

        // Real-time broadcast to student channel — synchronous
        try {
            broadcast(new \App\Events\StudentNotificationBroadcast(
                $submission->student_id,
                [
                    'type'          => 'payment_status_updated',
                    'status'        => 'rejected',
                    'title'         => 'تم رفض دفعتك',
                    'message'       => 'تم رفض دفعتك. السبب: ' . ($submission->admin_notes ?? 'لم يُحدد سبب.'),
                    'amount_paid'   => (float) $submission->amount_paid,
                    'group_id'      => $submission->group_id,
                    'submission_id' => $submission->id,
                    'admin_notes'   => $submission->admin_notes,
                ]
            ));
        } catch (\Throwable) {}

        try {
            broadcast(new CountersUpdated($submission->student?->branch_id));
        } catch (\Throwable) {}

        return response()->json(['ok' => true, 'message' => 'تم رفض الدفعة وإشعار الطالب.']);
    }
}
