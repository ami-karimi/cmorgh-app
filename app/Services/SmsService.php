<?php
namespace App\Services;

use App\Models\SmsLogs;
use Illuminate\Support\Facades\RateLimiter;
use Ippanel\Client;

class SmsService
{
    protected $client;
    protected $senderNumber;

    public function __construct()
    {
        $apiKey = env('IPPANEL_API_KEY');
        $this->senderNumber = env('IPPANEL_SENDER');

        if ($apiKey) {
            $this->client = new Client($apiKey);
        }
    }


    public function sendPattern($phone, $patternCode, array $patternValues)
    {
        $rateLimitKey = 'sms-request:' . $phone;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            $secondsLeft = RateLimiter::availableIn($rateLimitKey);

            return [
                'success' => false,
                'message' => "لطفاً $secondsLeft ثانیه دیگر برای دریافت مجدد پیامک صبر کنید."
            ];
        }



        try {
            $messageId = $this->client->sendPattern(
                $patternCode,
                $this->senderNumber,
                $phone,
                $patternValues
            );

            RateLimiter::hit($rateLimitKey, 120);

            $this->logSms($phone, $patternCode, $patternValues, 'success', $messageId->getMessageCode());

            return [
                'success' => true,
                'message' => 'پیامک با موفقیت ارسال شد.',
                'message_id' => $messageId
            ];

        } catch (\Exception $e) {
            $this->logSms($phone, $patternCode, $patternValues, 'failed', null, $e->getMessage());

            return [
                'success' => false,
                'message' => 'متأسفانه در ارسال پیامک خطایی رخ داد.',
                'error' => $e->getMessage()
            ];
        }
    }


    private function logSms($phone, $patternCode, $parameters, $status, $messageId = null, $errorMessage = null)
    {
        SmsLogs::create([
            'phone' => $phone,
            'pattern_code' => $patternCode,
            'parameters' => $parameters,
            'status' => $status,
            'message_id' => $messageId,
            'error_message' => $errorMessage,
        ]);
    }
}
