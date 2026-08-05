<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Sent to a student when a Group Exam belonging to one of their groups is published.
 * Delivered via database (notifications table); real-time broadcast is fired separately
 * from the controller using App\Events\StudentNotificationBroadcast (same pattern as
 * NewInvoiceNotification), so the HTTP request pushes it synchronously.
 */
class ExamPublishedNotification extends Notification
{
    public function __construct(
        public readonly int $examId,
        public readonly string $examTitle,
        public readonly string $groupName,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'exam_published',
            'title' => 'امتحان جديد متاح',
            'message' => 'تم نشر امتحان «' . $this->examTitle . '» لمجموعتك «' . $this->groupName . '». يمكنك الدخول إليه الآن.',
            'exam_id' => $this->examId,
        ];
    }
}
