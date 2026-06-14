<?php

namespace App\Notifications;

use App\Models\StudentPaymentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class StudentPaymentSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly StudentPaymentSubmission $submission) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $student = $this->submission->student;
        $group   = $this->submission->group;

        return [
            'type'           => 'student_payment_submitted',
            'title'          => 'دفعة مالية جديدة بانتظار المراجعة',
            'submission_id'  => $this->submission->id,
            'student_id'     => $student?->id,
            'student_name'   => $student?->name ?? '',
            'student_number' => $student?->student_number ?? '',
            'branch'         => $student?->branch?->name_ar ?? '',
            'group_name'     => $group?->name ?? 'خارج المجموعة',
            'amount_paid'    => (float) $this->submission->amount_paid,
            'submitted_at'   => $this->submission->created_at?->toDateTimeString(),
            'url'            => route('admin.financial.student-payments'),
        ];
    }
}
