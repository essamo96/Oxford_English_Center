<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $baseUrl;
    private string $user;
    private string $pass;
    private string $sender;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.tweetsms.base_url'), '/');
        $this->user    = config('services.tweetsms.user');
        $this->pass    = config('services.tweetsms.pass');
        $this->sender  = config('services.tweetsms.sender');
    }

    /**
     * Send an SMS message to the given mobile number.
     *
     * @param  string $mobile  Recipient mobile number (international format).
     * @param  string $message Message body.
     * @return array{success: bool, status: int|string, message: string}
     */
    public function send(string $mobile, string $message): array
    {
        try {
            $response = Http::get($this->baseUrl . '/send', [
                'user'    => $this->user,
                'pass'    => $this->pass,
                'numbers' => $mobile,
                'sender'  => $this->sender,
                'msg'     => $message,
            ]);

            return $this->parseResponse((string) $response->body());
        } catch (\Throwable $e) {
            Log::error('TweetSMS send failed', [
                'mobile'  => $mobile,
                'error'   => $e->getMessage(),
            ]);

            return ['success' => false, 'status' => -999, 'message' => 'connection_error'];
        }
    }

    /**
     * Check the remaining SMS balance on the account.
     *
     * @return array{success: bool, balance: float|null, message: string}
     */
    public function checkBalance(): array
    {
        try {
            $response = Http::get($this->baseUrl . '/chk_balance', [
                'user' => $this->user,
                'pass' => $this->pass,
            ]);

            $body = trim((string) $response->body());

            if (is_numeric($body) && (float) $body >= 0) {
                return ['success' => true, 'balance' => (float) $body, 'message' => 'ok'];
            }

            $parsed = $this->parseResponse($body);

            return ['success' => false, 'balance' => null, 'message' => $parsed['message']];
        } catch (\Throwable $e) {
            Log::error('TweetSMS balance check failed', ['error' => $e->getMessage()]);

            return ['success' => false, 'balance' => null, 'message' => 'connection_error'];
        }
    }

    /**
     * Map a raw API response body to a structured result array.
     *
     * TweetSMS API status codes:
     *   Positive integer  → message ID (success)
     *   -2                → invalid sender name
     *   -100              → insufficient balance
     *   -110              → invalid mobile number
     *   -113              → message body is empty or too long
     *   -115              → duplicate message (same content sent recently)
     *   -116              → message sending limit exceeded
     *   -999              → authentication failure / invalid credentials
     *
     * @param  string $body Raw response body from the API.
     * @return array{success: bool, status: int|string, message: string}
     */
    private function parseResponse(string $body): array
    {
        $status = trim($body);

        if (is_numeric($status) && (int) $status > 0) {
            return ['success' => true, 'status' => (int) $status, 'message' => 'sent'];
        }

        $code = (int) $status;

        $messages = [
            -2   => 'invalid_sender',
            -100 => 'insufficient_balance',
            -110 => 'invalid_mobile_number',
            -113 => 'message_empty_or_too_long',
            -115 => 'duplicate_message',
            -116 => 'sending_limit_exceeded',
            -999 => 'authentication_failed',
        ];

        $message = $messages[$code] ?? 'unknown_error';

        Log::error('TweetSMS API error', ['status' => $code, 'reason' => $message]);

        return ['success' => false, 'status' => $code, 'message' => $message];
    }
}
