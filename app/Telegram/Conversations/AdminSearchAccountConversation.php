<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use App\Models\Accounts;
use App\Models\User;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Morilog\Jalali\Jalalian;

class AdminSearchAccountConversation extends Conversation
{
    /**
     * شروع مکالمه: دریافت یوزرنیم از ادمین
     */
    public function start(Nutgram $bot)
    {
        $telegramId = $bot->userId();
        $user = User::where('telegram_id', $telegramId)->first();

        // بررسی دسترسی ادمین/مدیر
        if (!$user || !in_array($user->role, ['admin', 'manager'])) {
            $bot->sendMessage("⛔ شما دسترسی به این بخش را ندارید.");
            $this->end();
            return;
        }

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('🔙 بازگشت به منو', callback_data: 'back_to_admin_menu')
            );

        $bot->sendMessage("🔍 لطفاً **نام کاربری (Username)** اکانت مورد نظر را ارسال کنید:", reply_markup: $keyboard);
        $this->next('findAccount');
    }

    /**
     * پیدا کردن اکانت و نمایش کارت اطلاعات به همراه دکمه‌های مدیریت
     */
    public function findAccount(Nutgram $bot)
    {
        $username = trim($bot->message()?->text ?? '');

        if (empty($username)) {
            $bot->sendMessage("⚠️ نام کاربری نمی‌تواند خالی باشد. لطفاً مجدداً ارسال کنید:");
            return;
        }

        $account = Accounts::where('username', $username)->first();

        if (!$account) {
            $keyboard = InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('🔄 جستجوی مجدد', callback_data: 'admin_manage_acc'),
                    InlineKeyboardButton::make('🔙 بازگشت به منو', callback_data: 'back_to_admin_menu')
                );

            $bot->sendMessage("❌ اکانتی با نام کاربری <code>{$username}</code> یافت نشد!", parse_mode: 'HTML', reply_markup: $keyboard);
            $this->end();
            return;
        }

        // محاسبه مشخصات اکانت
        $statusText = $account->is_enabled ? "🟢 فعال" : "🔴 مسدود / غیرفعال";
        $onlineText = $account->is_online ? "⚡ آنلاین" : "💤 آفلاین";

        // محاسبه حجم
        $usageFormatted = method_exists($account, 'formatBytes') ? $account->formatBytes($account->usage) : round($account->usage / (1024*1024*1024), 2) . " GB";
        $maxUsageFormatted = $account->max_usage == 0 ? "نامحدود" : (method_exists($account, 'formatBytes') ? $account->formatBytes($account->max_usage) : round($account->max_usage / (1024*1024*1024), 2) . " GB");

        // محاسبه انقضا
        $expireText = "شروع نشده / بدون انقضا";
        if ($account->expire_date) {
            $jalaliDate = Jalalian::forge($account->expire_date)->format('Y/m/d - H:i');
            $daysLeft = (int) now()->diffInDays($account->expire_date, false);
            $expireText = $daysLeft > 0 ? "{$jalaliDate} ({$daysLeft} روز مانده)" : "{$jalaliDate} (منقضی شده)";
        }

        $creatorName = $account->creatorUser?->name ?? 'سیستم';

        $text = "👤 <b>اطلاعات اکانت:</b> <code>{$account->username}</code>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "🔑 <b>رمز عبور:</b> <code>{$account->password}</code>\n";
        $text .= "📊 <b>وضعیت حساب:</b> {$statusText} | {$onlineText}\n";
        $text .= "📦 <b>سرویس:</b> <code>{$account->service_group}</code>\n";
        $text .= "👨‍💼 <b>نماینده/سازنده:</b> {$creatorName}\n";
        $text .= "💾 <b>مصرف ترافیک:</b> {$usageFormatted} / {$maxUsageFormatted}\n";
        $text .= "📅 <b>تاریخ انقضا:</b> {$expireText}\n";

        // ساخت دکمه‌های مدیریت سریع
        $toggleBtnText = $account->is_enabled ? "🔴 مسدودسازی" : "🟢 فعال‌سازی";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make($toggleBtnText, callback_data: "admin_toggle_acc:{$account->id}"),
                InlineKeyboardButton::make('🔄 شارژ مجدد', callback_data: "admin_recharge_acc:{$account->id}")
            );

        // 🔴 اضافه کردن دکمه‌های کانفیگ فقط اگر سرویس وایرگارد باشد
        if ($account->service_group === 'wireguard') {
            $keyboard->addRow(
                InlineKeyboardButton::make('📥 دریافت فایل کانفیگ', callback_data: "dl_wg_conf:{$account->id}"),
                InlineKeyboardButton::make('📱 دریافت QR Code', callback_data: "dl_wg_qr:{$account->id}")
            );
        }

        $keyboard->addRow(
            InlineKeyboardButton::make('🔍 جستجوی اکانت دیگر', callback_data: 'admin_manage_acc'),
            InlineKeyboardButton::make('🏠 منوی اصلی ادمین', callback_data: 'back_to_admin_menu')
        );

        $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        $this->end();
    }

}
