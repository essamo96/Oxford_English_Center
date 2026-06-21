<?php

namespace App\Notifications;

use App\Models\StudentPaymentSubmission;
use Illuminate\Notifications\Notification;

// Sent synchronously (not queued) so the database row exists immediately —
// the bell badge/sidebar count/notifications inbox read straight from this
// table and must not depend on a queue worker being online.
class PaymentStatusUpdatedNotification extends Notification
{
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

        // NOTE: broadcast() is NOT called here — this method runs inside a queue job
        // and the broadcast would be lost if no queue worker is running.
        // The controller (StudentPaymentRequestsController) fires the broadcast directly.
        return [
            'type'          => 'payment_status_updated',
            'status'        => $this->status,
            'title'         => $approved ? 'تمت الموافقة على دفعتك' : 'تم رفض دفعتك',
            'message'       => $approved
                ? 'تمت الموافقة على دفعتك بمبلغ ' . number_format((float) $this->submission->amount_paid, 2) . ' بنجاح.'
                : 'تم رفض دفعتك. السبب: ' . ($this->submission->admin_notes ?? 'لم يُحدد سبب.'),
            'amount_paid'   => (float) $this->submission->amount_paid,
            'group_id'      => $this->submission->group_id,
            'submission_id' => $this->submission->id,
            'admin_notes'   => $this->submission->admin_notes,
        ];
    }
}
