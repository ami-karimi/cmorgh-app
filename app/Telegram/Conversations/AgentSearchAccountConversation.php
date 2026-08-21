<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use App\Models\Accounts;
use App\Models\User;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Morilog\Jalali\Jalalian;

class AgentSearchAccountConversation extends Conversation
{
    public function start(Nutgram $bot)
    {
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('🔙 بازگشت به منو', callback_data: 'back_to_admin_menu')
            );

        $bot->sendMessage("🔍 <b>جستجوی اکانت مشتری</b>\n\nلطفاً **نام کاربری (Username)** اکانت مورد نظر را ارسال کنید:", parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('findAccount');
    }

    public function findAccount(Nutgram $bot)
    {
        $username = trim($bot->message()?->text ?? '');

        if (empty($username)) {
            $bot->sendMessage("⚠️ نام کاربری نمی‌تواند خالی باشد. لطفاً مجدداً ارسال کنید:");
            return;
        }

        $agent = User::where('telegram_id', $bot->userId())->first();
        $account = Accounts::where('username', $username)->first();

        // 🔴 بررسی وجود اکانت و مالکیت آن (نماینده فقط اکانت خودش را ببیند)
        // فرض بر این است که یوزری که اکانت بهش متصل است، توسط این نماینده ساخته شده
        // اگر ساختار دیتابیس شما متفاوت است، این شرط را متناسب با آن تغییر دهید
        if (!$account || ($account->creatorUser && $account->creatorUser->parentAgent && $account->creatorUser->parentAgent->id !== $agent->id )) {
            $keyboard = InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('🔄 جستجوی مجدد', callback_data: 'agent_manage_acc'),
                    InlineKeyboardButton::make('🔙 بازگشت به منو', callback_data: 'back_to_admin_menu')
                );

            $bot->sendMessage("❌ اکانتی با نام کاربری <code>{$username}</code> یافت نشد یا متعلق به شما نیست!", parse_mode: 'HTML', reply_markup: $keyboard);
            $this->end();
            return;
        }

        \App\Telegram\Services\BotMenuService::renderAccountCardAgent($bot, $account, isEdit: false);
        $this->end();
    }
}
