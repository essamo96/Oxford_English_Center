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
    public ?string $receiptPath;

    public function __construct(Students $student, ?GroupStudentsFees $fee = null, ?string $receiptPath = null)
    {
        $this->student     = $student;
        $this->fee         = $fee;
        $this->receiptPath = $receiptPath;
    }

    public function build()
    {
        // Resolve the program / group the student was branched into so the invoice
        // email shows it clearly.
        $programTitle = null;
        $groupName    = null;
        if ($this->fee) {
            $programTitle = optional($this->fee->program)->title
                ?: optional(optional($this->fee->group)->program)->title;
            $groupName    = optional($this->fee->group)->name;
        }

        $mail = $this->subject('Payment Confirmed | تأكيد استلام الدفعة')
                     ->view('emails.payment_verified')
                     ->with([
                         'programTitle' => $programTitle,
                         'groupName'    => $groupName,
                     ]);

        // Attach the payment receipt the applicant uploaded (when available)
        if ($this->receiptPath && is_file($this->receiptPath)) {
            $mail->attach($this->receiptPath);
        }

        return $mail;
    }
}
