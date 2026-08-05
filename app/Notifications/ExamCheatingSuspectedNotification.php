<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/** Sent to the owning teacher (or admin, for Placement Tests) when a student's attempt exceeds the configured anti-cheat violation limit. */
class ExamCheatingSuspectedNotification extends Notification
{
    public function __construct(
        public readonly int $attemptId,
        public readonly string $examTitle,
        public readonly string $studentName,
        public readonly int $violationsCount,
        public readonly bool $autoSubmitted,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'exam_cheating_suspected',
            'title' => 'اشتباه غش في امتحان',
            'message' => 'الطالب «' . $this->studentName . '» تجاوز الحد المسموح من المخالفات (' . $this->violationsCount . ') في امتحان «' . $this->examTitle . '»'
                . ($this->autoSubmitted ? ' — تم تسليم المحاولة تلقائياً.' : '.'),
            'attempt_id' => $this->attemptId,
        ];
    }
}
