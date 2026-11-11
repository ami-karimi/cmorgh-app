<?php

namespace App\Services;

use App\Models\BankMessage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Morilog\Jalali\Jalalian;

class BankMessageService
{
    public function storeFromArray(array $data): BankMessage
    {
        $data = Validator::make($data, [
            'account_number' => 'required|string|max:50',
            'deposit_amount' => 'required|integer|min:1',
            'balance' => 'nullable|integer|min:0',
            'transaction_datetime' => 'required|date_format:Y-m-d H:i:s',
            'message' => 'nullable|string|max:10000',
        ])->validate();
        $key = hash('sha256', implode('|', [$data['account_number'], $data['deposit_amount'], $data['transaction_datetime']]));
        // Include old rows without a fingerprint, without rewriting historical bank data.
        $exists = BankMessage::where('account_number', $data['account_number'])
            ->where('deposit_amount', $data['deposit_amount'])
            ->where('transaction_datetime', $data['transaction_datetime'])->exists();
        if ($exists) {
            throw new \RuntimeException('این پیام قبلاً ثبت شده است.', 409);
        }
        $message = BankMessage::firstOrCreate(['deduplication_key' => $key], [
            'account_number' => $data['account_number'],
            'deposit_amount' => $data['deposit_amount'],
            'balance' => $data['balance'] ?? 0,
            'transaction_datetime' => $data['transaction_datetime'],
            'raw_message' => $data['message'] ?? null,
            'processed' => false,
        ]);
        if (!$message->wasRecentlyCreated) {
            throw new \RuntimeException('این پیام قبلاً ثبت شده است.', 409);
        }
        return $message;
    }

    public function storeFromRawMessage(string $rawMessage): BankMessage
    {
        $parsed = $this->parseRawMessage($rawMessage);
        if (!$parsed) {
            throw ValidationException::withMessages(['message' => 'فرمت پیام واریز یا تاریخ تراکنش معتبر نیست.']);
        }
        return $this->storeFromArray($parsed);
    }

    public function parseRawMessage(string $rawMessage): ?array
    {
        $text = strtr($rawMessage, array_combine(
            preg_split('//u', '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩', -1, PREG_SPLIT_NO_EMPTY),
            str_split('01234567890123456789')
        ));
        $text = str_replace(['٬', '،', "\u{200c}"], [',', ',', ' '], $text);
        if (!preg_match('/حساب\s*[:：]?\s*([0-9]+(?:[.-][0-9]+)*)/u', $text, $account)
            || !preg_match('/واریز\s*[:：]?\s*([0-9][0-9,]*)\s*(تومان|ریال)?/u', $text, $deposit)
            || !preg_match('/(?<![0-9])([0-9]{4}|[0-9]{2})\/([0-9]{1,2})\/([0-9]{1,2})\s*[- ]\s*([0-9]{2}):([0-9]{2})(?::([0-9]{2}))?/', $text, $date)) {
            return null;
        }
        preg_match('/مانده\s*[:：]?\s*([0-9][0-9,]*)\s*(تومان|ریال)?/u', $text, $balance);
        $year = (int) $date[1];
        if ($year < 100) { $year += 1400; }
        $month = (int) $date[2]; $day = (int) $date[3];
        $hour = (int) $date[4]; $minute = (int) $date[5]; $second = (int) ($date[6] ?? 0);
        if ($year < 1300 || $year > 1499 || $month < 1 || $month > 12 || $day < 1 || $day > 31
            || ($month > 6 && $day > 30) || $hour > 23 || $minute > 59 || $second > 59) { return null; }
        try {
            $jalali = new Jalalian($year, $month, $day, $hour, $minute, $second);
            $carbon = $jalali->toCarbon();
            // Reject invalid dates instead of silently turning an old/malformed SMS into today's deposit.
            if (Jalalian::fromCarbon($carbon)->format('Y/m/d') !== sprintf('%04d/%02d/%02d', $year, $month, $day)) { return null; }
            $amount = (int) str_replace(',', '', $deposit[1]);
            $remaining = isset($balance[1]) ? (int) str_replace(',', '', $balance[1]) : 0;
            // Bank SMS defaults to rial; explicitly labelled toman must be converted once.
            if (($deposit[2] ?? '') === 'تومان') { $amount *= 10; }
            if (($balance[2] ?? '') === 'تومان') { $remaining *= 10; }
            return [
                'account_number' => $account[1], 'deposit_amount' => $amount, 'balance' => $remaining,
                'message' => $rawMessage, 'transaction_datetime' => $carbon->toDateTimeString(),
            ];
        } catch (\Throwable) { return null; }
    }
}
