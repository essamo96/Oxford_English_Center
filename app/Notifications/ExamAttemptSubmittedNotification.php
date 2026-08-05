<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/** Sent to the owning teacher (or admin, for Placement Tests) when a student submits an attempt that needs manual grading. */
class ExamAttemptSubmittedNotification extends Notification
{
    public function __construct(
        public readonly int $attemptId,
        public readonly string $examTitle,
        public readonly string $studentName,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'exam_attempt_submitted',
            'title' => 'محاولة بانتظار التصحيح',
            'message' => 'قام الطالب «' . $this->studentName . '» بتسليم امتحان «' . $this->examTitle . '» وينتظر التصحيح اليدوي.',
            'attempt_id' => $this->attemptId,
        ];
    }
}
