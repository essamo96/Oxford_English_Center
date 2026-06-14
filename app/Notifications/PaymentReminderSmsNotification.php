<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PaymentReminderSmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $studentName,
        public readonly float  $amountDue,
    ) {}

    public function via($notifiable): array
    {
        return ['tweetsms'];
    }

    /**
     * Build the SMS message body for TweetSMS.
     *
     * @param  mixed $notifiable
     * @return string
     */
    public function toTweetSms(mixed $notifiable): string
    {
        return "عزيزي {$this->studentName}، يرجى تسديد المبلغ المستحق {$this->amountDue} ₪ في أقرب وقت. Oxford English Centre.";
    }
}
