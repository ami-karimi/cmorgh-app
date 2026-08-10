<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use App\Models\User;
use App\Models\Financial;
use Illuminate\Support\Facades\Storage;

class AgentSubmitReceiptConversation extends Conversation
{
    public $amount;

    /**
     * مرحله ۱: دریافت مبلغ واریزی
     */
    public function start(Nutgram $bot)
    {
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('❌ لغو و بازگشت', callback_data: 'cancel_receipt')
            );

        $bot->editMessageText(
            "🧾 <b>ثبت فیش واریزی جدید</b>\n\nلطفاً <b>مبلغ واریز شده</b> را به تومان وارد کنید (فقط عدد انگلیسی):",
            parse_mode: 'HTML',
            reply_markup: $keyboard
        );
        $this->next('askForPhoto');
    }

    /**
     * مرحله ۲: بررسی مبلغ و درخواست عکس فیش
     */
    public function askForPhoto(Nutgram $bot)
    {
        if ($bot->isCallbackQuery() && $bot->callbackQuery()->data === 'cancel_receipt') {
            $bot->answerCallbackQuery();
            $this->cancelAndReturn($bot);
            return;
        }

        $amountInput = $bot->message()?->text;

        // پاکسازی کاما یا حروف اضافی از مبلغ
        $amount = (int) preg_replace('/\D/', '', $amountInput ?? '0');

        if ($amount < 1000) {
            $bot->sendMessage("⚠️ مبلغ وارد شده نامعتبر است (حداقل 1,000 تومان).\nلطفاً مبلغ را مجدداً به صورت عددی ارسال کنید:");
            return;
        }

        $this->amount = $amount;

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('❌ لغو و بازگشت', callback_data: 'cancel_receipt')
            );

        $bot->sendMessage(
            "💰 <b>مبلغ ثبت شده:</b> " . number_format($this->amount) . " تومان\n\n📸 حالا لطفاً <b>تصویر فیش واریزی</b> خود را ارسال کنید:",
            parse_mode: 'HTML',
            reply_markup: $keyboard
        );

        $this->next('saveReceipt');
    }

    /**
     * مرحله ۳: دریافت عکس، ذخیره روی سرور و ثبت در دیتابیس
     */
    /**
     * مرحله ۳: دریافت عکس، ذخیره روی سرور و ثبت در دیتابیس
     */
    public function saveReceipt(Nutgram $bot)
    {
        if ($bot->isCallbackQuery() && $bot->callbackQuery()->data === 'cancel_receipt') {
            $bot->answerCallbackQuery();
            $this->cancelAndReturn($bot);
            return;
        }

        if (!$bot->message()?->photo) {
            $bot->sendMessage("⚠️ لطفاً یک تصویر (عکس فیش) ارسال کنید، متن یا فایل قابل قبول نیست.");
            return;
        }

        $bot->sendMessage("⏳ در حال آپلود و ثبت فیش شما...");

        try {
            // ۱. دریافت فایل از تلگرام
            $photos = $bot->message()->photo;
            $bestPhoto = end($photos);
            $fileId = $bestPhoto->file_id; // آیدی فایل در سرور تلگرام

            $file = $bot->getFile($fileId);
            $filename = '/financial/' . time() . '_' . rand(1000, 9999) . '.jpg';

            $fullPath = public_path('storage/attachments' . $filename);

            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            $bot->downloadFile($file, $fullPath);

            // ۲. پیدا کردن نماینده
            $agent = User::where('telegram_id', $bot->userId())->first();

            $receipt = Financial::create([
                'creator'     => $agent->id,
                'for'         => $agent->id,
                'type'        => 'plus',
                'price'       => $this->amount,
                'description' => 'ثبت فیش واریزی از طریق ربات تلگرام',
                'attachment'  => $filename,
                'approved'    => 0,
            ]);


            $admins = User::whereIn('role', ['admin', 'manager'])
                ->whereNotNull('telegram_id')
                ->get();

            $caption = "🧾 <b>فیش واریزی جدید (نیازمند تایید)</b>\n";
            $caption .= "➖➖➖➖➖➖➖➖➖➖\n";
            $caption .= "👤 <b>نماینده:</b> {$agent->name}\n";
            $caption .= "💰 <b>مبلغ:</b> " . number_format($this->amount) . " تومان\n";
            $caption .= "📅 <b>تاریخ ثبت:</b> " . \Morilog\Jalali\Jalalian::now()->format('Y/m/d - H:i') . "\n";

            $adminKeyboard = InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('✅ تایید و افزایش موجودی', callback_data: "admin_approve_receipt:{$receipt->id}")
                )
                ->addRow(
                    InlineKeyboardButton::make('❌ رد فیش', callback_data: "admin_reject_receipt:{$receipt->id}")
                );

            foreach ($admins as $admin) {
                try {
                    // با استفاده از $fileId تلگرام عکس را در صدم ثانیه فروارد می‌کند بدون نیاز به آپلود مجدد
                    $bot->sendPhoto(
                        photo: $fileId,
                        chat_id: $admin->telegram_id,
                        caption: $caption,
                        parse_mode: 'HTML',
                        reply_markup: $adminKeyboard
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("خطا در ارسال نوتیف فیش برای ادمین {$admin->id}: " . $e->getMessage());
                }
            }

            // ۵. پیام موفقیت به نماینده
            $keyboard = InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu'));

            $bot->sendMessage(
                "✅ <b>فیش شما با موفقیت ثبت شد!</b>\n\nمدیریت سیستم هم‌اکنون این فیش را دریافت کرد. پس از تایید، مبلغ " . number_format($this->amount) . " تومان به کیف پول شما اضافه خواهد شد.",
                parse_mode: 'HTML',
                reply_markup: $keyboard
            );

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('خطا در ذخیره فیش تلگرام: ' . $e->getMessage());
            $bot->sendMessage("❌ <b>خطا در ثبت فیش:</b> متاسفانه مشکلی رخ داد. لطفاً به پشتیبانی اطلاع دهید.", parse_mode: 'HTML');
        }

        $this->end();
    }

    /**
     * خروج از مکالمه
     */
    private function cancelAndReturn(Nutgram $bot)
    {
        $bot->deleteMessage($bot->chatId(), $bot->messageId());

        $user = User::where('telegram_id', $bot->userId())->first();
        if ($user) {
            \App\Telegram\Services\BotMenuService::showMainMenu($bot, $user);
        }
        $this->end();
    }
}
