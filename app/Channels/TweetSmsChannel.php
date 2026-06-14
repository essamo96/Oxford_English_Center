<?php

namespace App\Channels;

use App\Services\SmsService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TweetSmsChannel
{
    public function __construct(private readonly SmsService $sms) {}

    /**
     * Send the notification via TweetSMS.
     *
     * The notifiable model must define a `routeNotificationForTweetSms()` method
     * that returns the recipient's mobile number, OR expose a `phone` attribute.
     *
     * The notification class must implement a `toTweetSms($notifiable)` method
     * that returns the message string to be sent.
     *
     * @param  mixed        $notifiable
     * @param  Notification $notification
     * @return array{success: bool, status: int|string, message: string}|null
     */
    public function send(mixed $notifiable, Notification $notification): ?array
    {
        $mobile = $this->resolveRecipient($notifiable);

        if (! $mobile) {
            Log::error('TweetSmsChannel: no mobile number found on notifiable', [
                'notifiable' => get_class($notifiable),
                'id'         => $notifiable->getKey(),
            ]);
            return null;
        }

        if (! method_exists($notification, 'toTweetSms')) {
            Log::error('TweetSmsChannel: notification does not implement toTweetSms()', [
                'notification' => get_class($notification),
            ]);
            return null;
        }

        $message = $notification->toTweetSms($notifiable);

        if (empty($message)) {
            return null;
        }

        return $this->sms->send($mobile, $message);
    }

    /**
     * Resolve the recipient's mobile number from the notifiable model.
     *
     * Priority:
     *   1. `routeNotificationForTweetSms()` method
     *   2. `phone` attribute
     *   3. `mobile` attribute
     */
    private function resolveRecipient(mixed $notifiable): ?string
    {
        if (method_exists($notifiable, 'routeNotificationForTweetSms')) {
            return $notifiable->routeNotificationForTweetSms() ?: null;
        }

        return $notifiable->phone ?? $notifiable->mobile ?? null;
    }
}
