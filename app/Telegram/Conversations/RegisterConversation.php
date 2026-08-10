<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Telegram\Services\BotMenuService;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;

class RegisterConversation extends Conversation
{
    protected $name;
    protected $email;

    public function start(Nutgram $bot)
    {
        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(KeyboardButton::make('❌ انصراف'));

        $bot->sendMessage("📝 <b>ساخت حساب کاربری جدید</b>\n\nلطفاً <b>نام و نام خانوادگی</b> خود را ارسال کنید:", parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('getEmail');
    }

    public function getEmail(Nutgram $bot)
    {
        $text = $bot->message()?->text;

        if ($text === '❌ انصراف' || $text === '/start') {
            $bot->sendMessage('عملیات ثبت‌نام لغو شد.');
            BotMenuService::showGuestMenu($bot, $bot->user()->first_name ?? 'کاربر');
            $this->end();
            return;
        }

        $this->name = $text;

        $bot->sendMessage("📧 لطفاً <b>آدرس ایمیل</b> خود را ارسال کنید:\n(این ایمیل برای ورود به حساب استفاده خواهد شد)", parse_mode: 'HTML');
        $this->next('getPassword');
    }

    public function getPassword(Nutgram $bot)
    {
        $text = $bot->message()?->text;

        if ($text === '❌ انصراف' || $text === '/start') {
            $bot->sendMessage('عملیات ثبت‌نام لغو شد.');
            BotMenuService::showGuestMenu($bot, $bot->user()->first_name ?? 'کاربر');
            $this->end();
            return;
        }

        // بررسی فرمت ایمیل
        if (!filter_var($text, FILTER_VALIDATE_EMAIL)) {
            $bot->sendMessage("⚠️ <b>فرمت ایمیل نامعتبر است!</b>\nلطفاً یک ایمیل صحیح (مانند info@gmail.com) ارسال کنید:", parse_mode: 'HTML');
            return;
        }

        // بررسی تکراری نبودن ایمیل
        if (User::where('email', $text)->exists()) {
            $bot->sendMessage("⚠️ <b>این ایمیل قبلاً در سیستم ثبت شده است!</b>\nلطفاً ایمیل دیگری ارسال کنید یا وارد حساب خود شوید:", parse_mode: 'HTML');
            return;
        }

        $this->email = $text;

        $bot->sendMessage("🔑 بسیار عالی! حالا یک <b>کلمه عبور (پسورد)</b> برای حساب خود تعیین کنید:", parse_mode: 'HTML');
        $this->next('registerUser');
    }

    public function registerUser(Nutgram $bot)
    {
        $password = $bot->message()?->text;

        if ($password === '❌ انصراف' || $password === '/start') {
            $bot->sendMessage('عملیات ثبت‌نام لغو شد.');
            BotMenuService::showGuestMenu($bot, $bot->user()->first_name ?? 'کاربر');
            $this->end();
            return;
        }

        $bot->sendMessage("⏳ در حال ساخت حساب کاربری...");

        $find_mail = User::where('email',$this->email)->first();
        if($find_mail){
            $bot->sendMessage("⚠️ <b>ایمیل از قبل موجود میباشد چنانچه ایمیل مربوط به شماست لطفا وارد حساب کاربری خود شوید یا ایمیل جدیدی وارد نمایید!</b>", parse_mode: 'HTML');
            return;
        }
        // ساخت یوزر جدید با نقش customer
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($password),
            'role' => 'customer',
            'telegram_id' => $bot->userId(),
        ]);

        $bot->sendMessage("🎉 <b>تبریک!</b> حساب کاربری شما با موفقیت ایجاد شد.", parse_mode: 'HTML');

        // هدایت مستقیم کاربر به منوی اصلی
        BotMenuService::showMainMenu($bot, $user);
        $this->end();
    }
}
