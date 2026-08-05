<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/** Sent to the owning teacher (or admin, for Placement Tests) when a student requests a review of a graded attempt. */
class ExamReviewRequestedNotification extends Notification
{
    public function __construct(
        public readonly int $attemptId,
        public readonly string $examTitle,
        public readonly string $studentName,
        public readonly string $message,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'exam_review_requested',
            'title' => 'طلب مراجعة جديد',
            'message' => 'طلب الطالب «' . $this->studentName . '» مراجعة نتيجته في امتحان «' . $this->examTitle . '»: ' . $this->message,
            'attempt_id' => $this->attemptId,
        ];
    }
}
