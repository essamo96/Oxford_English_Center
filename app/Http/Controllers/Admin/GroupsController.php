<?php

namespace App\Http\Controllers\Admin;


use DB;
use File;
use Carbon\Carbon;
use App\Models\Fees;
use App\Models\Times;
////////////////////////////////////
use App\Models\Groups;
use App\Models\Programs;
use App\Models\Students;
use App\Models\Teachers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\GroupStudents;
use App\Models\Absent_Student;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Teacher_Evaluate_Answer;
use App\Models\Teacher_Evaluate_Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

use App\Notifications\newAdminCreatedNotification;
use Illuminate\Contracts\Encryption\DecryptException;

class GroupsController extends AdminController
{

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
        parent::$data['active_menu'] = 'groups';
    }

    //////////////////////////////////////////////
    public function getIndex()
    {
        $opj = new Programs();
        parent::$data['programs'] = $opj->getAllPrograms();
        $teachers_opj = new Teachers();
        parent::$data['teachers'] = $teachers_opj->getAllTeachers();
        $times_opj = new Times();
        parent::$data['times'] = $times_opj->getAllTimes();
        // For the bulk-assign / promote modals
        parent::$data['active_groups_for_picker'] = Groups::with('program')
            ->where('status', 1)->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name', 'program_id']);

        // The "البرنامج" picker must list EVERY active program that has fees configured —
        // not only the ones that currently own an active group.
        parent::$data['programs_with_fees'] = Programs::where('status', 1)
            ->whereNull('deleted_at')
            ->whereIn('id', \App\Models\FeeSettings::distinct()->pluck('program_id'))
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('admin.groups.view', parent::$data);
    }

    /* ============================================================
       BULK GROUP ASSIGNMENT  +  STUDENT PROMOTION
       ============================================================ */

    /**
     * Return active students eligible for group assignment.
     *
     * Eligibility:
     *   - active (status=1, not soft-deleted)
     *   - NOT currently a member of any active group
     *     (members of CLOSED / DELETED groups remain eligible — they finished a class)
     *   - have AT LEAST ONE verified payment
     *   - have NO pending payments (everything they paid is admin-confirmed)
     *
     * Query params:
     *   search       — name / mobile / email substring
     *   program_type — 'adult' | 'kids'
     *   exclude_ids  — comma-separated student IDs to skip (already on the right pane)
     *   limit        — default 200
     */
    public function getEligibleStudents(Request $request)
    {
        $search       = trim((string) $request->get('search', ''));
        $programType  = $request->get('program_type');
        $programId    = $request->get('program_id');   // NEW
        $level        = $request->get('level');        // NEW
        $excludeIds   = array_filter(array_map('intval', explode(',', (string) $request->get('exclude_ids', ''))));
        $limit        = (int) $request->get('limit', 200);
        $limit        = max(1, min(500, $limit));
        // Multi-enroll mode: show students already seated in OTHER active groups so they can
        // be branched into an additional program. Off by default (existing behaviour intact).
        $includeEnrolled = $request->boolean('include_enrolled');
        $targetGroupId   = (int) $request->get('target_group_id', 0);

        $query = Students::query()
            ->where('students.status', 1)
            ->whereNull('students.deleted_at')

            // Group-membership filter:
            //   • default            → exclude students already in ANY active group
            //   • multi-enroll mode  → exclude only those already in the TARGET group
            ->when(!$includeEnrolled, function ($qq) {
                $qq->whereNotExists(function ($q) {
                    $q->select(\DB::raw(1))
                      ->from('group_students')
                      ->join('groups', 'groups.id', '=', 'group_students.group_id')
                      ->whereColumn('group_students.student_id', 'students.id')
                      ->whereNull('group_students.deleted_at')
                      ->whereNull('groups.deleted_at')
                      ->where('groups.status', 1);
                });
            })
            ->when($includeEnrolled && $targetGroupId, function ($qq) use ($targetGroupId) {
                $qq->whereNotExists(function ($q) use ($targetGroupId) {
                    $q->select(\DB::raw(1))
                      ->from('group_students')
                      ->whereColumn('group_students.student_id', 'students.id')
                      ->where('group_students.group_id', $targetGroupId)
                      ->whereNull('group_students.deleted_at');
                });
            })

            // Has at least one verified payment
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                  ->from('group_students_fees')
                  ->whereColumn('group_students_fees.student_id', 'students.id')
                  ->where('group_students_fees.audit_status', 'verified')
                  ->whereNull('group_students_fees.deleted_at');
            })

            // No pending payments at all
            ->whereNotExists(function ($q) {
                $q->select(\DB::raw(1))
                  ->from('group_students_fees')
                  ->whereColumn('group_students_fees.student_id', 'students.id')
                  ->where('group_students_fees.audit_status', 'pending')
                  ->whereNull('group_students_fees.deleted_at');
            })

            // NO ungraded placement test — student must be assessed first.
            // A test counts as "graded" when EITHER the score is set OR the status is 'completed'
            // (admins sometimes mark complete without filling a numeric score).
            ->whereNotExists(function ($q) {
                $q->select(\DB::raw(1))
                  ->from('placement_tests')
                  ->whereColumn('placement_tests.student_id', 'students.id')
                  ->whereNull('placement_tests.deleted_at')
                  ->whereNull('placement_tests.score')
                  ->where(function ($q2) {
                      $q2->whereNull('placement_tests.status')
                         ->orWhere('placement_tests.status', '!=', 'completed');
                  });
            })
            ->select('students.id', 'students.name', 'students.email', 'students.mobile',
                     'students.image', 'students.program_type', 'students.current_level',
                     'students.dob', 'students.gender')
            ->orderBy('students.name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('students.name', 'like', "%$search%")
                  ->orWhere('students.mobile', 'like', "%$search%")
                  ->orWhere('students.email', 'like', "%$search%");
            });
        }
        if ($programType) {
            $query->where('students.program_type', $programType);
        }

        // NEW: Restrict by a specific program (AND).
        // Eligibility for a program means: student has at least one verified fee row
        // tied to that program (or its FeeSettings), OR their previously-deleted
        // membership was in that program — we approximate via fee_settings.program_id
        // and group.program_id.
        // In multi-enroll mode this program filter is RELAXED so the rest of the students
        // (registered in other programs) also appear and can be branched into a new program.
        if ($programId && !$includeEnrolled) {
            // A student belongs to a program when they have a (non-deleted) fee row tied to it —
            // either by the fee row's OWN program_id (registration / pre-group) OR via the
            // program of the group the row is attached to. This binds the list to the program
            // (the old "any pre-group fee" rule showed everyone regardless of program).
            $query->whereExists(function ($s) use ($programId) {
                $s->select(\DB::raw(1))
                  ->from('group_students_fees as gsf')
                  ->leftJoin('groups as g', 'g.id', '=', 'gsf.group_id')
                  ->whereColumn('gsf.student_id', 'students.id')
                  ->whereNull('gsf.deleted_at')
                  ->where(function ($w) use ($programId) {
                      $w->where('gsf.program_id', $programId)
                        ->orWhere('g.program_id', $programId);
                  });
            });
        }

        // NEW: Restrict by level (AND on students.current_level)
        if ($level) {
            $query->where('students.current_level', $level);
        }

        if (!empty($excludeIds)) {
            $query->whereNotIn('students.id', $excludeIds);
        }

        $rows = $query->limit($limit)->get();

        $financialService = app(\App\Services\FinancialService::class);
        $validator        = app(\App\Services\Enrollment\EnrollmentValidator::class);

        // When the admin has chosen a target group, flag students that clash with it so the
        // UI can highlight them BEFORE the branching is submitted.
        $targetGroup = $request->get('target_group_id')
            ? \App\Models\Groups::find($request->get('target_group_id'))
            : null;

        $payload = $rows->map(function ($s) use ($financialService, $validator, $targetGroup) {
            $ledger = $financialService->getStudentLedger($s->id, null);
            $totalPaid = $ledger ? (float) $ledger['total_paid'] : 0.0;
            $totalDue  = $ledger ? (float) $ledger['total_fee']  : 0.0;

            // Outstanding across ALL of the student's ledgers (not just the pre-group one)
            $outstanding = $validator->outstandingBalance($s->id);

            $hasConflict   = false;
            $conflictGroup = null;
            if ($targetGroup) {
                $conflict = $validator->findTimeConflict($s->id, $targetGroup);
                if ($conflict) { $hasConflict = true; $conflictGroup = $conflict->name; }
            }

            return [
                'id'            => $s->id,
                'name'          => $s->name,
                'email'         => $s->email,
                'mobile'        => $s->mobile,
                'level'         => $s->current_level,
                'program_type'  => $s->program_type,
                'avatar'        => ($s->image && file_exists(public_path($s->image)))
                                   ? asset($s->image) : asset('uploads/default.jpg'),
                'total_due'     => round($totalDue, 2),
                'total_paid'    => round($totalPaid, 2),
                'remaining'     => round($outstanding, 2),
                'outstanding'   => round($outstanding, 2),
                'fully_paid'    => $outstanding <= 0.009,
                // UX flags for the target group
                'has_conflict'  => $hasConflict,
                'conflict_with' => $conflictGroup,
                // Only a time clash blocks branching now; an outstanding balance is informational
                // (a new-program seat raises a pending order instead of being refused).
                'blocked'       => $hasConflict,
            ];
        })->values();

        return response()->json(['success' => true, 'students' => $payload]);
    }

    /**
     * Bulk-assign selected students to a target group.
     * Creates a GroupStudents row (with the program's course fee as student_fee_total)
     * for each student not already a member.
     */
    public function postBulkAssign(Request $request)
    {
        $request->validate([
            'group_id'      => 'required|exists:groups,id',
            'student_ids'   => 'nullable|array',
            'student_ids.*' => 'integer|exists:students,id',
            'remove_ids'    => 'nullable|array',
            'remove_ids.*'  => 'integer|exists:students,id',
        ]);

        $addIds    = array_values(array_filter($request->student_ids ?? []));
        $removeIds = array_values(array_filter($request->remove_ids ?? []));

        if (empty($addIds) && empty($removeIds)) {
            return response()->json(['status' => 'error', 'message' => 'لم تحدد أي طالب للإضافة أو الإزالة.'], 422);
        }

        $group = Groups::find($request->group_id);
        if (!$group || $group->status != 1) {
            return response()->json(['status' => 'error', 'message' => 'لا يمكن التشعيب لمجموعة غير فعّالة.'], 422);
        }

        // ---- Program-match guard ----
        // A student may only be seated in a group that belongs to the SAME program they are
        // registered in. Reject the whole operation if any add candidate is registered in a
        // different program (e.g. an IELTS student into a non-IELTS group). We only enforce
        // when the student's program is actually known (has fee rows carrying a program_id).
        // SKIPPED in multi-enroll mode, where adding to a DIFFERENT program is intentional.
        if (!empty($addIds) && $group->program_id && !$request->boolean('include_enrolled')) {
            $names    = Students::whereIn('id', $addIds)->pluck('name', 'id');
            $mismatch = [];
            foreach ($addIds as $sid) {
                $studentProgramIds = \App\Models\GroupStudentsFees::where('student_id', $sid)
                    ->whereNotNull('program_id')
                    ->whereNull('deleted_at')
                    ->distinct()
                    ->pluck('program_id')
                    ->map(fn ($p) => (int) $p)
                    ->all();

                if (!empty($studentProgramIds) && !in_array((int) $group->program_id, $studentProgramIds, true)) {
                    $mismatch[] = $names[$sid] ?? ('#' . $sid);
                }
            }

            if (!empty($mismatch)) {
                $groupProgram = optional(\App\Models\Programs::find($group->program_id))->title ?: ('#' . $group->program_id);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'تعذّر التشعيب: الطلاب التالون مسجّلون في برنامج آخر ولا يمكن تشعيبهم في مجموعة تابعة لبرنامج «' . $groupProgram . '»: ' . implode('، ', $mismatch) . '.',
                ], 422);
            }
        }

        // ---- Business-rule pre-validation (time conflict only) ----
        // An outstanding balance NO LONGER blocks branching: a same-program seat is free and a
        // new-program seat raises a pending order (handled in EnrollmentService::enroll).
        // Duplicate memberships are handled inside the ADD loop below (counted as "skipped").
        $adminId   = optional(\Illuminate\Support\Facades\Auth::guard('admin')->user())->id;
        $validator = app(\App\Services\Enrollment\EnrollmentValidator::class);
        $blocked   = [];
        if (!empty($addIds)) {
            $vNames  = Students::whereIn('id', $addIds)->pluck('name', 'id');
            $allowed = [];
            foreach ($addIds as $sid) {
                if ($validator->isEnrolledIn($sid, $group->id)) { $allowed[] = $sid; continue; }
                if ($conflict = $validator->findTimeConflict($sid, $group)) {
                    $blocked[] = ['name' => $vNames[$sid] ?? ('#' . $sid),
                                  'reasons' => ['تعارض زمني مع مجموعة «' . $conflict->name . '»']];
                } else {
                    $allowed[] = $sid;
                }
            }
            $addIds = $allowed;
        }

        // Fee for THIS program — unified with the financial module: ALL fees except the
        // placement test (course + registration + books …). Single source of truth.
        $programCourseFee = app(\App\Services\Enrollment\EnrollmentService::class)
            ->programFee($group->program_id);

        $added = 0; $skipped = 0; $removed = 0;

        \DB::beginTransaction();
        try {
            /* ---------------- REMOVE: unlocked members being unassigned ---------------- */
            foreach ($removeIds as $sid) {
                GroupStudents::where('student_id', $sid)
                    ->where('group_id', $request->group_id)
                    ->whereNull('deleted_at')
                    ->delete();

                $this->detachStudentFees((int) $sid, (int) $request->group_id);

                $removed++;
            }

            /* ---------------- ADD: new students (unified through EnrollmentService) ---------------- */
            // enroll() does everything in one place: stores the chosen program + group, carries any
            // prior registration payment, DEDUCTS the due from the student's credit balance, and for
            // the remainder raises a PENDING order (الطلبات العالقة) at the minimum installment.
            $svc = app(\App\Services\Enrollment\EnrollmentService::class);
            foreach ($addIds as $sid) {
                if (GroupStudents::where('student_id', $sid)->where('group_id', $request->group_id)
                        ->whereNull('deleted_at')->exists()) {
                    $skipped++; continue;
                }
                try {
                    $result = $svc->enroll((int) $sid, $group, $adminId);
                    $added++;

                    // Notify the student about the new invoice (non-fatal)
                    if (!empty($result['new_program']) && !empty($result['pending_created'])) {
                        try {
                            $student = \App\Models\Students::find($sid);
                            if ($student) {
                                $programTitle = optional(\App\Models\Programs::find($group->program_id))->title ?? '';
                                $notifData = [
                                    'type'          => 'new_invoice',
                                    'title'         => 'فاتورة جديدة — تشعيب في برنامج ' . $programTitle,
                                    'message'       => 'تم تشعيبك في مجموعة «' . ($group->name ?? '') . '» ببرنامج «' . $programTitle . '». '
                                                     . 'إجمالي الرسوم: ₪ ' . number_format((float) $result['fee'], 2)
                                                     . ' — المتبقي للسداد: ₪ ' . number_format((float) $result['remaining'], 2) . '.',
                                    'total_due'     => (float) $result['fee'],
                                    'remaining'     => (float) $result['remaining'],
                                    'credit_applied'=> (float) $result['credit_applied'],
                                    'program'       => $programTitle,
                                    'group_name'    => $group->name ?? '',
                                    'group_id'      => (int) $group->id,
                                ];
                                // DB notification (queued — OK to be async)
                                $student->notify(new \App\Notifications\NewInvoiceNotification(
                                    studentId:    (int) $sid,
                                    groupId:      (int) $group->id,
                                    totalDue:     (float) $result['fee'],
                                    remaining:    (float) $result['remaining'],
                                    creditApplied:(float) $result['credit_applied'],
                                    programTitle: $programTitle,
                                    groupName:    $group->name ?? '',
                                ));
                                // Real-time broadcast — fired here (HTTP request) not inside queue job
                                try {
                                    broadcast(new \App\Events\StudentNotificationBroadcast((int) $sid, $notifData));
                                } catch (\Throwable $be) {
                                    \Illuminate\Support\Facades\Log::error('[RT] NewInvoice broadcast failed for student ' . $sid . ': ' . $be->getMessage());
                                }
                            }
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::error('[Enrollment] Notification failed for student ' . $sid . ': ' . $e->getMessage());
                        }
                    }
                } catch (\App\Exceptions\EnrollmentException $e) {
                    $blocked[] = ['name' => ($vNames[$sid] ?? ('#' . $sid)), 'reasons' => $e->errors];
                }
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'فشل التشعيب: ' . $e->getMessage()], 500);
        }

        $parts = [];
        if ($added)   $parts[] = "تشعيب {$added}";
        if ($removed) $parts[] = "إزالة {$removed}";
        if ($skipped) $parts[] = "تجاهل {$skipped} (موجودون أصلاً)";
        if (!empty($blocked)) $parts[] = 'مُنع ' . count($blocked) . ' (مخالفة شروط)';

        // Warn the admin when the target program has no chargeable fee configured (nothing was billed)
        $feeWarning = ($added > 0 && $programCourseFee <= 0)
            ? 'تنبيه: برنامج «' . optional(\App\Models\Programs::find($group->program_id))->title . '» لا توجد له رسوم مُعرّفة في إعدادات الرسوم، لذلك لم تُرصَّد رسوم على الطلاب. يرجى ضبط رسوم البرنامج.'
            : null;

        return response()->json([
            'status'      => 'success',
            'message'     => 'تم: ' . (implode(' · ', $parts) ?: 'لا تغييرات') . '.',
            'added'       => $added, 'removed' => $removed, 'skipped' => $skipped,
            // Students refused by a business rule, with the reason(s) for each — for the admin UI
            'blocked'     => $blocked,
            'fee_warning' => $feeWarning,
        ]);
    }

    /**
     * Bulk-promote students from one group to another (program/level change).
     * - Closes their membership in the source group (soft delete row).
     * - Opens membership in the target group.
     * - If any fees are outstanding on the source, they get carried into a NEW
     *   fee row attached to the target group (audit_status='pending') so the
     *   admin can verify/refund as needed.
     */
    public function postBulkPromote(Request $request)
    {
        $request->validate([
            'source_group_id' => 'required|exists:groups,id',
            'target_group_id' => 'required|different:source_group_id|exists:groups,id',
            'student_ids'     => 'required|array|min:1',
            'student_ids.*'   => 'integer|exists:students,id',
            'carry_fees'      => 'nullable|boolean',
        ]);

        $targetGroup = Groups::find($request->target_group_id);
        if (!$targetGroup || $targetGroup->status != 1) {
            return response()->json(['status' => 'error', 'message' => 'المجموعة المُستهدفة غير فعّالة.'], 422);
        }

        $sourceGroup = Groups::find($request->source_group_id);
        $adminId = optional(\Illuminate\Support\Facades\Auth::guard('admin')->user())->id;

        // Delegate to the service: only students with ZERO outstanding balance are promoted
        // (billed the new level's fee + credit auto-applied); the rest are returned as skipped.
        try {
            $result = app(\App\Services\Enrollment\EnrollmentService::class)
                ->promote($request->student_ids, $sourceGroup, $targetGroup, $adminId);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'فشل التصعيد: ' . $e->getMessage()], 500);
        }

        $promotedCount = count($result['promoted']);
        $skippedCount  = count($result['skipped']);

        $msg = "تم تصعيد {$promotedCount} طالباً إلى «" . ($targetGroup->name ?? 'المجموعة الجديدة') . '»';
        if ($skippedCount > 0) {
            $msg .= " — تعذّر تصعيد {$skippedCount} بسبب رسوم مستحقة عليهم";
        }

        return response()->json([
            'status'   => 'success',
            'message'  => $msg . '.',
            'promoted' => $result['promoted'],
            // [{student_id, name, amount}] — students NOT promoted because they still owe money
            'skipped'  => $result['skipped'],
        ]);
    }

    /**
     * Diagnose why a particular student isn't eligible for group assignment.
     * Returns a list of all rule violations + how to fix each.
     */
    public function diagnoseStudentEligibility(Request $request)
    {
        $studentId = $request->get('student_id');
        if (!$studentId) {
            // Without a specific student, return aggregate stats on what's blocking
            $stats = [
                'inactive'           => Students::where('status', '!=', 1)->whereNull('deleted_at')->count(),
                'in_active_group'    => \DB::table('students')
                    ->whereExists(function ($q) {
                        $q->select(\DB::raw(1))->from('group_students')
                          ->join('groups', 'groups.id', '=', 'group_students.group_id')
                          ->whereColumn('group_students.student_id', 'students.id')
                          ->whereNull('group_students.deleted_at')
                          ->whereNull('groups.deleted_at')
                          ->where('groups.status', 1);
                    })->whereNull('students.deleted_at')->count(),
                'has_pending_fees'   => Students::whereExists(function ($q) {
                        $q->from('group_students_fees')
                          ->whereColumn('group_students_fees.student_id', 'students.id')
                          ->where('group_students_fees.audit_status', 'pending')
                          ->whereNull('group_students_fees.deleted_at');
                    })->whereNull('deleted_at')->count(),
                'ungraded_placement' => Students::whereExists(function ($q) {
                        $q->from('placement_tests')
                          ->whereColumn('placement_tests.student_id', 'students.id')
                          ->whereNull('placement_tests.deleted_at')
                          ->whereNull('placement_tests.score')
                          ->where(function ($q2) {
                              $q2->whereNull('placement_tests.status')
                                 ->orWhere('placement_tests.status', '!=', 'completed');
                          });
                    })->whereNull('deleted_at')->count(),
                'no_verified_payment'=> Students::whereDoesntHave('gropes')
                    ->whereNotExists(function ($q) {
                        $q->from('group_students_fees')
                          ->whereColumn('group_students_fees.student_id', 'students.id')
                          ->where('group_students_fees.audit_status', 'verified')
                          ->whereNull('group_students_fees.deleted_at');
                    })->whereNull('deleted_at')->where('status', 1)->count(),
            ];
            return response()->json(['success' => true, 'stats' => $stats]);
        }

        $student = Students::find($studentId);
        if (!$student) return response()->json(['success' => false, 'message' => 'الطالب غير موجود'], 404);

        $reasons = [];

        if ($student->status != 1) {
            $reasons[] = ['key' => 'inactive', 'label' => 'الطالب غير مفعّل (status ≠ 1)', 'fix' => 'فعّله من شاشة إدارة الطلاب'];
        }

        $activeGroup = \DB::table('group_students')
            ->join('groups', 'groups.id', '=', 'group_students.group_id')
            ->where('group_students.student_id', $studentId)
            ->whereNull('group_students.deleted_at')
            ->whereNull('groups.deleted_at')
            ->where('groups.status', 1)
            ->select('groups.id', 'groups.name')
            ->first();
        if ($activeGroup) {
            $reasons[] = ['key' => 'in_active_group', 'label' => 'الطالب مشعّب حالياً في: ' . $activeGroup->name, 'fix' => 'افتح المودال على نفس المجموعة، اضغط 🔓 وأزل الطالب ثم احفظ.'];
        }

        $hasPending = \App\Models\GroupStudentsFees::where('student_id', $studentId)
            ->where('audit_status', 'pending')
            ->whereNull('deleted_at')->exists();
        if ($hasPending) {
            $reasons[] = ['key' => 'has_pending', 'label' => 'هناك دفعات قيد التدقيق', 'fix' => 'راجع شاشة "الطلبات المعلقة" وأكّد الدفعات.'];
        }

        $ungraded = \App\Models\PlacementTests::where('student_id', $studentId)
            ->whereNull('score')
            ->whereNull('deleted_at')
            ->where(function ($q) { $q->whereNull('status')->orWhere('status', '!=', 'completed'); })
            ->first();
        if ($ungraded) {
            $reasons[] = ['key' => 'ungraded_placement', 'label' => 'اختبار تحديد المستوى غير مُقيَّم بعد', 'fix' => 'اذهب لشاشة اختبارات تحديد المستوى وارصد علامة الطالب.'];
        }

        $hasVerified = \App\Models\GroupStudentsFees::where('student_id', $studentId)
            ->where('audit_status', 'verified')
            ->whereNull('deleted_at')->exists();
        if (!$hasVerified) {
            $reasons[] = ['key' => 'no_verified', 'label' => 'لا توجد أي دفعة مؤكدة', 'fix' => 'يجب تأكيد دفعة واحدة على الأقل من شاشة المالية.'];
        }

        return response()->json([
            'success'  => true,
            'student'  => ['id' => $student->id, 'name' => $student->name],
            'eligible' => empty($reasons),
            'reasons'  => $reasons,
        ]);
    }

    /**
     * AJAX: return distinct levels (group names) registered under a program.
     * Used by the bulk-assign filter dropdown.
     */
    public function getProgramLevels($programId)
    {
        $levels = collect();

        // Pull levels from FeeSettings.level_name
        $fromFees = \App\Models\FeeSettings::where('program_id', $programId)
            ->whereNotNull('level_name')->where('level_name', '!=', '')
            ->pluck('level_name')->unique()->values();

        // And from active groups (their `name` is the level/section)
        $fromGroups = Groups::where('program_id', $programId)
            ->whereNull('deleted_at')
            ->pluck('name')->unique()->values();

        $levels = $fromFees->merge($fromGroups)->unique()->sort()->values();

        return response()->json(['success' => true, 'levels' => $levels]);
    }

    /**
     * AJAX: return all groups (active + closed) a student was ever part of,
     * including the teacher and the membership status.
     */
    public function getStudentGroupsHistory($studentId)
    {
        // Pull both active and soft-deleted memberships
        $rows = \DB::table('group_students')
            ->leftJoin('groups',   'groups.id',   '=', 'group_students.group_id')
            ->leftJoin('teachers', 'teachers.id', '=', 'groups.teacher_id')
            ->leftJoin('programs', 'programs.id', '=', 'groups.program_id')
            ->where('group_students.student_id', $studentId)
            ->orderBy('group_students.id', 'desc')
            ->get([
                'group_students.id          as membership_id',
                'group_students.deleted_at  as left_at',
                'group_students.created_at  as joined_at',
                'groups.id                  as group_id',
                'groups.name                as group_name',
                'groups.status              as group_status',
                'groups.start_date',
                'groups.end_date',
                'programs.title             as program_title',
                'teachers.name              as teacher_name',
                'teachers.id                as teacher_id',
            ]);

        return response()->json(['success' => true, 'history' => $rows]);
    }

    /**
     * AJAX: return the current roster of a group (for the promote modal source pane).
     */
    public function getGroupRoster($groupId)
    {
        $rows = GroupStudents::with(['student' => function ($q) {
                $q->select('id', 'name', 'email', 'mobile', 'image', 'current_level', 'program_type');
            }])
            ->where('group_id', $groupId)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();

        $payload = $rows->filter(fn($r) => $r->student)->map(function ($r) {
            $s = $r->student;
            return [
                'id'           => $s->id,
                'name'         => $s->name,
                'email'        => $s->email,
                'mobile'       => $s->mobile,
                'level'        => $s->current_level,
                'program_type' => $s->program_type,
                'avatar'       => ($s->image && file_exists(public_path($s->image)))
                                  ? asset($s->image) : asset('uploads/default.jpg'),
                'fee_total'    => (float) $r->student_fee_total,
            ];
        })->values();

        return response()->json(['success' => true, 'students' => $payload]);
    }

    public function getIndexProgramGrops()
    {
        $opj = new Programs();
        parent::$data['programs'] = $opj->getAllPrograms();
        return view('admin.groups.program_grops_view', parent::$data);
    }
    public function getEndGropes()
    {
        $opj = new Programs();
        parent::$data['programs'] = $opj->getAllPrograms();
        return view('admin.groups.grope_end', parent::$data);
    }
    //////////////////////////////////////////////
    public function getTeacherStudents(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        $data['btn_class'] = parent::$data['btn_class'];
        parent::$data['group_id'] = $id;
        $opj = new GroupStudents();
        $ids = $opj->getGroupStudents($id);
        parent::$data['teacher_st'] = Students::whereIn('id', $ids)->where('delaying', 0)->get();
        $dataa = Groups::withoutGlobalScopes()->with(['teacher', 'branch'])->where('id', $id)->first();
        parent::$data['grope_teacher_name'] = $dataa->name ?? 'non';
        parent::$data['teacher_name'] = $dataa->teacher->name ?? 'non';
        parent::$data['group_branch'] = $dataa->branch ?? null;
        $currentDate = Carbon::now()->format('d-m-Y');
        parent::$data['Date'] = $currentDate;
        return view('admin.groups.parts.teacher_student', parent::$data);
    }
    //////////////////////////////////////////////
    function split_myString($str)
    {
        $myString = $substring = substr($str, 5);
        return  $myString;
    }
    /////////////////////////////////////////
    public function getList(Request $request)
    {
        $title = $request->get('title', NULL);
        $program_id = $request->get('program_id', NULL);
        $activeG = $request->get('activeG', NULL);
        $teacher_id = $request->get('teacher_id', NULL);
        $student_name = $request->get('student_name', NULL);
        $is_today = $request->get('is_today', NULL);
        $date_from = $request->get('date_from', NULL);
        $date_to = $request->get('date_to', NULL);
        $date_id = $request->get('date_id', NULL);
        $branch_id = app(\App\Services\BranchContext::class)->getId()
                  ?? ($request->get('branch_id') ?: null);

        $groups = new Groups();
        $info = $groups->getSearchGroups($title, $program_id, $activeG, $teacher_id, $student_name, $is_today, $date_from, $date_to, $date_id, $branch_id);
        $datatable = Datatables::of($info);

        $datatable->editColumn('name', function ($row) {
            $icon = '';
            if ($row->image) {
                $icon = '<img src="' . url($row->image) . '" style="object-fit:cover;"/>';
            } else {
                $icon = '<div class="symbol-label fs-3 bg-light-primary text-primary">
                            <i class="ki-duotone ki-people fs-3 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                         </div>';
            }

            return '<div class="d-flex align-items-center">
                        <div class="symbol symbol-40px symbol-circle me-3">
                            ' . $icon . '
                        </div>
                        <div class="d-flex flex-column text-start">
                            <a href="javascript:;" onclick="showGroupModal(' . $row->id . ')" class="text-gray-900 text-hover-primary fw-bold fs-6 lh-1 mb-1">' . $row->name . '</a>
                            <span class="text-muted fw-semibold fs-8">' . ($row->ctime ? $row->ctime->days : 'لم يحدد موعد') . '</span>
                        </div>
                    </div>';
        });

        $datatable->editColumn('teacher_name', function ($row) {
            if (!$row->teacher) return '<span class="badge badge-light-danger fs-8 fw-bold">بدون مدرس</span>';
            return '<div class="d-flex align-items-center">
                        <div class="symbol symbol-30px symbol-circle me-3" data-bs-toggle="tooltip" title="' . $row->teacher->name . '">
                            <img src="' . ($row->teacher->image ? url($row->teacher->image) : asset('assets/media/avatars/blank.png')) . '" alt="" style="object-fit:cover;"/>
                        </div>
                        <a href="javascript:;" onclick="showTeacherModal(' . $row->teacher_id . ')" class="text-gray-800 text-hover-primary fw-bold fs-7">' . $row->teacher->name . '</a>
                    </div>';
        });

        $datatable->editColumn('program_name', function ($row) {
            if (!$row->program) return '<span class="badge badge-light-info fs-8">N/A</span>';
            return '<a href="javascript:;" onclick="showProgramModal(' . $row->program_id . ')" class="text-gray-800 text-hover-primary fw-bold fs-7">' . $row->program->title . '</a>';
        });

        $datatable->editColumn('checkbox', function ($row) {
            if (!empty($row->id)) {
                $id = $row->id;
                return '<div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                            <div class="form-check form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input select-checkbox checkboxes h-20px w-20px" name="mobile[' . $id . ']" value="' . $id . '" data-id="' . $id . '"/>
                            </div>
                        </div>';
            }
            return '';
        });
        $datatable->editColumn('studens_no', function ($row) {
            $groupStudents = new GroupStudents();
            $studens_ids = $groupStudents->countStudentGroup($row->id);
            $studens_no = Students::whereIn('id', $studens_ids)->where('delaying', 0)->where('status', 1)->count();

            return '<a href="' . URL("admin/groups/teacher/students/" . Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-light-primary fw-bold" style="min-width: 90px;">
                        <i class="bi bi-people-fill fs-5 me-1"></i> ' . $studens_no . ' طالب
                    </a>';
        });

        $datatable->editColumn('code', function ($row) {
            $x = Str::random(8);
            return '<button type="button" onclick="generatekey(' . $row->id . ')" data-random="' . $x . '" class="btn btn-xs btn-light-warning fw-bold">
                        <i class="bi bi-key-fill fs-5 me-1"></i> كود
                    </button>';
        });

        $datatable->editColumn('certifcate', function ($row) {
            return '<button type="button" onclick="generateCertificateCode(' . $row->id . ')" class="btn btn-xs btn-light-success fw-bold">
                        <i class="bi bi-file-earmark-arrow-up fs-5 me-1"></i> تصدير
                    </button>';
        });


        $datatable->editColumn('time_day', function ($row) {
            if ($row->ctime && $row->ctime->days) {
                return '<span class="badge badge-light-warning fw-bold px-4 py-3">' . $row->ctime->days . '</span>';
            } else {
                return '<span class="text-danger"><i class="bi bi-exclamation-circle text-danger me-1"></i>لا يوجد</span>';
            }
        });
        $datatable->editColumn('time', function ($row) {
            if ($row->ctime && $row->ctime->times) {
                return '<span class="badge badge-light-info fw-bold px-4 py-3">' . $row->ctime->times . '</span>';
            } else {
                return '<span class="text-danger"><i class="bi bi-exclamation-circle text-danger me-1"></i>لا يوجد</span>';
            }
        });

        $datatable->addColumn('branch_name', function ($row) {
            if ($row->branch) {
                return '<span class="badge badge-light-info fw-bold px-3 py-2">'
                    . '<i class="bi bi-geo-fill me-1"></i>'
                    . e($row->branch->name_ar)
                    . '</span>';
            }
            return '<span class="badge badge-light-secondary fw-bold px-3 py-2">—</span>';
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.groups.parts.status', $data)->render();
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['teacher_id'] = $row->teacher_id;
            $data['group_name'] = $row->name ?? '';
            $data['x'] = 2;
            return view('admin.groups.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    public function getGroupDetails(Request $request) {
        $id = $request->get('id');
        $group = Groups::with(['teacher', 'program', 'ctime'])->find($id);
        if(!$group) return "Group not found";

        $groupStudents = new GroupStudents();
        $studentIds = $groupStudents->countStudentGroup($group->id);
        $studentsCount = Students::whereIn('id', $studentIds)->where('delaying', 0)->where('status', 1)->count();

        return view('admin.groups.parts.group_modal_content', compact('group', 'studentsCount'))->render();
    }

    public function getTeacherDetails(Request $request) {
        $id = $request->get('id');
        $teacher = Teachers::find($id);
        if(!$teacher) return "Teacher not found";

        $groupsCount = Groups::where('teacher_id', $id)->whereNull('deleted_at')->count();

        return view('admin.groups.parts.teacher_modal_content', compact('teacher', 'groupsCount'))->render();
    }

    public function getProgramDetails(Request $request) {
        $id = $request->get('id');
        $program = Programs::find($id);
        if(!$program) return "Program not found";

        $groupsCount = Groups::where('program_id', $id)->whereNull('deleted_at')->count();

        return view('admin.groups.parts.program_modal_content', compact('program', 'groupsCount'))->render();
    }


    /////////////////////////////////////////
    public function listEndGropes(Request $request)
    {

        $title = $request->get('title', NULL);

        $groups = new Groups();

        $info = $groups->getSearchEndGroups($title);
        $datatable = Datatables::of($info);

        $datatable->editColumn('teacher_name', function ($row) {
            if (!$row->teacher) return 'N/A';
            return '<a href="javascript:;" onclick="showTeacherModal(' . $row->teacher_id . ')" class="text-gray-800 text-hover-primary fw-bold d-flex align-items-center">
                        <div class="symbol symbol-35px symbol-circle me-3">
                            <img src="' . ($row->teacher->image ? url($row->teacher->image) : asset('assets/media/avatars/blank.png')) . '" alt="" />
                        </div>
                        ' . $row->teacher->name . '
                    </a>';
        });
        $datatable->editColumn('title', function ($row) {
            $icon = '';
            if ($row->image) {
                $icon = '<img src="' . url($row->image) . '" class="w-100" style="object-fit:cover;"/>';
            } else {
                $icon = '<div class="symbol-label fs-2 bg-light-danger text-danger">
                            <i class="ki-duotone ki-people fs-2 text-danger"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                         </div>';
            }

            return '<div class="d-flex align-items-center text-start">
                        <div class="symbol symbol-40px symbol-circle me-3">
                            ' . $icon . '
                        </div>
                        <div class="d-flex flex-column">
                            <a href="javascript:;" onclick="showGroupModal(' . $row->id . ')" class="text-gray-800 text-hover-primary fw-bold fs-6">' . $row->name . '</a>
                            <span class="text-muted fw-semibold fs-7">' . ($row->ctime ? $row->ctime->days : '') . '</span>
                        </div>
                    </div>';
        });
        $datatable->editColumn('program_name', function ($row) {
            return ($row->program ? $row->program->title : 'N/A');
        });

        $datatable->editColumn('studens_no', function ($row) {
            $groupStudents = new GroupStudents();
            $studens_no = $groupStudents->countStudentGroup($row->id);
            return '<a href="' . URL("admin/groups/teacher/students/" . Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-light-primary fw-bold" style="min-width: 90px;">
                        <i class="bi bi-people-fill fs-5 me-1"></i> ' . count($studens_no) . ' طالب
                    </a>';
        });

        $datatable->editColumn('time_day', function ($row) {
            return ($row->ctime ? $row->ctime->days . '::' . $row->ctime->times : 'N/A');
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.groups.parts.status', $data)->render();
        });

        $datatable->addColumn('actions', function ($row) {
            $data['x'] = 2;
            $data['id'] = $row->id;
            $data['teacher_id'] = $row->teacher_id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.groups.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getAdd()
    {
        $program = new Programs();
        parent::$data['programs'] = $program->getAllPrograms();
        $teacher = new Teachers();
        parent::$data['teachers'] = $teacher->getAllTeachers();
        $time = new Times();
        parent::$data['times'] = $time->getAllTimes();
        return view('admin.groups.add', parent::$data);
    }

    //////////////////////////////////////////////
    public function postAdd(Request $request)
    {
        $name = $request->get('name');
        $program_id = $request->get('program_id');
        $teacher_id = $request->get('teacher_id');
        $date_id = $request->get('date_id');
        $start_date = $request->get('start_date');
        $subjects = $request->get('subjects');
        $end_date = $request->get('end_date');
        $zoom  = $request->get('zoom');
        $drive  = $request->get('drive');
        $image  = $request->get('image') != null ? $request->get('image') : '';
        $status = (int) $request->get('status');

        $validator = Validator::make([
            'name' => $name,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ], [
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('groups.add'))->withInput();
        } else {
            $groups = new Groups();
            $add = $groups->addGroup($name, $program_id, $teacher_id, $date_id, $start_date, $end_date, $subjects, $status, $zoom, $image, $drive);
            if ($add) {
                $branchId = $request->get('branch_id') ?: auth()->guard('admin')->user()->branch_id;
                if ($branchId) {
                    $add->branch_id = $branchId;
                    $add->save();
                }
                $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route('groups.view'));
            } else {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('groups.add'))->withInput();
            }
        }
    }

    //////////////////////////////////////////////
    public function getEdit(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        //////////////////////////////////////////////
        $groups = new Groups();
        $info = $groups->getGroup($id);
        if ($info) {
            $program = new Programs();
            parent::$data['programs'] = $program->getAllPrograms();
            $teacher = new Teachers();
            parent::$data['teachers'] = $teacher->getAllTeachers();
            $time = new Times();
            parent::$data['times'] = $time->getAllTimes();
            parent::$data['info'] = $info;
            return view('admin.groups.edit', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
    }

    ////////////////////////////////////////////////
    public function postEdit(Request $request, $id)
    {
        try {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('pages.view'));
        }
        /////////////////////////////
        $groups = new Groups();
        $info = $groups->getGroup($id);
        if ($info) {
            $name = $request->get('name');
            $program_id = $request->get('program_id');
            $teacher_id = $request->get('teacher_id');
            $date_id = $request->get('date_id');
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $subjects = $request->get('subjects');
            $zoom  = $request->get('zoom');
            $drive  = $request->get('drive');
            $image  = $request->get('image');
            $status = (int) $request->get('status');
            $validator = Validator::make([
                'name' => $name,
            ], [
                'name' => 'required'
            ]);
            //////////////////////////////////////////////////////////
            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('groups.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                $update = $groups->updateGroup($info, $name, $program_id, $teacher_id, $date_id, $start_date, $end_date, $subjects, $status, $zoom, $image, $drive);
                if ($update) {
                    $branchId = $request->get('branch_id') ?: auth()->guard('admin')->user()->branch_id;
                    if ($branchId) {
                        $info->branch_id = $branchId;
                        $info->save();
                    }
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('groups.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('groups.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
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
        $groups = new Groups();
        $info = $groups->getGroup($id);
        if ($info) {
            $delete = $groups->deleteGroup($info);
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
    public function postStatus(Request $request)
    {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $groups = new Groups();
        $info = $groups->getGroup($id);
        if ($info) {
            $status = $info->status;
            if ($status == 0) {
                $update = $groups->updateStatus($id, 1);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $groups->updateStatus($id, 0);
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
    public function postStudentStatus(Request $request)
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
    //////////////////////////////////////////////ggf
    public function postStudentdelay(Request $request)
    {

        $group_id = $request->get('id');
        $message = $request->get('message');
        $id = $request->get('id_student');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        $opj = GroupStudents::where('student_id', $id)->where('group_id', $group_id)->first();
        /////////////////////////////////////
        $students = new Students();
        $update_note = $students->AddDelayCusess($id, $message);
        $info = $students->getStudent($id);
        if ($info) {
            $delaying = $info->delaying;

            if ($delaying  == 0) {
                $update = $students->updateDailling($id, 1);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {

                $update = $students->updateDailling($id, 0);
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

    public function getStudentIndex($id)
    {

        try {

            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        //////////////////////////////////////////////
        $groups = new Groups();
        $info = $groups->getGroup($id);
        if ($info) {
            parent::$data['id'] = $id;
            parent::$data['info'] = $info;
            return view('admin.groups.student.view', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
    }

    public function getStudentList(Request $request, $id)
    {

        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        $title = $request->get('title', NULL);

        $groups = new GroupStudents();
        $info = $groups->getSearchStudents($title, $id);
        $datatable = Datatables::of($info);

        $datatable->editColumn('name', function ($row) {
            return (!empty($row->name) ? $row->name : 'N/A');
        });
        $datatable->editColumn('paid', function ($row) {
            return $row->fees->where('student_paid_type', 0)->where('group_id', $row->group_id)->sum('student_fee_paid');
        });
        $datatable->editColumn('books', function ($row) {
            return $row->fees->where('student_paid_type', 1)->where('group_id', $row->group_id)->sum('student_fee_paid') . '/' . $row->student_book_total;
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->gid;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.groups.student.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getAddStudent($id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        //////////////////////////////////////////////
        $groups = new Groups();
        $info = $groups->getGroup($id);
        if ($info) {
            parent::$data['info'] = $info;
            return view('admin.groups.student.add', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
    }

    //////////////////////////////////////////////
    public function postAddStudent(Request $request, $group_id)
    {
        try {
            $group_id = Crypt::decrypt($group_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        $student_fee_paid = $request->get('student_fee_paid');
        $student_fee_total = $request->get('student_fee_total');
        $student_book_paid = $request->get('student_book_paid');
        $student_book_total = $request->get('student_book_total');
        $student_names = $request->get('student_name');
        $validator = Validator::make([
            'name' => $student_names[0],
        ], [
            'name' => 'required',
        ]);
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('groups.student.add', ['id' => Crypt::encrypt($group_id)]))->withInput();
        } else {
            foreach ($student_names as $key => $item) {
                if ($item != '') {

                    $students = new GroupStudents();
                    $exist = $students->checkStudentGroupExist($item, $group_id);
                    if (!$exist) {
                        $fee = new Fees();
                        $add = $students->addGroupStudent($item, $student_fee_total[$key], $student_book_total[$key], $group_id);
                        $add_fee = $fee->addGroupStudentFees($item, $student_fee_paid[$key], 0, $group_id); //course fees
                        $bookFee = new Fees();
                        $add_book_fee = $bookFee->addGroupStudentFees($item, $student_book_paid[$key], 1, $group_id); //books fees
                    }
                }
            }
            $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
            return redirect(route('groups.student.view', ['id' => Crypt::encrypt($group_id)]));
        }
    }

    public function getStudentDelete(Request $request)
    {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        $students = new GroupStudents();
        $info = $students->getStudent($id);
        if ($info) {
            $this->detachStudentFees($info->student_id, $info->group_id);
            $delete = $students->deleteStudent($info);
            if ($delete) {
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            } else {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }
    public function getStudentDeleted(Request $request)
    {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        $students = new GroupStudents();
        $info = $students->getStudent($id);
        if ($info) {
            $this->detachStudentFees($info->student_id, $info->group_id);
            $delete = $students->deleteStudent($info);
            if ($delete) {
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            } else {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

    /**
     * Detach a student's fee records from a group on removal:
     * - Verified/confirmed fees → set group_id=null (become program-level credit, picked up on re-enroll)
     * - Pending/unverified fees → soft-delete (no money received)
     */
    private function detachStudentFees(int $studentId, int $groupId): void
    {
        // Preserve confirmed payments — set group_id=null so adoptProgramPayment() can re-link them
        \App\Models\GroupStudentsFees::where('student_id', $studentId)
            ->where('group_id', $groupId)
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('audit_status', 'verified')
                  ->orWhere('status', 'confirmed')
                  ->orWhere('admin_verified_amount', '>', 0);
            })
            ->where(function ($q) {
                $q->where('student_paid_type', 'NOT LIKE', '%Placement Test%')
                  ->orWhereNull('student_paid_type');
            })
            ->update(['group_id' => null]);

        // Delete unconfirmed fees — no money was received
        \App\Models\GroupStudentsFees::where('student_id', $studentId)
            ->where('group_id', $groupId)
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('student_paid_type', 'NOT LIKE', '%Placement Test%')
                  ->orWhereNull('student_paid_type');
            })
            ->where('audit_status', '!=', 'verified')
            ->where(function ($q) {
                $q->where('admin_verified_amount', 0)
                  ->orWhereNull('admin_verified_amount');
            })
            ->delete();
    }
    public function getStudentAxiosDelete(Request $request, $student_id)
    {

        try {

            $student_id = Crypt::decrypt($student_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('students.view'));
        }
        $student = Students::destroy($student_id);
        if ($student) {
            $request->session()->flash('success', self::DELETE_SUCCESS);
            return redirect()->back();
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect()->back();
        }
    }


    public function postSendMessage(Request $request)
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
        $groupStudents = new GroupStudents;
        $student_ids = $groupStudents->getGroupStudentsForNotification($id);
        $students = Students::whereIn('id', $student_ids)->where('delaying', 0)->get();

        $recipients = [];
        foreach ($students as $student) {
            if ($student->email) {
                $recipients[] = [
                    'email' => $student->email,
                    'name' => $student->name
                ];
            }
        }

        if (count($recipients) > 0) {
            $campaignService = new \App\Services\EmailCampaignService();
            $campaign = $campaignService->launchCampaign([
                'subject' => $title,
                'message' => $message,
                'sender_name' => $auth,
                'recipients' => $recipients
            ]);
            
            return response()->json([
                'status' => 'success', 
                'message' => 'تم بدء الحملة البريدية بنجاح',
                'campaign_id' => $campaign->id,
                'total_recipients' => count($recipients),
                'redirect_url' => route('admin.email_campaigns.index')
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'لا يوجد طلاب لديهم بريد إلكتروني في هذه المجموعة']);
    }
    public function getAjaxStudentName(Request $request)
    {
        $student = new Students();
        $name = $request->get('term');
        $info = $student->getSearchStudentsAjax($name);
        return response()->json($info);
    }

    public function getAjaxStudentGroups(Request $request)
    {
        $group = new Groups();
        $name = $request->get('term');
        $info = $group->getSearchGroups($name);
        return response()->json($info);
    }
    public function addCodeForGrope(Request $request)
    {
        $id = $request->get('id');
        $code_scope     = $request->get('code_scope');
        $code = $request->get('code');
        $send_email = $request->get('send_email');

        if (isset($id)) {
            $group = new Groups();
            $info = $group->updateCode($id, $code, $code_scope);
            if ($info) {
                if ($send_email == 1) {
                    $groupData = Groups::find($id);
                    $groupStudents = \App\Models\GroupStudents::where('group_id', $id)
                        ->join('students', 'group_students.student_id', '=', 'students.id')
                        ->whereNotNull('students.email')
                        ->where('students.email', '!=', '')
                        ->select('students.email', 'students.name')
                        ->get();

                    $recipients = [];
                    foreach ($groupStudents as $student) {
                        if (filter_var($student->email, FILTER_VALIDATE_EMAIL)) {
                            $recipients[] = [
                                'name'  => $student->name,
                                'email' => strtolower(trim($student->email))
                            ];
                        }
                    }

                    $uniqueRecipients = collect($recipients)->unique('email')->values()->toArray();

                    if (!empty($uniqueRecipients)) {
                        $message = "<div dir='ltr'>
                        <p>Dear Student,</p>
                        <p>Here is your join code for the group <strong>{$groupData->title}</strong>.</p>
                        <p><strong>Code:</strong> {$code}</p>
                        <p><strong>Expiration Date:</strong> {$code_scope}</p>
                        <p>Please make sure to use it before it expires.</p>
                        <br>
                        <p>Best Regards,</p>
                        <p>Oxford English Centre</p>
                        </div>";

                        $campaignService = new \App\Services\EmailCampaignService();
                        $campaign = $campaignService->launchCampaign([
                            'subject'     => 'Group Join Code - ' . $groupData->title,
                            'message'     => $message,
                            'sender_name' => \Illuminate\Support\Facades\Auth::guard('admin')->user()->name ?? 'Oxford English Centre',
                            'attachment'  => null,
                            'recipients'  => $uniqueRecipients,
                        ]);
                        
                        return response()->json([
                            'status' => 'success', 
                            'message' => 'تم إنشاء الكود وبدء الإرسال للطلاب بنجاح.',
                            'campaign_id' => $campaign->id,
                            'total_recipients' => count($uniqueRecipients),
                            'redirect_url' => route('admin.email_campaigns.show', $campaign->id)
                        ], 200);
                    } else {
                        return response()->json(['status' => 'success', 'message' => 'تم الحفظ، لكن لا توجد إيميلات صالحة للطلاب بهذا الجروب لإرسال الكود.'], 200);
                    }
                }
                return response()->json(['status' => 'success', 'message' => 'نجح تم اضافة كود بنجاح'], 200);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'فشل الاضافة']);
        }
    }

    //////////////////////////////////////////////
    public function getSubjectsIndex($id)
    {
        try {

            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }

        $groups = new Groups();
        $info = $groups->getGroup($id);
        if ($info) {
            parent::$data['id'] = $id;
            parent::$data['info'] = $info;
            return view('admin.groups.subjects.add', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
    }

    public function getStudentDegree($id)
    {
        try {

            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        //////////////////////////////////////////////
        $groups = new Groups();
        $info = $groups->getGroup($id);
        if ($info) {
            parent::$data['id'] = $id;
            parent::$data['info'] = $info;
            return view('admin.groups.student.degree', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
    }
    public function getStudentEvaluation($id)
    {
        try {

            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        //////////////////////////////////////////////
        $groups = new Groups();
        $info = $groups->getGroup($id);
        if ($info) {
            parent::$data['id'] = $id;
            parent::$data['info'] = $info;
            return view('admin.groups.student.evaluation', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
    }
    public function showStudentEvaluation(Request $request, $id, $group_id, $student_id)
    {
        try {

            $group_id = Crypt::decrypt($group_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        try {

            $student_id = Crypt::decrypt($student_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }

        $info = Teacher_Evaluate_Student::where('group_id', $group_id)->where('student_id', $student_id)->where('evaluation_sort', $id)->first();
        if ($info == null) {

            $request->session()->flash('danger', 'لا يوجد تقيم حاليا بانتظار تقييم المدرس');
            return redirect(route('groups.student.evaluation', Crypt::encrypt($group_id)));
        } else {
            $questions = Teacher_Evaluate_Answer::with('questions')->where('evaluate_id', $info->id)->get();
            parent::$data['questions'] = $questions;
            parent::$data['info'] = $info;
            return view('admin.groups.student.show_evaluat', parent::$data);
        }
    }

    public function getStudentListDegree(Request $request, $id)
    {

        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        $groupStudents = new GroupStudents();
        $info = $groupStudents->getGroupStudentsDegrees($id);
        $datatable = Datatables::of($info);
        $datatable->editColumn('name', function ($row) {
            return (!empty($row->student->name) ? $row->student->name : 'N/A');
        });
        $datatable->editColumn('exam1_degree', function ($row) {
            return '<input type="text" value="' . $row->exam1_degree . '" name="exam1_degree[' . $row->student_id . ']" id="exam1_degree_' . $row->student_id . '" data_id="' . $row->student_id . '" placeholder="15" class="deg-input tes">';
        });
        $datatable->editColumn('exam2_degree', function ($row) {
            return '<input type="text" value="' . $row->exam2_degree . '" name="exam2_degree[' . $row->student_id . ']" id="exam2_degree_' . $row->student_id . '" data_id="' . $row->student_id . '" placeholder="15" class="deg-input ">';
        });
        $datatable->editColumn('exam3_degree', function ($row) {
            return '<input type="text" value="' . $row->exam3_degree . '" name="exam3_degree[' . $row->student_id . ']" id="exam3_degree' . $row->student_id . '" data_id="' . $row->student_id . '" placeholder="60" class="deg-input ">';
        });
        $datatable->editColumn('exam4_degree', function ($row) {
            return '<input type="text" value="' . $row->exam4_degree . '" name="exam4_degree[' . $row->student_id . ']" id="exam4_degree' . $row->student_id . '" data_id="' . $row->student_id . '" placeholder="60" class="deg-input ">';
        });
        $datatable->editColumn('activity_degree', function ($row) {
            return '<input type="text" value="' . $row->activity_degree . '" name="activity_degree[' . $row->student_id . ']" id="activity_degree' . $row->student_id . '" data_id="' . $row->student_id . '" placeholder="10" class="deg-input ">';
        });
        $datatable->editColumn('workbook_degree', function ($row) {
            return '<input type="text" value="' . $row->workbook_degree . '" name="workbook_degree[' . $row->student_id . ']" id="workbook_degree' . $row->student_id . '" data_id="' . $row->student_id . '" placeholder="10" class="deg-input ">';
        });
        $datatable->editColumn('total_degree', function ($row) {
            return '<label id="total_lbl_' . $row->student_id . '">' . $row->total_degree . '</label>';
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }
    public function getStudentListEvaluation(Request $request, $id)
    {

        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.view'));
        }
        $groupStudents = new GroupStudents();
        $info = $groupStudents->getGroupStudentsDegrees($id);
        $datatable = Datatables::of($info);
        $datatable->editColumn('name', function ($row) {
            return (!empty($row->student->name) ? $row->student->name : 'N/A');
        });
        $datatable->editColumn('evaluation1', function ($row) {
            $url = route('view.student.evaluation', ['id' => 1, 'group_id' => Crypt::encrypt($row->group_id), 'student_id' => Crypt::encrypt($row->student_id)]);
            return '
            <a href="' . $url . '" class="btn btn-warning btn-sm" style=" color: #fff;background-color: #ff5900; border-color: #000000;width: 60px;" " >
            <i class="bi bi-clipboard"></i> عرض</a></div>';
        });
        $datatable->editColumn('evaluation2', function ($row) {
            $url = route('view.student.evaluation', ['id' => 2, 'group_id' => Crypt::encrypt($row->group_id), 'student_id' => Crypt::encrypt($row->student_id)]);

            return '
            <a href="' . $url . '" class="btn btn-warning btn-sm" style=" color: #fff;background-color: #ff5900; border-color: #000000;width: 60px;" " >
            <i class="bi bi-clipboard"></i> عرض</a></div>';
        });
        $datatable->editColumn('evaluation3', function ($row) {
            $url = route('view.student.evaluation', ['id' => 3, 'group_id' => Crypt::encrypt($row->group_id), 'student_id' => Crypt::encrypt($row->student_id)]);
            return '
            <a href="' . $url . '" class="btn btn-warning btn-sm" style=" color: #fff;background-color: #ff5900; border-color: #000000;width: 60px;" " >
            <i class="bi bi-clipboard"></i> عرض</a></div>';
        });
        $datatable->editColumn('evaluation4', function ($row) {
            $url = route('view.student.evaluation', ['id' => 4, 'group_id' => Crypt::encrypt($row->group_id), 'student_id' => Crypt::encrypt($row->student_id)]);
            return '
            <a href="' . $url . '" class="btn btn-warning btn-sm" style=" color: #fff;background-color: #ff5900; border-color: #000000;width: 60px;" " >
            <i class="bi bi-clipboard"></i> عرض</a></div>';
        });

        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getbirthdayes()
    {
        parent::$data['active_menu'] = 'birthdayes';
        parent::$data['groups'] = Groups::whereNull('deleted_at')->orderBy('name')->get();
        return view('admin.birthdays.view', parent::$data);
    }
    //////////////////////////////////////////////
    public function getbirthdayeslist(Request $request)
    {
        $title = $request->get('title', NULL);
        $activeS = $request->get('activeS', NULL);
        $delaying = $request->get('delaying', NULL);
        $group_id = $request->get('group_id', NULL);
        
        $students = new Students();
        $info = $students->getAllStudentsHaveBirthdays($title, $activeS, $delaying, $group_id);

        $datatable = Datatables::of($info);

        $datatable->editColumn('company_name', function ($row) {
            return (!empty($row->company_name) ? $row->company_name : 'N/A');
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.students.parts.status', $data)->render();
        });
        $datatable->editColumn('checkbox', function ($row) {
            if (!empty($row->mobile)) {
                $mobile = $row->mobile;
                $email = $row->email;
                $id = $row->id;
                return '<div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                            <div class="form-check form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input select-checkbox checkboxes h-20px w-20px" name="mobile[' . $id . ']" value="' . $mobile . '" data-mob="' . $mobile . '" data-email="' . $email . '"  data-id="' . $id . '"/>
                            </div>
                        </div>';
            }
            return '';
        });
        $datatable->editColumn('dob', function ($row) {
            $dob = $row->dob;
            $dob = date('Y-m-d', strtotime($dob));
            if (isset($dob) != '') {

                return '<i class="bi bi-gift" style="color:#f01000;"> </i><strong>' . $dob . '</strong>';
            } else {
                return '<i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>';
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

        $datatable->addColumn('group_names', function ($row) {
            return $row->group_names ?: '<span class="badge badge-light-warning">بدون مجموعة</span>';
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
    public function showStudentAttendance(Request $request)
    {

        parent::$data['teacher_id'] = $request->teacher_id;
        parent::$data['group_id'] = $request->group_id;

        // parent::$data['days'] = 
        return view('admin.groups.view_attendance', parent::$data);
    }
    //////////////////////////////////////////////
    public function listStudentAttendance(Request $request, $teacher_id, $group_id)
    {

        $obj = new Absent_Student();

        $info = $obj->getAttendanceWithCount($teacher_id, $group_id);

        $datatable = Datatables::of($info);

        $datatable->editColumn('name', function ($row) {
            return (!empty($row->name) ? $row->name : 'N/A');
        });

        $datatable->editColumn('attendance_count', function ($row) {
            return '<a class="btn btn-success "><strong style="font-size:18px; color:black;">' . $row->attendance_count . ' /  22</strong></a>';
        });
        $datatable->editColumn('days', function ($row) use ($teacher_id, $group_id) {
            if (isset($row->attendance_count) && $row->attendance_count > 0) {
                $student_id = $row->id;
                $days = DB::table('absent_student')
                    ->select('days')
                    ->where('teacher_id', $teacher_id)
                    ->where('group_id', $group_id)
                    ->where('student_id', $student_id)
                    ->orderBy('created_at')
                    ->get();
                $days_html = '';

                foreach ($days as $day) {
                    $days_html .= '<span class="badge bg-primary me-2">' . $day->days . '</span>';
                }

                return $days_html;
            } else {
                return 'sssss<i class="bi bi-exclamation-circle" style="color:#f01000;"> </i><strong>لا يوجد</strong>';
            }
        });

        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }
    //////////////////////////////////////////////
    public function postStudentDegree(Request $request, $id)
    {

        $groupStudents = new GroupStudents();
        $info = $groupStudents->getAllGroupStudents($id);
        $encryptedId = encrypt($id);
        if ($info) {
            $exam1_degrees = $request->get('exam1_degree');
            $exam2_degrees = $request->get('exam2_degree');
            $exam3_degrees = $request->get('exam3_degree');
            $exam4_degrees = $request->get('exam4_degree');
            $activity_degree = $request->get('activity_degree');
            $workbook_degree = $request->get('workbook_degree');
            foreach ($info as $student) {
                $groupStudents = new GroupStudents();
                $groupStudents->updateGroupStudent($student->id, "exam1_degree", $exam1_degrees[$student->student_id]);
                $groupStudents->updateGroupStudent($student->id, "exam2_degree", $exam2_degrees[$student->student_id]);
                $groupStudents->updateGroupStudent($student->id, "exam3_degree", $exam3_degrees[$student->student_id]);
                $groupStudents->updateGroupStudent($student->id, "exam4_degree", $exam4_degrees[$student->student_id]);
                $groupStudents->updateGroupStudent($student->id, "activity_degree", $activity_degree[$student->student_id]);
                $groupStudents->updateGroupStudent($student->id, "workbook_degree", $workbook_degree[$student->student_id]);

                $total = $exam1_degrees[$student->student_id] + $exam2_degrees[$student->student_id] +
                    $exam3_degrees[$student->student_id] + $exam4_degrees[$student->student_id] + $activity_degree[$student->student_id] +
                    $workbook_degree[$student->student_id];

                $groupStudents->updateGroupStudent($student->id, "total_degree", $total);
            }

            $request->session()->flash('success', self::UPDATE_SUCCESS);
            return redirect(route('groups.student.degree', $encryptedId));
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('groups.student.degree', $encryptedId));
        }
    }

    public function show(Request $request)
    {
        $id = $request->input('id');
        $teacher_id = $request->input('teacher_id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
        try {
            $teacher_id = Crypt::decrypt($teacher_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
        $infos = Groups::findOrFail($id);
        $count_student = GroupStudents::where('group_id', $id)->whereNull('deleted_at')->get();
        $group_count = Groups::where('teacher_id', $teacher_id)->whereNull('deleted_at')->get();
        $countActiveStudent = GroupStudents::whereHas('student', function ($query) {
            $query->where('status', 1);
        })->whereNull('deleted_at')->where('group_id', $id)->count();
        $countUnActiveStudent = GroupStudents::whereHas('student', function ($query) {
            $query->where('status', 0);
        })->whereNull('deleted_at')->where('group_id', $id)->count();
        $countdelayStudent = GroupStudents::whereHas('student', function ($query) {
            $query->where('delaying', 1);
        })->whereNull('deleted_at')->where('group_id', $id)->count();
        $countmailStudent = GroupStudents::whereHas('student', function ($query) {
            $query->where('gender', 1);
        })->whereNull('deleted_at')->where('group_id', $id)->count();
        $countfemalStudent = GroupStudents::whereHas('student', function ($query) {
            $query->where('gender', 0);
        })->whereNull('deleted_at')->where('group_id', $id)->count();
        // $student = GroupStudents::where('group_id', $id)
        //     ->orderBy('total_degree', 'desc')
        //     ->first()
        //     ->student;
        if ($infos) {
            parent::$data['infos'] = $infos;
            parent::$data['group_count'] = $group_count;
            parent::$data['count_student'] = $count_student;
            parent::$data['countActiveStudent'] = $countActiveStudent;
            parent::$data['countUnActiveStudent'] = $countUnActiveStudent;
            parent::$data['countdelayStudent'] = $countdelayStudent;
            parent::$data['countmailStudent'] = $countmailStudent;
            parent::$data['countfemalStudent'] = $countfemalStudent;
            // $totalSum = Emp_Allowance::where('employee_id', $id)->sum('allowance_value');
            return view('admin.groups.parts.infos', parent::$data);
        } else {
            $request->session()->flash('error', self::EXECUTION_ERROR);
            return redirect(route($this->path . '.view'));
        }
    }


    public  function postCertificateStudent($groupId)
    {
        $groupStudents = GroupStudents::where('group_id', $groupId)->where('total_degree', '>', 75)->get();
        // dd($groupStudents);
        if (!$groupStudents->isEmpty()) {

            $group = Groups::findOrFail($groupId);
            $programInitial = strtoupper(substr($group->program->title, 0, 1));
            foreach ($groupStudents as $groupStudent) {
                $currentYear = Carbon::now()->year;
                $code = "{$programInitial}." . sprintf('%02d', $groupStudent->student_id) . ".$currentYear";


                $groupStudent->update(['cer_code' => $code]);
            }
            return response()->json(['message' => 'Codes generated and updated successfully']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'لا يوجد طلاب لديهم علامات نهائية اكبر من 75'], 400);
        }
    }

    public function sendBulkEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title'   => 'required|string|max:255',
                'message' => 'required|string',
                'emails'  => 'required' // 'emails' field actually contains group IDs
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
            }

            $groupIdsInput = $request->input('emails');
            if (is_array($groupIdsInput)) {
                $groupIds = $groupIdsInput;
            } else {
                $groupIds = array_filter(array_map('trim', explode(',', (string)$groupIdsInput)));
            }
            
            if (empty($groupIds)) {
                return response()->json(['status' => 'error', 'message' => 'يجب تحديد مجموعة واحدة على الأقل'], 400);
            }

            // Fetch active students belonging to these groups and having valid emails
            $groupStudents = \App\Models\GroupStudents::whereIn('group_id', $groupIds)
                ->join('students', 'group_students.student_id', '=', 'students.id')
                ->whereNotNull('students.email')
                ->where('students.email', '!=', '')
                ->select('students.email', 'students.name')
                ->get();

            $recipients = [];
            foreach ($groupStudents as $student) {
                if (filter_var($student->email, FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = [
                        'name'  => $student->name,
                        'email' => strtolower(trim($student->email))
                    ];
                }
            }

            // Deduplicate by email address
            $uniqueRecipients = collect($recipients)->unique('email')->values()->toArray();

            if (empty($uniqueRecipients)) {
                return response()->json(['status' => 'error', 'message' => 'لا توجد إيميلات صالحة للطلاب في المجموعات المحددة'], 400);
            }

            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('email-attachments', 'public');
            }

            $campaignService = new \App\Services\EmailCampaignService();
            $campaign = $campaignService->launchCampaign([
                'subject'     => $request->title,
                'message'     => $request->message,
                'sender_name' => \Illuminate\Support\Facades\Auth::guard('admin')->user()->name ?? 'Oxford English Centre',
                'attachment'  => $filePath,
                'recipients'  => $uniqueRecipients,
            ]);

            return response()->json([
                'status'           => 'success',
                'message'          => 'تم بدء حملة الإرسال لطلاب المجموعات بنجاح',
                'campaign_id'      => $campaign->id,
                'total_recipients' => count($uniqueRecipients),
                'redirect_url'     => route('admin.email_campaigns.show', $campaign->id)
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }
}
