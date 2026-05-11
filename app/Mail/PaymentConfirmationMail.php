<?php

namespace App\Mail;

use App\Models\PlacementTests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $test;
    public $student;

    /**
     * Create a new message instance.
     */
    public function __construct(PlacementTests $test)
    {
        $this->test = $test;
        $this->student = $test->student;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Payment Confirmed - Placement Test | تم تأكيد دفع رسوم اختبار المستوى')
                    ->view('emails.payment_confirmation');
    }
}
