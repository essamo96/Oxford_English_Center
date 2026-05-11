<?php

namespace App\Mail;

use App\Models\PlacementTests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlacementTestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $test;
    public $subjectLine;
    public $customMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(PlacementTests $test, $subjectLine, $customMessage)
    {
        $this->test = $test;
        $this->subjectLine = $subjectLine;
        $this->customMessage = $customMessage;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view('emails.placement_test');
    }
}
