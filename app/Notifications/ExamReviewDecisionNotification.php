<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/** Sent to a student when their exam review request is approved or rejected. */
class ExamReviewDecisionNotification extends Notification
{
    public function __construct(
        public readonly int $attemptId,
        public readonly string $examTitle,
        public readonly bool $approved,
        public readonly ?string $comment,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'exam_review_decision',
            'title' => $this->approved ? 'تم اعتماد طلب المراجعة' : 'تم رفض طلب المراجعة',
            'message' => 'تم ' . ($this->approved ? 'اعتماد' : 'رفض') . ' طلب مراجعتك لامتحان «' . $this->examTitle . '»'
                . ($this->comment ? ' — تعليق المدرس: ' . $this->comment : ''),
            'attempt_id' => $this->attemptId,
        ];
    }
}
