<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class AgentHandler
{
    public function showReferralInfo(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            try { $bot->answerCallbackQuery(); } catch (\Exception $e) {}
        }

        $user = User::where('telegram_id', $bot->userId())->whereIn('role',['agent','sub_agent'])->first();

        if (!$user) {
            $bot->sendMessage("❌ حساب کاربری شما یافت نشد.");
            return;
        }

        // دریافت کد و لینک اختصاصی از Accessorهای مدل User
        $referralCode = $user->referral_code;
        $referralLink = $user->referral_link;

        $text = "🔑 <b>اطلاعات و لینک دعوت اختصاصی شما</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n\n";

        $text .= "📌 <b>کد معرف اختصاصی شما:</b>\n";
        $text .= "<code>{$referralCode}</code>\n\n";

        $text .= "🔗 <b>لینک مستقیم ثبت‌نام مشتریان:</b>\n";
        $text .= "<code>{$referralLink}</code>\n\n";

        $text .= "💡 <i>راهنما:</i>\n";
        $text .= "۱. می‌توانید <b>لینک بالا</b> را برای مشتریان خود بفرستید؛ با کلیک روی آن، سیستم به صورت خودکار آن‌ها را زیرمجموعه شما قرار می‌دهد.\n";
        $text .= "۲. یا می‌توانید فقط <b>کد معرف</b> را به مشتری بدهید تا در مراحل ثبت‌نام دستی وارد کند.\n\n";
        $text .= "👇 <i>برای کپی کردن هر کدام، کافیست روی باکس خاکستری آن لمس/کلیک کنید.</i>";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu')
            );

        if ($bot->isCallbackQuery()) {
            $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
        } else {
            $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }
}
