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
            // normalize mobile number to international format expected by provider
            $m = trim((string) $mobile);
            $m = preg_replace('/[^0-9+]/', '', $m);
            if (str_starts_with($m, '+')) {
                $m = ltrim($m, '+');
            }
            if (preg_match('/^0/', $m)) {
                // replace leading 0 with country code 972 (Israel) if not provided
                $m = preg_replace('/^0/', '972', $m);
            }

            $params = [
                'comm'    => 'sendsms',
                'user'    => $this->user,
                'pass'    => $this->pass,
                'to'      => $m,
                'message' => $message,
                'sender'  => $this->sender,
            ];

            // log the exact request for debugging
            try {
                $url = $this->baseUrl . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
                Log::info('TweetSMS request', ['url' => $url]);
            } catch (\Throwable $_) {
            }

            $response = Http::get($this->baseUrl, $params);

            try {
                Log::info('TweetSMS response', ['status' => $response->status(), 'body' => (string) $response->body()]);
            } catch (\Throwable $_) {
            }

            return $this->parseResponse((string) $response->body());
        } catch (\Throwable $e) {
            Log::error('TweetSMS send failed', [
                'mobile'  => $mobile,
                'error'   => $e->getMessage(),
            ]);

            return ['success' => false, 'status' => -999, 'message' => 'فشل الاتصال بمزود الخدمة (خطأ في الاتصال)'];
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
            $response = Http::get($this->baseUrl, [
                'comm' => 'chk_balance',
                'user' => $this->user,
                'pass' => $this->pass,
            ]);

            $body = trim((string) $response->body());

            // If response is just a number, it's the balance
            if (is_numeric($body) && (float) $body >= 0) {
                return ['success' => true, 'balance' => (float) $body, 'message' => 'ok'];
            }

            $parsed = $this->parseResponse($body);

            return ['success' => false, 'balance' => null, 'message' => $parsed['message']];
        } catch (\Throwable $e) {
            Log::error('TweetSMS balance check failed', ['error' => $e->getMessage()]);

            return ['success' => false, 'balance' => null, 'message' => 'فشل الاتصال بمزود الخدمة'];
        }
    }

    /**
     * Map a raw API response body to a structured result array.
     *
     * @param  string $body Raw response body from the API.
     * @return array{success: bool, status: int|string, message: string}
     */
    private function parseResponse(string $body): array
    {
        $statusRaw = trim($body);
        $parts = explode(':', $statusRaw);
        $code = trim($parts[0]);

        if ($code === '1') {
            return ['success' => true, 'status' => 1, 'message' => 'تم الإرسال بنجاح'];
        }

        $messages = [
            '-2'   => 'الرقم غير صالح أو لا يدعم هذه الدولة',
            '-100' => 'بيانات ناقصة (user, pass, to, message, sender)',
            '-110' => 'اسم المستخدم أو كلمة المرور خاطئة',
            '-113' => 'لا يوجد رصيد كافٍ',
            '-115' => 'اسم المرسل غير متاح أو غير مفعل',
            '-116' => 'اسم المرسل غير صالح',
            '-999' => 'فشل الإرسال من مزود الخدمة',
            'u'    => 'حالة الرسالة غير معروفة',
        ];

        $message = $messages[$code] ?? 'خطأ غير معروف من مزود الخدمة: ' . $code;

        Log::error('TweetSMS API error', ['status' => $code, 'reason' => $message]);

        return ['success' => false, 'status' => $code, 'message' => $message];
    }
}
