<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class SendMailToStudentsHasBirthday extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $title;
    public $message;
    public $recipientEmail;
    public $attachmentPath;
    public $mysettings;
    public $social;

    public function __construct($title, $message, $recipientEmail = null, $attachmentPath = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->recipientEmail = $recipientEmail;
        $this->attachmentPath = $attachmentPath;
        
        // Fetch settings and social links from cache
        $this->mysettings = \Illuminate\Support\Facades\Cache::get('settings');
        $this->social = \Illuminate\Support\Facades\Cache::get('social');
    }

    public function build()
    {
        $mail = $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($this->title)
            ->view('emails.students.sendToStudents') // Use the same branded view for consistency
            ->with([
                'title' => $this->title,
                'mailContent' => $this->message,
                'mysettings' => $this->mysettings,
                'social' => $this->social,
                'attachment' => $this->attachmentPath ? asset('storage/' . $this->attachmentPath) : null,
            ]);


        if ($this->attachmentPath) {
            $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($this->attachmentPath);
            if (file_exists($fullPath)) {
                $mail->attach($fullPath);
            }
        }

        return $mail;
    }

}


