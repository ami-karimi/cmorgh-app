<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\Financial;
use App\Models\AgentBankAccount;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class AgentWalletHandler
{
    /**
     * متد اصلی برای پردازش کلیک روی دکمه "موجودی ولت"
     */
    public function __invoke(Nutgram $bot)
    {
        // 🔴 راه‌حل: چک کردن نوع درخواست (بستن لودینگ فقط در صورت شیشه‌ای بودن دکمه)
        if ($bot->isCallbackQuery()) {
            try {
                $bot->answerCallbackQuery();
            } catch (\Exception $e) {}
        }

        $user = User::where('telegram_id', $bot->userId())->first();

        if (!$user) {
            if ($bot->isCallbackQuery()) {
                $bot->answerCallbackQuery(text: '❌ کاربر یافت نشد!', show_alert: true);
            } else {
                $bot->sendMessage('❌ کاربر یافت نشد!');
            }
            return;
        }

        // ۱. دریافت موجودی فعلی
        $balance = number_format($user->balance);

        // ۲. بررسی فیش معلق
        $pendingReceipt = Financial::where('for', $user->id)
                            ->where('type', 'plus')
                            ->where('approved', 0)
                            ->latest()
                            ->first();

        // ۳. دریافت داینامیک اطلاعات حساب بانکی منیجر/بالاسری
        $bankAccount = $this->getManagerBankAccount($user);

        // ۴. ساخت متن پیام
        $text = $this->generateWalletText($user, $balance, $pendingReceipt, $bankAccount);

        // ۵. ساخت دکمه‌ها
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('➕ افزایش موجودی (ثبت فیش)', callback_data: 'agent_submit_receipt')
            )
            ->addRow(
                // دکمه بازگشت برای مواقعی که کاربر در منوهای تودرتو است به درد می‌خورد
                InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu')
            );

        // 🔴 راه‌حل: ارسال پیام یا ویرایش آن بر اساس نوع کلیک کاربر
        if ($bot->isCallbackQuery()) {
            $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
        } else {
            $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }

    /**
     * دریافت شماره حساب بانکی منیجر (یا ادمین اصلی در صورت عدم وجود بالاسری)
     */
    private function getManagerBankAccount(User $user): ?AgentBankAccount
    {
        if ($user->creator) {
            $account = AgentBankAccount::where('user_id', $user->creator)->first();
            if ($account) {
                return $account;
            }
        }

        $managerId = User::whereIn('role', ['admin', 'manager'])->value('id');

        return AgentBankAccount::where('user_id', $managerId)->first() ?? AgentBankAccount::first();
    }

    /**
     * تولید متن پیام موجودی به همراه اطلاعات حساب بانکی داینامیک
     */
    private function generateWalletText(User $user, string $balance, $pendingReceipt, ?AgentBankAccount $bankAccount): string
    {
        $text = "💰 <b>کیف پول کاربری شما</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "👤 <b>صاحب حساب:</b> {$user->name}\n";
        $text .= "💳 <b>موجودی فعلی:</b> {$balance} تومان\n\n";

        if ($pendingReceipt) {
            $text .= "⏳ <i>شما یک فیش واریزی به مبلغ " . number_format($pendingReceipt->price) . " تومان در صف انتظار تایید دارید.</i>\n\n";
        }

        $text .= "🏦 <b>اطلاعات حساب جهت واریز:</b>\n";

        if ($bankAccount) {
            if ($bankAccount->bank_name) {
                $text .= "🏛 <b>بانک:</b> {$bankAccount->bank_name}\n";
            }
            if ($bankAccount->account_name) {
                $text .= "👤 <b>به نام:</b> {$bankAccount->account_name}\n";
            }
            if ($bankAccount->card_number) {
                $text .= "💳 <b>شماره کارت:</b> <code>{$bankAccount->card_number}</code>\n";
            }
            if ($bankAccount->sheba_number) {
                $text .= "🔢 <b>شماره شبا:</b> <code>{$bankAccount->sheba_number}</code>\n";
            }
        } else {
            $text .= "⚠️ <i>اطلاعات حسابی برای واریز در سیستم ثبت نشده است. لطفاً با پشتیبانی تماس بگیرید.</i>\n";
        }

        $text .= "\n💡 <i>جهت افزایش موجودی، ابتدا مبلغ را به حساب بالا واریز کرده و سپس روی «➕ افزایش موجودی» کلیک کنید.</i>";

        return $text;
    }
}
