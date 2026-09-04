<?php
namespace App\Services;

use App\Models\BankMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Morilog\Jalali\Jalalian;

class BankMessageService
{
    /**
     * ذخیره‌سازی پیام بانکی از یک آرایه
     *
     * @param array $data
     * @return BankMessage
     * @throws ValidationException
     */
    public function storeFromArray(array $data): BankMessage
    {
        // ۱. اعتبارسنجی داده‌ها
        $validator = Validator::make($data, [
            'account_number' => 'required|string|max:50',
            'deposit_amount' => 'required|integer|min:1',
            'balance' => 'nullable|integer|min:0',
            'transaction_datetime' => 'nullable|date_format:Y-m-d H:i:s',
            'message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // ۲. اگر تاریخ ارسال نشده، از زمان حال استفاده کن
        if (empty($data['transaction_datetime'])) {
            $data['transaction_datetime'] = now();
        }

        // ۳. بررسی تکراری نبودن (بر اساس شماره حساب + مبلغ + تاریخ)
        $exists = BankMessage::where('account_number', $data['account_number'])
            ->where('deposit_amount', $data['deposit_amount'])
            ->where('transaction_datetime', $data['transaction_datetime'])
            ->exists();

        if ($exists) {
            Log::warning('پیام بانکی تکراری دریافت شد', $data);
            throw new \Exception('این پیام قبلاً ثبت شده است.', 409);
        }

        // ۴. ذخیره در دیتابیس
        try {
            $bankMessage = BankMessage::create([
                'account_number' => $data['account_number'],
                'deposit_amount' => $data['deposit_amount'],
                'balance' => $data['balance'] ?? 0,
                'transaction_datetime' => $data['transaction_datetime'],
                'raw_message' => $data['message'] ?? null,
                'processed' => false,
            ]);

            Log::info('پیام بانکی جدید ذخیره شد', [
                'id' => $bankMessage->id,
                'account' => $bankMessage->account_number,
                'amount' => $bankMessage->deposit_amount,
            ]);

            return $bankMessage;

        } catch (\Exception $e) {
            Log::error('خطا در ذخیره‌سازی پیام بانکی: ' . $e->getMessage(), $data);
            throw new \Exception('خطا در ذخیره‌سازی پیام: ' . $e->getMessage());
        }
    }

    /**
     * ذخیره‌سازی یک پیام خام با pars کردن خودکار
     *
     * @param string $rawMessage
     * @return BankMessage
     * @throws \Exception
     */
    public function storeFromRawMessage(string $rawMessage): BankMessage
    {
        // pars کردن پیام خام
        $parsed = $this->parseRawMessage($rawMessage);

        if (!$parsed) {
            throw new \Exception('فرمت پیام نامعتبر است');
        }

        // اضافه کردن raw_message به داده‌ها
        $parsed['raw_message'] = $rawMessage;

        return $this->storeFromArray($parsed);
    }

    /**
     * تابع کمکی برای pars کردن پیام خام
     * فرمت: "حساب9490032979 واریز10,000,000 مانده3,058,498,934 05/06/12-09:57"
     */
    public  function parseRawMessage(string $rawMessage): ?array
    {
        // استخراج شماره حساب
        preg_match('/حساب([\d]+)/', $rawMessage, $accountMatch);
        if (empty($accountMatch)) {
            return null;
        }

        // استخراج مبلغ واریزی
        preg_match('/واریز([\d,]+)/', $rawMessage, $depositMatch);
        if (empty($depositMatch)) {
            return null;
        }

        // استخراج مانده (اختیاری)
        preg_match('/مانده([\d,]+)/', $rawMessage, $balanceMatch);
        $balance = !empty($balanceMatch) ? (int) str_replace(',', '', $balanceMatch[1]) : 0;

        // استخراج تاریخ و ساعت
        preg_match('/(\d{2}\/\d{2}\/\d{2}-\d{2}:\d{2})/', $rawMessage, $dateMatch);
        $transactionDatetime = null;
        if (!empty($dateMatch)) {
            try {
                // جداسازی تاریخ و ساعت
                $parts = explode('-', $dateMatch[1]);
                $datePart = $parts[0]; // "05/06/12"
                $timePart = $parts[1]; // "09:57"

                // تقسیم تاریخ به اجزا
                $dateSegments = explode('/', $datePart);
                // انتظار: [سال, ماه, روز] یا [روز, ماه, سال]؟ با توجه به توضیح شما، سال اول است
                // پس: $year = $dateSegments[0]; $month = $dateSegments[1]; $day = $dateSegments[2];
                $year = $dateSegments[0];
                $month = $dateSegments[1];
                $day = $dateSegments[2];

                // تبدیل سال دو رقمی به چهار رقمی با فرض قرن ۱۴۰۰
                $year = 1400 + (int)$year; // اگر سال ۰۵ باشد => 1405
                // یا می‌توان از تابع تبدیل استفاده کرد: اما برای سادگی همین کافی است

                // ساخت تاریخ شمسی به صورت رشته
                $jalaliDateStr = $year . '/' . $month . '/' . $day . ' ' . $timePart . ':00';

                // تبدیل به میلادی با استفاده از Jalalian::fromFormat
                $jalali = Jalalian::fromFormat('Y/m/d H:i:s', $jalaliDateStr);
                $transactionDatetime = $jalali->toCarbon()->toDateTimeString();
            } catch (\Exception $e) {
                Log::warning("خطا در تبدیل تاریخ شمسی: {$dateMatch[1]} - " . $e->getMessage());
            }
        }

        if (!$transactionDatetime) {
            $transactionDatetime = now()->toDateTimeString();
        }

        if (!$accountNumber || !$depositAmount) {
            throw new \Exception('فرمت پیام نامعتبر است');
        }


        return [
            'account_number' => $accountMatch[1],
            'deposit_amount' => (int) str_replace(',', '', $depositMatch[1]),
            'balance' => $balance,
            'message' => $rawMessage,
            'transaction_datetime' => $transactionDatetime ?? now()->format('Y-m-d H:i:s'),
        ];
    }
}
