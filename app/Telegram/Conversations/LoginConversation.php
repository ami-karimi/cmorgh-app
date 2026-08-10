<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Telegram\Services\BotMenuService;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;

class LoginConversation extends Conversation
{
    protected $email;

    public function start(Nutgram $bot)
    {
        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(KeyboardButton::make('❌ انصراف'));

        $text = "🔐 <b>ورود به حساب کاربری</b>\n\nلطفاً <b>آدرس ایمیل</b> خود را ارسال کنید:";

        $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('getPassword');
    }

    public function getPassword(Nutgram $bot)
    {
        $text = $bot->message()?->text;

        if ($text === '❌ انصراف' || $text === '/start') {
            $bot->sendMessage('عملیات ورود لغو شد.');
            BotMenuService::showGuestMenu($bot, $bot->user()->first_name ?? 'کاربر');
            $this->end();
            return;
        }

        $this->email = $text;

        $bot->sendMessage("🔑 حالا لطفاً <b>رمز عبور (Password)</b> خود را ارسال کنید:", parse_mode: 'HTML');
        $this->next('authenticate');
    }

    public function authenticate(Nutgram $bot)
    {
        $password = $bot->message()?->text;

        if ($password === '❌ انصراف' || $password === '/start') {
            $bot->sendMessage('عملیات ورود لغو شد.');
            BotMenuService::showGuestMenu($bot, $bot->user()->first_name ?? 'کاربر');
            $this->end();
            return;
        }

        $bot->sendMessage('⏳ در حال بررسی اطلاعات...');

        // 🔴 جستجو فقط بر اساس ایمیل انجام می‌شود
        $user = User::where('email', $this->email)->first();

        if ($user && Hash::check($password, $user->password)) {
            $user->telegram_id = $bot->userId();
            $user->save();

            $bot->sendMessage("✅ <b>ورود موفقیت‌آمیز بود!</b>\nخوش آمدید، {$user->name} عزیز.", parse_mode: 'HTML');
            BotMenuService::showMainMenu($bot, $user);
        } else {
            $bot->sendMessage("❌ <b>اطلاعات ورود اشتباه است!</b>\nایمیل یا رمز عبور یافت نشد.\n\nلطفاً مجدداً ایمیل خود را ارسال کنید (یا برای لغو، انصراف را بزنید):", parse_mode: 'HTML');
            $this->start($bot);
            return;
        }

        $this->end();
    }
}
