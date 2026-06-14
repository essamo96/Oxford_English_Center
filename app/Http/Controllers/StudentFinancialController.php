<?php

namespace App\Http\Controllers;

use App\Events\CountersUpdated;
use App\Events\StudentPaymentSubmittedBroadcast;
use App\Models\GroupStudentsFees;
use App\Models\PaymentMethods;
use App\Models\StudentPaymentSubmission;
use App\Models\User;
use App\Notifications\StudentPaymentSubmittedNotification;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentFinancialController extends Controller
{
    public function __construct(protected FinancialService $financialService)
    {
        parent::__construct();
        $this->middleware('auth:students');
    }

    /**
     * السجل المالي - Confirmed (verified) transactions only.
     */
    public function history()
    {
        $student = Auth::guard('students')->user();

        $rows = \App\Models\GroupStudentsFees::with(['group.program', 'paymentMethod', 'verifiedBy'])
            ->where('student_id', $student->id)
            ->where('audit_status', 'verified')
            ->orderByDesc('created_at')
            ->get();

        return view('frontend.students.financial.history', parent::$data + [
            'rows'    => $rows,
            'student' => $student,
        ]);
    }

    /**
     * الفواتير المستحقة - Outstanding balances + student payment submissions.
     */
    public function invoices()
    {
        $student     = Auth::guard('students')->user();
        $allInvoices = $this->financialService->getStudentAllInvoices($student->id);

        // Map pending submissions by group_id key ('pre_group' for null)
        $pendingSubmissions = StudentPaymentSubmission::where('student_id', $student->id)
            ->whereIn('status', ['pending'])
            ->with('group')
            ->get()
            ->keyBy(fn($s) => $s->group_id ?? 'pre_group');

        $mySubmissions = StudentPaymentSubmission::where('student_id', $student->id)
            ->with('group', 'reviewedBy')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        $paymentMethods = PaymentMethods::where('is_active', 1)->get();

        return view('frontend.students.financial.invoices', parent::$data + [
            'allInvoices'        => $allInvoices,
            'pendingSubmissions' => $pendingSubmissions,
            'mySubmissions'      => $mySubmissions,
            'paymentMethods'     => $paymentMethods,
            'student'            => $student,
        ]);
    }

    /**
     * POST: Student submits a payment for a specific invoice bucket.
     */
    public function submitPayment(Request $request)
    {
        $student = Auth::guard('students')->user();

        $request->validate([
            'group_id'          => 'nullable|integer|exists:groups,id',
            'amount_paid'       => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
            'receipt'           => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes'             => 'nullable|string|max:1000',
        ]);

        $groupId         = $request->group_id ?: null;
        $paymentMethodId = (int) $request->payment_method_id;

        // Block if a pending submission already exists for this bucket
        $exists = StudentPaymentSubmission::where('student_id', $student->id)
            ->where('group_id', $groupId)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('fin_error', 'لديك طلب دفع معلق بانتظار المراجعة لهذه الفاتورة. يرجى انتظار قرار الإدارة أولاً.');
        }

        // Validate amount does not exceed remaining balance
        $ledger = $this->financialService->getStudentLedger($student->id, $groupId);
        if ($ledger && $request->amount_paid > $ledger['remaining_balance']) {
            return back()->with('fin_error', 'المبلغ المدخل (' . number_format($request->amount_paid, 2) . ') يتجاوز الرصيد المتبقي (' . number_format($ledger['remaining_balance'], 2) . ').');
        }

        // Upload receipt
        $file     = $request->file('receipt');
        $filename = time() . '_' . $student->id . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/receipts'), $filename);
        $receiptPath = 'receipts/' . $filename;

        // Find the pending GroupStudentsFees record for this student+group
        $pendingFee = $groupId
            ? GroupStudentsFees::where('student_id', $student->id)
                ->where('group_id', $groupId)
                ->where('audit_status', 'pending')
                ->orderByDesc('id')
                ->first()
            : null;

        // Scenario B: if no pending row but there IS a verified row → student is paying remaining balance.
        // We create a NEW GroupStudentsFees row instead of touching the old verified one.
        $verifiedFee = null;
        if (!$pendingFee && $groupId) {
            $verifiedFee = GroupStudentsFees::where('student_id', $student->id)
                ->where('group_id', $groupId)
                ->where('audit_status', 'verified')
                ->where('transaction_type', 'payment')
                ->orderByDesc('id')
                ->first();
        }

        $submission = DB::transaction(function () use ($student, $groupId, $request, $receiptPath, $paymentMethodId, $pendingFee, $verifiedFee) {
            $feeIdForSubmission = null;

            if ($pendingFee) {
                // Scenario A: update existing pending fee (first payment on an invoice)
                $pendingFee->update([
                    'payment_method_id' => $paymentMethodId,
                    'payment_receipt'   => $receiptPath,
                    'student_fee_paid'  => $request->amount_paid,
                ]);
                $feeIdForSubmission = $pendingFee->id;
            } elseif ($verifiedFee) {
                // Scenario B: student pays remaining balance — create a NEW fee row
                $newFee = GroupStudentsFees::create([
                    'student_id'        => $student->id,
                    'group_id'          => $groupId,
                    'audit_status'      => 'pending',
                    'transaction_type'  => 'payment',
                    'transaction_amount'=> $request->amount_paid,
                    'student_fee_paid'  => $request->amount_paid,
                    'payment_method_id' => $paymentMethodId,
                    'payment_receipt'   => $receiptPath,
                ]);
                $feeIdForSubmission = $newFee->id;
            }

            return StudentPaymentSubmission::create([
                'student_id'        => $student->id,
                'group_id'          => $groupId,
                'amount_paid'       => $request->amount_paid,
                'receipt_file'      => $receiptPath,
                'payment_method_id' => $paymentMethodId,
                'fee_id'            => $feeIdForSubmission,
                'notes'             => $request->notes,
                'status'            => 'pending',
            ]);
        });

        // Notify branch admins (database + Pusher counter update)
        $branchId = $student->branch_id;
        $admins   = $branchId
            ? User::where('branch_id', $branchId)->get()
            : User::whereNull('branch_id')->get();

        foreach ($admins as $admin) {
            $admin->notify(new StudentPaymentSubmittedNotification($submission));
        }

        try {
            broadcast(new CountersUpdated($branchId));
            broadcast(new StudentPaymentSubmittedBroadcast(
                branchId:    $branchId ?? 0,
                studentName: $student->name ?? '',
                amount:      (float) $request->amount_paid,
                link:        route('admin.financial.student-payments'),
            ));
        } catch (\Throwable) {
            // Non-fatal: Pusher may be unavailable
        }

        return back()->with('fin_success', 'تم إرسال طلب الدفع بنجاح. سيتم مراجعته من قبل الإدارة وستصلك إشعار بالنتيجة.');
    }

    /**
     * GET AJAX: Return updated balance for a single invoice bucket.
     * Called by invoices.blade.php Pusher handler after payment approval.
     */
    public function bucketStatus(Request $request)
    {
        $student = Auth::guard('students')->user();
        $groupId = $request->filled('group_id') ? (int) $request->group_id : null;
        $ledger  = $this->financialService->getStudentLedger($student->id, $groupId);

        return response()->json([
            'remaining'  => $ledger ? round((float) $ledger['remaining_balance'], 2) : 0.0,
            'total_paid' => $ledger ? round((float) $ledger['total_paid'], 2)         : 0.0,
            'label'      => '',
        ]);
    }

    /**
     * DELETE: Student cancels a pending submission.
     */
    public function cancelSubmission(int $id)
    {
        $student    = Auth::guard('students')->user();
        $submission = StudentPaymentSubmission::where('student_id', $student->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        // Remove uploaded file
        $path = public_path('uploads/' . $submission->receipt_file);
        if (file_exists($path)) {
            @unlink($path);
        }

        $submission->delete();

        return back()->with('fin_success', 'تم إلغاء طلب الدفع.');
    }

    /**
     * GET: Student financial notifications page.
     */
    public function notifications()
    {
        $student       = Auth::guard('students')->user();
        $notifications = $student->notifications()->latest()->paginate(20);
        $unreadCount   = $student->unreadNotifications->count();

        return view('frontend.students.financial.fin_notifications', parent::$data + [
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
            'student'       => $student,
        ]);
    }

    /**
     * POST AJAX: Mark single notification as read.
     */
    public function markRead(string $id)
    {
        $student      = Auth::guard('students')->user();
        $notification = $student->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['ok' => true]);
    }

    /**
     * POST: Mark all notifications as read.
     */
    public function markAllRead()
    {
        Auth::guard('students')->user()->unreadNotifications->markAsRead();

        return back()->with('fin_success', 'تم تعليم جميع الإشعارات كمقروءة.');
    }
}
