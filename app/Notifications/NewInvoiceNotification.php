<?php

namespace App\Notifications;

use App\Events\StudentNotificationBroadcast;
use App\Models\Groups;
use App\Models\Programs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Sent to a student when the admin enrolls them in a new program/group.
 * Delivered via database (notifications table) + real-time Pusher broadcast.
 */
class NewInvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        $data = [
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

        // Real-time push to student's Pusher channel
        try {
            broadcast(new StudentNotificationBroadcast($this->studentId, $data));
        } catch (\Throwable) {
            // Non-fatal — Pusher may be unavailable
        }

        return $data;
    }
}
