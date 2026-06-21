<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Sent to a student when the admin enrolls them in a new program/group.
 * Delivered via database (notifications table) + real-time Pusher broadcast.
 * Sent synchronously (not queued) — the bell badge/sidebar count/notifications
 * inbox read straight from this table and must not depend on a queue worker.
 */
class NewInvoiceNotification extends Notification
{
    public function __construct(
        public readonly int   $studentId,
        public readonly int   $groupId,
        public readonly float $totalDue,
        public readonly float $remaining,
        public readonly float $creditApplied,
        public readonly string $programTitle,
        public readonly string $groupName,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'          => 'new_invoice',
            'title'         => 'فاتورة جديدة — تشعيب في برنامج ' . $this->programTitle,
            'message'       => 'تم تشعيبك في مجموعة «' . $this->groupName . '» ببرنامج «' . $this->programTitle . '». '
                             . 'إجمالي الرسوم: ₪ ' . number_format($this->totalDue, 2)
                             . ' — المتبقي للسداد: ₪ ' . number_format($this->remaining, 2) . '.',
            'total_due'     => $this->totalDue,
            'remaining'     => $this->remaining,
            'credit_applied'=> $this->creditApplied,
            'program'       => $this->programTitle,
            'group_name'    => $this->groupName,
            'group_id'      => $this->groupId,
        ];
        // Broadcast is fired from GroupsController directly (not here) so it fires
        // synchronously in the HTTP request rather than inside the queued job.
    }
}
