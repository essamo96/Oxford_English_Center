<?php

namespace App\Mail;

use App\Models\Students;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeStudentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $student;

    /**
     * Create a new message instance.
     */
    public function __construct(Students $student)
    {
        $this->student = $student;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Welcome to Oxford English Centre | أهلاً بك في أكسفورد')
                    ->view('emails.welcome_student');
    }
}
