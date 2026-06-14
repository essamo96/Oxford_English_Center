<?php

namespace App\Notifications;

use App\Events\StudentNotificationBroadcast;
use App\Models\StudentPaymentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PaymentStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly StudentPaymentSubmission $submission,
        public readonly string $status
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $approved = $this->status === 'approved';

        $data = [
            'type'          => 'payment_status_updated',
            'status'        => $this->status,
            'title'         => $approved ? 'تمت الموافقة على دفعتك' : 'تم رفض دفعتك',
            'message'       => $approved
                ? 'تمت الموافقة على دفعتك بمبلغ ' . number_format((float) $this->submission->amount_paid, 2) . ' بنجاح.'
                : 'تم رفض دفعتك. السبب: ' . ($this->submission->admin_notes ?? 'لم يُحدد سبب.'),
            'amount_paid'   => (float) $this->submission->amount_paid,
            'submission_id' => $this->submission->id,
            'admin_notes'   => $this->submission->admin_notes,
        ];

        // Real-time push to student channel
        broadcast(new StudentNotificationBroadcast($this->submission->student_id, $data));

        return $data;
    }
}
