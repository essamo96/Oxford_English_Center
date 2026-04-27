<?php

namespace App\Notifications;

use App\Models\Students;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class newAdminCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $obj;
    protected $message;
    protected $title;
    protected $sender_name;
    protected $attachmentPath;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($obj,$message,$title,$auth, $attachmentPath = null)
    {
        $this->obj = $obj;
        $this->message = $message;
        $this->title = $title;
        $this->sender_name = $auth;
        $this->attachmentPath = $attachmentPath;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail' ,'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mysettings = \Illuminate\Support\Facades\Cache::get('settings');
        $social = \Illuminate\Support\Facades\Cache::get('social');
        $attachmentUrl = $this->attachmentPath ? asset('storage/' . $this->attachmentPath) : null;

        $mail = (new \Illuminate\Notifications\Messages\MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($this->title)
            ->view('emails.students.sendToStudents', [
                'title' => $this->title,
                'mailContent' => $this->message,
                'notifiable' => $notifiable,
                'sender_name' => $this->sender_name,
                'mysettings' => $mysettings,
                'social' => $social,
                'attachment' => $attachmentUrl,
            ]);

        if ($this->attachmentPath) {
            $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($this->attachmentPath);
            if (file_exists($fullPath)) {
                $mail->attach($fullPath);
            }
        }

        return $mail;
    }


    public function toDatabase($notifiable)
    {
        return [
            'title'=> $this->title,
            'body'=> $this->message,
            'action'=> url('/'),
            'icon'=> '',
            'sender_name' => $this->sender_name,
            'attachment' => $this->attachmentPath ? asset('storage/' . $this->attachmentPath) : null,
        ];
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
