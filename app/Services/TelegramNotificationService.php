<?php

namespace App\Services;

use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\Financial;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Configuration;
use SergiX44\Nutgram\Telegram\Types\InputFile;

class TelegramNotificationService
{
    protected Nutgram $bot;

    public function __construct()
    {
        $config = config('nutgram');

        // دریافت api_url از کانفیگ
        $apiUrl = $config['config']['api_url'] ?? Configuration::DEFAULT_API_URL;

        // ساخت شیء Configuration با apiUrl و تنظیمات دیگر
        $configuration = new Configuration(
            apiUrl: $apiUrl,
        // در صورت نیاز تنظیمات دیگر (اختیاری)
        // testEnv: env('APP_ENV') === 'testing',
        // isLocal: env('APP_ENV') === 'local',
        // clientTimeout: 30,
        );

        // در صورت نیاز، safe_mode را از طریق property `testEnv` یا `isLocal` مدیریت کنید
        // اما safe_mode مربوط به وب‌هوک است و در اینجا ضروری نیست

        $this->bot = new Nutgram($config['token'], $configuration);
    }

    /**
     * ارسال نوتیفیکیشن فیش جدید به همه مدیران (role = manager یا admin)
     */
    public function notifyNewReceipt(Financial $receipt): void
    {
        // پیدا کردن مدیران دارای تلگرام
        $admins = User::whereIn('role', ['admin', 'manager'])
            ->whereNotNull('telegram_id')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        // اطلاعات نماینده
        $agent = User::find($receipt->creator);

        // تهیه کپشن
        $caption = "🧾 <b>فیش واریزی جدید (نیازمند تایید)</b>\n";
        $caption .= "➖➖➖➖➖➖➖➖➖➖\n";
        $caption .= "👤 <b>نماینده:</b> {$agent->name}\n";
        $caption .= "💰 <b>مبلغ:</b> " . number_format($receipt->price) . " تومان\n";
        $caption .= "📅 <b>تاریخ ثبت:</b> " . \Morilog\Jalali\Jalalian::now()->format('Y/m/d - H:i') . "\n";
        if ($receipt->description) {
            $caption .= "📝 <b>توضیحات:</b> {$receipt->description}\n";
        }

        // دکمه‌های تایید/رد (همانند ربات)
        $keyboard = \SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup::make()
            ->addRow(
                \SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton::make(
                    '✅ تایید و افزایش موجودی',
                    callback_data: "admin_handle_receipt:{$receipt->id}:approve"
                )
            )
            ->addRow(
                \SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton::make(
                    '❌ رد فیش',
                    callback_data: "admin_handle_receipt:{$receipt->id}:reject"
                )
            );

        // اگر فیش دارای attachment است، آن را به عنوان عکس ارسال کن
        $attachmentPath = $receipt->attachment;

        foreach ($admins as $admin) {
            try {
                if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                    // ارسال عکس
                    $fullPath = url('storage/'.$attachmentPath);
                    $this->bot->sendPhoto(
                        photo: $fullPath,
                        chat_id: $admin->telegram_id,
                        caption: $caption,
                        parse_mode: 'HTML',
                        reply_markup: $keyboard
                    );
                } else {
                    // اگر عکس وجود نداشت، فقط متن ارسال شود
                    $this->bot->sendMessage(
                        chat_id: $admin->telegram_id,
                        text: $caption,
                        parse_mode: 'HTML',
                        reply_markup: $keyboard
                    );
                }
            } catch (\Exception $e) {
                Log::warning("خطا در ارسال نوتیف فیش به ادمین {$admin->id}: ".public_path('storage/'.$attachmentPath) . $e->getMessage());
            }
        }
    }
}
