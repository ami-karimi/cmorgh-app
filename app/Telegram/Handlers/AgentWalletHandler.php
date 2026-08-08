<?php
namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\Financial;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class AgentWalletHandler
{
    /**
     * متد اصلی برای پردازش کلیک روی دکمه "موجودی ولت"
     */
    public function __invoke(Nutgram $bot)
    {
        $user = User::where('telegram_id', $bot->userId())->first();

        if (!$user) {
            $bot->answerCallbackQuery(text: '❌ کاربر یافت نشد!', show_alert: true);
            return;
        }

        // بستن حالت لودینگ دکمه شیشه‌ای
        $bot->answerCallbackQuery();

        // دریافت موجودی
        $balance = number_format($user->balance);

        // بررسی آخرین فیش معلق
        $pendingReceipt = Financial::where('for', $user->id)
                            ->where('type', 'plus')
                            ->where('approved', 0)
                            ->latest()
                            ->first();

        // ساخت متن پیام
        $text = $this->generateWalletText($user, $balance, $pendingReceipt);

        // ساخت دکمه‌ها
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('➕ افزایش موجودی (ثبت فیش)', callback_data: 'agent_submit_receipt')
            )
            ->addRow(
                InlineKeyboardButton::make('🔙 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu')
            );

        $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
    }

    /**
     * یک متد کمکی (Helper) برای مرتب ماندن متن پیام
     */
    private function generateWalletText($user, $balance, $pendingReceipt): string
    {
        $text = "💰 <b>کیف پول کاربری شما</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "👤 <b>صاحب حساب:</b> {$user->name}\n";
        $text .= "💳 <b>موجودی فعلی:</b> {$balance} تومان\n\n";

        if ($pendingReceipt) {
            $text .= "⏳ <i>شما یک فیش واریزی به مبلغ " . number_format($pendingReceipt->price) . " تومان در صف انتظار تایید دارید.</i>\n\n";
        }

        $text .= "🏦 <b>اطلاعات حساب جهت واریز:</b>\n";
        $text .= "💳 <b>شماره کارت:</b> <code>6104337000000000</code>\n";
        $text .= "👤 <b>به نام:</b> نام و نام خانوادگی شما\n";
        $text .= "🏛 <b>بانک:</b> ملت\n\n";
        $text .= "💡 <i>جهت افزایش موجودی، ابتدا مبلغ مورد نظر را واریز کرده و سپس فیش آن را ثبت کنید.</i>";

        return $text;
    }
}
