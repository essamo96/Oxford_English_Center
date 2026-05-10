<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use App\Models\Settings;
use App\Models\Socials;

/**
 * Mailable for sending campaign emails.
 * Note: ShouldQueue is NOT implemented here because this is sent synchronously 
 * within the SendCampaignEmailJob to allow proper failure tracking.
 */
class SendMailToStudents extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $messageBody;
    public $targetEmail;
    public $attachmentPath;
    public $recipientName;

    /**
     * Create a new message instance.
     */
    public function __construct($subjectLine, $messageBody, $targetEmail, $attachmentPath = null, $recipientName = null)
    {
        $this->subjectLine    = (string)$subjectLine;
        $this->messageBody    = (string)$messageBody;
        $this->targetEmail    = (string)$targetEmail;
        $this->attachmentPath = $attachmentPath;
        $this->recipientName  = $recipientName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Fetch settings dynamically during build
        $mysettings = Cache::remember('mysettings_mail', 3600, function() {
            return Settings::find(1) ?? (object)['name' => 'Oxford English Centre'];
        });

        $social = Cache::remember('socials_mail', 3600, function() {
            return (new Socials())->getAllSocialActive();
        });

        $fromAddress = config('mail.from.address') ?: 'info@oxford.com';
        $fromName    = config('mail.from.name') ?: 'Oxford English Centre';

        // Handle Logo URL for email (using public URL instead of embed() which is not available in Mailable)
        $logoCid = null;
        if (isset($mysettings->logo) && $mysettings->logo && file_exists(public_path($mysettings->logo))) {
             $logoCid = asset($mysettings->logo);
        } elseif (file_exists(public_path('OXFORD-LOGO.jpg'))) {
             $logoCid = asset('OXFORD-LOGO.jpg');
        } else {
             $logoCid = 'https://ui-avatars.com/api/?name=Oxford&background=002147&color=fff&size=120&font-size=0.33';
        }

        $mail = $this->from($fromAddress, $fromName)
            ->subject($this->subjectLine)
            ->view('emails.students.sendToStudents')
            ->with([
                'title'       => $this->subjectLine,
                'mailContent' => $this->messageBody,
                'email'       => $this->targetEmail,
                'name'        => $this->recipientName,
                'mysettings'  => $mysettings,
                'social'      => $social,
                'logoCid'     => $logoCid,
                'attachment'  => $this->attachmentPath ? asset('storage/' . $this->attachmentPath) : null,
            ]);

        // Attach file if it exists in the public disk
        if ($this->attachmentPath) {
            $fullPath = storage_path('app/public/' . $this->attachmentPath);
            if (file_exists($fullPath)) {
                $mail->attach($fullPath);
            }
        }

        return $mail;
    }
}
