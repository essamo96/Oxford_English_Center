<?php

namespace App\Mail;

use App\Models\Students;
use App\Models\GroupStudentsFees;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentVerifiedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Students $student;
    public ?GroupStudentsFees $fee;

    public function __construct(Students $student, ?GroupStudentsFees $fee = null)
    {
        $this->student = $student;
        $this->fee     = $fee;
    }

    public function build()
    {
        return $this->subject('Payment Confirmed | تأكيد استلام الدفعة')
                    ->view('emails.payment_verified');
    }
}
