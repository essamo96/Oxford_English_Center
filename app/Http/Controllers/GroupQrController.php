<?php

namespace App\Http\Controllers;

use App\Models\GroupQrToken;
use App\Models\Groups;
use App\Models\GroupStudents;
use App\Models\Students;
use App\Models\FeeSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GroupQrController extends Controller
{
    /**
     * Generate a new QR token for a group.
     * Inputs: group_id, expires_at (datetime), max_uses (nullable), created_by_type (admin|teacher)
     */
    public function generate(Request $request)
    {
        $request->validate([
            'group_id'        => 'required|exists:groups,id',
            'expires_at'      => 'required|date|after:now',
            'max_uses'        => 'nullable|integer|min:1',
            'created_by_type' => 'nullable|in:admin,teacher',
        ]);

        $group = Groups::find($request->group_id);
        if (!$group || $group->status != 1) {
            return response()->json(['status' => 'error', 'message' => 'لا يمكن إنشاء QR لمجموعة غير فعّالة.'], 422);
        }

        $createdByType = $request->created_by_type ?: 'admin';
        $createdById   = Auth::id();

        $token = Str::random(48);

        $qr = GroupQrToken::create([
            'group_id'        => $group->id,
            'token'           => $token,
            'expires_at'      => $request->expires_at,
            'max_uses'        => $request->max_uses,
            'created_by_type' => $createdByType,
            'created_by_id'   => $createdById,
            'is_active'       => true,
        ]);

        // URL that, when opened by a logged-in student, triggers self-enrollment
        $scanUrl = url('/qr/join/' . $token);

        return response()->json([
            'status'   => 'success',
            'token'    => $token,
            'qr_url'   => $scanUrl,
            // High error-correction (ecc=H, up to 30% damage tolerance) lets us
            // overlay a centered logo without breaking the scan.
            'qr_image' => 'https://api.qrserver.com/v1/create-qr-code/?size=480x480&margin=14&ecc=H&data=' . urlencode($scanUrl),
            // Logo to composite in the center (drawn client-side on canvas)
            'logo_url' => asset('assets/oxford/img/logo.png'),
            'expires'  => Carbon::parse($qr->expires_at)->format('Y-m-d H:i'),
            'group'    => [
                'id'      => $group->id,
                'name'    => $group->name,
                'program' => optional($group->program)->title,
            ],
        ]);
    }

    /**
     * Handle a QR scan — the URL the QR points to opens this endpoint in the
     * student's browser. If they're authenticated as a student, they get
     * enrolled in the group; otherwise we show a friendly error page.
     */
    public function join($token)
    {
        $qr = GroupQrToken::where('token', $token)->first();

        if (!$qr) {
            return view('frontend.qr.result', [
                'status'  => 'error',
                'title'   => 'رمز غير صحيح',
                'message' => 'هذا الـ QR Code غير موجود أو تم حذفه.',
            ]);
        }

        $invalidReason = $qr->reasonInvalid();
        if ($invalidReason) {
            return view('frontend.qr.result', [
                'status'  => 'error',
                'title'   => 'رمز غير صالح',
                'message' => $invalidReason,
            ]);
        }

        // Must be logged in AS A STUDENT — silently redirect to login with return URL.
        // After successful login the student lands back on this exact endpoint and gets enrolled.
        $student = Auth::guard('student')->user() ?? Auth::user();
        if (!$student || !($student instanceof Students)) {
            $returnUrl = url('/qr/join/' . $token);
            // Remember intended URL for the auth pipeline
            session(['url.intended' => $returnUrl, 'qr_redirect' => $returnUrl]);
            return redirect()->to('/login?redirect=' . urlencode($returnUrl) . '&qr=1');
        }

        // Already enrolled?
        $alreadyIn = GroupStudents::where('student_id', $student->id)
            ->where('group_id', $qr->group_id)
            ->whereNull('deleted_at')->exists();

        if ($alreadyIn) {
            return view('frontend.qr.result', [
                'status'  => 'info',
                'title'   => 'أنت مشعّب أصلاً',
                'message' => 'أنت بالفعل عضو في هذه المجموعة، لا حاجة لمسح الكود مجدداً.',
            ]);
        }

        // Compute the program course fee for membership total
        $group  = Groups::find($qr->group_id);
        $programCourseFee = (float) FeeSettings::where('program_id', $group->program_id)
            ->whereIn('type', ['course', 'course_fee'])
            ->sum('amount');

        GroupStudents::create([
            'student_id'         => $student->id,
            'group_id'           => $qr->group_id,
            'student_fee_total'  => $programCourseFee > 0 ? $programCourseFee : 0,
            'student_book_total' => 0,
            'status'             => 1,
        ]);

        $qr->increment('uses_count');

        return view('frontend.qr.result', [
            'status'  => 'success',
            'title'   => 'تم التشعيب بنجاح',
            'message' => 'تم انضمامك إلى مجموعة "' . ($group->name ?? '—') . '" بنجاح. سيتم إبلاغك بالخطوات التالية قريباً.',
        ]);
    }

    /**
     * Deactivate a QR token early.
     */
    public function deactivate(Request $request, $id)
    {
        $qr = GroupQrToken::find($id);
        if (!$qr) return response()->json(['status' => 'error', 'message' => 'الرمز غير موجود'], 404);
        $qr->is_active = false;
        $qr->save();
        return response()->json(['status' => 'success', 'message' => 'تم تعطيل الرمز.']);
    }
}
