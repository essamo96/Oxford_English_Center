<?php

namespace App\Http\Controllers;

use App\Events\AdminExamNotificationBroadcast;
use App\Events\StudentNotificationBroadcast;
use App\Events\TeacherNotificationBroadcast;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamReviewRequest;
use App\Models\GroupStudents;
use App\Models\Teachers;
use Illuminate\Support\Facades\Log;

/**
 * Centralizes the Examination Center's real-time notification fan-out so the same
 * database-row + broadcast pattern used elsewhere in the app (see GroupsController's
 * NewInvoiceNotification/StudentNotificationBroadcast pair) isn't duplicated across the
 * admin, teacher, and student exam controllers that all need to trigger it.
 */
class ExamNotifier
{
    // A Group Exam was published/rescheduled -> notify every student enrolled in that group.
    public static function notifyGroupExamPublished(Exam $exam): void
    {
        $studentIds = GroupStudents::where('group_id', $exam->group_id)->whereNull('deleted_at')->pluck('student_id');

        $students = \App\Models\Students::whereIn('id', $studentIds)->get();
        foreach ($students as $student) {
            $student->notify(new \App\Notifications\ExamPublishedNotification($exam->id, $exam->title, $exam->group->title ?? ''));

            try {
                broadcast(new StudentNotificationBroadcast((int) $student->id, [
                    'type' => 'exam_published',
                    'title' => 'امتحان جديد متاح',
                    'message' => 'تم نشر امتحان «' . $exam->title . '»',
                    'icon' => '📝',
                    'link' => route('student.exams.view'),
                    'created_at' => now()->diffForHumans(),
                ]));
            } catch (\Throwable $e) {
                Log::error('[RT] ExamPublished broadcast failed for student ' . $student->id . ': ' . $e->getMessage());
            }
        }
    }

    // A student submitted an attempt that needs manual grading -> notify the owning teacher
    // (group exams) or the admin bell (placement tests, which have no single owning teacher).
    public static function notifyAttemptSubmitted(ExamAttempt $attempt): void
    {
        $exam = $attempt->exam;
        $studentName = $attempt->student->name ?? '';

        if ($exam->category === 'group' && $exam->teacher_id) {
            $teacher = Teachers::find($exam->teacher_id);
            if ($teacher) {
                $teacher->notify(new \App\Notifications\ExamAttemptSubmittedNotification($attempt->id, $exam->title, $studentName));

                try {
                    broadcast(new TeacherNotificationBroadcast((int) $teacher->id, [
                        'type' => 'exam_attempt_submitted',
                        'title' => 'محاولة بانتظار التصحيح',
                        'message' => 'قام الطالب «' . $studentName . '» بتسليم امتحان «' . $exam->title . '»',
                        'icon' => '🖊️',
                        'link' => route('teacher.exam_reviews.view'),
                        'created_at' => now()->diffForHumans(),
                    ]));
                } catch (\Throwable $e) {
                    Log::error('[RT] ExamAttemptSubmitted broadcast failed for teacher ' . $teacher->id . ': ' . $e->getMessage());
                }
            }
        } else {
            try {
                broadcast(new AdminExamNotificationBroadcast([
                    'type' => 'exam_attempt_submitted',
                    'title' => 'محاولة بانتظار التصحيح',
                    'message' => 'قام الطالب «' . $studentName . '» بتسليم امتحان «' . $exam->title . '»',
                    'icon' => '🖊️',
                    'link' => route('exam_reviews.view'),
                    'created_at' => now()->diffForHumans(),
                ], $exam->branch_id));
            } catch (\Throwable $e) {
                Log::error('[RT] ExamAttemptSubmitted admin broadcast failed: ' . $e->getMessage());
            }
        }
    }

    // A review request was approved/rejected -> notify the requesting student.
    public static function notifyReviewDecision(ExamReviewRequest $review): void
    {
        $student = $review->student;
        if (!$student) {
            return;
        }

        $approved = $review->status === 'approved';
        $examTitle = $review->attempt->exam->title ?? '';

        $student->notify(new \App\Notifications\ExamReviewDecisionNotification($review->attempt_id, $examTitle, $approved, $review->teacher_comment));

        try {
            broadcast(new StudentNotificationBroadcast((int) $student->id, [
                'type' => 'exam_review_decision',
                'title' => $approved ? 'تم اعتماد طلب المراجعة' : 'تم رفض طلب المراجعة',
                'message' => 'بخصوص امتحان «' . $examTitle . '»',
                'icon' => $approved ? '✅' : '❌',
                'link' => route('student.exams.result', ['attempt' => \Illuminate\Support\Facades\Crypt::encrypt($review->attempt_id)]),
                'created_at' => now()->diffForHumans(),
            ]));
        } catch (\Throwable $e) {
            Log::error('[RT] ExamReviewDecision broadcast failed for student ' . $student->id . ': ' . $e->getMessage());
        }
    }
}
