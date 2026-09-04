<?php
// app/Jobs/ProcessBankMessages.php

namespace App\Jobs;

use App\Models\BankMessage;
use App\Models\TopupRequest;
use App\Models\Financial;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessBankMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * تعداد دفعات تلاش مجدد در صورت خطا
     */
    public $tries = 3;

    /**
     * اجرای Job
     */
    public function handle(): void
    {
        // دریافت ۵۰ پیام پردازش‌نشده (برای جلوگیری از سنگین شدن)
        $messages = BankMessage::where('processed', false)
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get();

        if ($messages->isEmpty()) {
            Log::info('هیچ پیام بانکی جدیدی برای پردازش وجود ندارد.');
            return;
        }

        $processedCount = 0;
        $matchedCount = 0;

        foreach ($messages as $message) {
            $result = $this->processMessage($message);

            if ($result) {
                $matchedCount++;
            }

            $processedCount++;
        }

        Log::info("Job پردازش پیام‌های بانکی انجام شد", [
            'تعداد کل پردازش‌شده' => $processedCount,
            'تعداد تطابق‌یافته' => $matchedCount,
        ]);
    }

    /**
     * پردازش یک پیام بانکی
     *
     * @param BankMessage $message
     * @return bool آیا تطابق پیدا شد؟
     */
    private function processMessage(BankMessage $message): bool
    {
        try {
            return DB::transaction(function () use ($message) {
                // قفل کردن پیام برای جلوگیری از پردازش همزمان
                $message = BankMessage::where('id', $message->id)
                    ->where('processed', false)
                    ->lockForUpdate()
                    ->first();

                if (!$message) {
                    return false;
                }

                // پیدا کردن درخواست شارژ با مبلغ قابل پرداخت برابر
                $request = TopupRequest::where('payable_amount', $message->deposit_amount)
                    ->where('status', 'pending')
                    ->where('expires_at', '>', now())
                    ->lockForUpdate()
                    ->first();

                // اگر درخواستی یافت نشد
                if (!$request) {
                    $message->update([
                        'processed' => true,
                        'processed_at' => now(),
                    ]);

                    Log::info("هیچ درخواستی با مبلغ {$message->deposit_amount} یافت نشد.", [
                        'message_id' => $message->id,
                    ]);

                    return false;
                }

                // ========== تطابق پیدا شد ==========

                // ۱. ثبت تراکنش مالی در جدول financial
                $financial = Financial::create([
                    'creator'     => $request->user_id,
                    'for'         => $request->user_id,
                    'price'       => $request->requested_amount,
                    'type'        => 'plus',
                    'approved'    => 1,
                    'description' => 'شارژ خودکار - درخواست #' . $request->id . ' - تراکنش بانکی #' . $message->id,
                    'attachment'  => null,
                ]);

                // ۲. به‌روزرسانی درخواست
                $request->update([
                    'status' => 'completed',
                ]);


                // ۴. علامت‌گذاری پیام به‌عنوان پردازش‌شده
                $message->update([
                    'processed' => true,
                    'processed_at' => now(),
                ]);

                Log::info("✅ درخواست شارژ تأیید شد", [
                    'request_id' => $request->id,
                    'user_id' => $request->user_id,
                    'requested_amount' => $request->requested_amount,
                    'payable_amount' => $request->payable_amount,
                    'message_id' => $message->id,
                ]);

                return true;
            });

        } catch (\Exception $e) {
            Log::error("خطا در پردازش پیام بانکی #{$message->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }
}
