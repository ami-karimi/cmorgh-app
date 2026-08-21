<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\Accounts;
use App\Telegram\Services\BotMenuService;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class CustomerServiceHandler
{
    /**
     * ۱. نمایش لیست تمام سرویس‌های کاربر به صورت دکمه‌های شیشه‌ای
     */
    public function listServices(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            try {
                $bot->answerCallbackQuery();
            } catch (\Exception $e) {}
        }

        $user = User::where('telegram_id', $bot->userId())->first();

        if (!$user) {
            $bot->sendMessage('❌ حساب کاربری شما یافت نشد.');
            return;
        }

        $accounts = $user->vpnAccounts;

        if ($accounts->isEmpty()) {
            $text = "❌ <b>شما در حال حاضر هیچ سرویس فعالی ندارید.</b>\n\nجهت خرید سرویس جدید از منوی پایین روی «🛍 سفارش سرویس جدید» کلیک کنید.";

            if ($bot->isCallbackQuery()) {
                $bot->editMessageText($text, parse_mode: 'HTML');
            } else {
                $bot->sendMessage($text, parse_mode: 'HTML');
            }
            return;
        }

        $text = "⚙️ <b>لیست و وضعیت سرویس‌های شما:</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n\n";

        $keyboard = InlineKeyboardMarkup::make();

        // حلقه برای تولید متن گزارش هر اکانت و دکمه آن
        foreach ($accounts as $index => $account) {
            $rowNumber = $index + 1;

            // ۱. تعیین پروتکل (تبدیل l2tp_cisco به L2TP)
            $protocol = $account->service_group ?? 'نامشخص';
            $protocol = str_ireplace(['l2tp_cisco', 'l2tp-cisco', 'l2tp/cisco'], 'L2TP', $protocol);

            // ۲. محاسبه روزهای باقیمانده (با شرط اولین اتصال)
            $isExpired = false;

            if (!empty($account->expire_date)) {
                $expireDate = \Carbon\Carbon::parse($account->expire_date);
                if ($expireDate->isPast()) {
                    $daysLeftText = "پایان یافته ❌";
                    $isExpired = true;
                } else {
                    $daysLeftText = round(now()->diffInDays($expireDate)) . " روز";
                }
            } else {
                // وقتی هنوز وصل نشده
                $daysLeftText = "محاسبه پس از اولین اتصال ⏳";
            }

            // ۳. فراخوانی حجم‌ها دقیقاً از روی مدل Accounts شما
            if (!$account->max_usage || $account->max_usage <= 0) {
                $volumeText = "نامحدود ∞";
            } else {
                // استفاده از Attribute های مدل
                $volumeText = "{$account->used_formatted} مصرفی | {$account->remaining_formatted} باقیمانده (کل: {$account->max_formatted})";
            }

            // ۴. وضعیت کلی سرویس
            $isActive = $account->is_enabled && !$isExpired;
            $statusIcon = $isActive ? '🟢' : '🔴';
            $statusText = $isActive ? 'فعال و متصل' : 'غیرفعال / منقضی';

            // ۵. اضافه کردن اطلاعات به متن اصلی
            $text .= "{$statusIcon} <b>سرویس {$rowNumber}:</b> <code>{$account->username}</code>\n";
            $text .= "🌐 <b>نوع سرویس:</b> {$protocol}\n";
            $text .= "⏳ <b>اعتبار زمانی:</b> {$daysLeftText}\n";
            $text .= "📊 <b>وضعیت حجم:</b> {$volumeText}\n";
            $text .= "وضعیت اکانت: <i>{$statusText}</i>\n";
            $text .= "〰️〰️〰️〰️〰️〰️〰️〰️〰️\n";

            // ۶. اضافه کردن دکمه مدیریت برای این سرویس
            $buttonText = "{$statusIcon} تنظیمات سرویس {$rowNumber} ({$protocol})";
            $keyboard->addRow(
                InlineKeyboardButton::make($buttonText, callback_data: "cust_show_service:{$account->id}")
            );
        }

        $text .= "\n👇 <i>جهت دریافت لینک اتصال و تمدید، روی دکمه‌ی سرویس مورد نظر کلیک کنید:</i>";

        // دکمه بازگشت
        $keyboard->addRow(
            InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu')
        );

        if ($bot->isCallbackQuery()) {
            $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
        } else {
            $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }

    /**
     * ۲. نمایش کارت مدیریت یک سرویس انتخاب‌شده
     */
    public function showServiceDetail(Nutgram $bot, $id)
    {
        if ($bot->isCallbackQuery()) {
            try {
                $bot->answerCallbackQuery();
            } catch (\Exception $e) {}
        }

        $user = User::where('telegram_id', $bot->userId())->first();

        if (!$user) {
            $bot->sendMessage('❌ حساب کاربری یافت نشد!');
            return;
        }

        // 🔴 تغییر مهم: بررسی اینکه آیا این اکانت در لیست اکانت‌های این کاربر وجود دارد یا خیر
        $account = $user->vpnAccounts()->where('account_id', $id)->first();

        // بررسی امنیت (اگر کاربر سعی کرد با دستکاری ID دکمه، اکانت شخص دیگری را ببیند)
        if (!$account) {
            $bot->sendMessage('❌ سرویس مورد نظر یافت نشد یا متعلق به شما نیست!');
            return;
        }

        // رندر کارت اختصاصی سرویس برای مشتری با قابلیت ویرایش پیام
        BotMenuService::renderCustomerAccountCard($bot, $account, isEdit: true);
    }
}
