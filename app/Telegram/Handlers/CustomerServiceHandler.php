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

        // 🔴 تغییر مهم: استفاده از ریلیشن vpnAccounts به جای کوئری مستقیم
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

        $text = "⚙️ <b>مدیریت سرویس‌های شما</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "لطفاً برای مشاهده جزئیات و مدیریت، روی سرویس مورد نظر کلیک کنید:";

        $keyboard = InlineKeyboardMarkup::make();

        // ساخت یک دکمه مجزا برای هر سرویس
        foreach ($accounts as $account) {
            $statusIcon = $account->is_enabled ? '🟢' : '🔴';
            $buttonText = "{$statusIcon} سرویس: {$account->username} (" . ($account->service_group ?? 'V2Ray') . ")";

            $keyboard->addRow(
                InlineKeyboardButton::make($buttonText, callback_data: "cust_show_service:{$account->id}")
            );
        }

        // دکمه بستن / بازگشت
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
