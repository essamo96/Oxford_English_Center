<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewStudentEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $student;
    public $username;
    public $password;
    public $mysettings;
    public $social;
    public $loginUrl;
    public $studentName;

    public function __construct($student, $username, $password, $mysettings = [], $social = [], $loginUrl = null)
    {
        $this->student = $student;
        $this->username = $username;
        $this->password = $password;
        $this->mysettings = $mysettings;
        $this->social = $social;
        $this->studentName = isset($student) ? ($student->name_en ?? $student->name ?? $student->name_ar ?? null) : null;
        $this->loginUrl = $loginUrl ?? url('login');
    }

    public function build()
    {
        return $this->subject('تفعيل حسابك - Oxford English Centre')
            ->view('emails.new-student')
            ->with([
                'loginUrl' => $this->loginUrl,
                'studentName' => $this->studentName,
            ]);
    }
}
