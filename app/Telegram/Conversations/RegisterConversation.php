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
    public $agentId;

    public function start(Nutgram $bot)
    {

        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(KeyboardButton::make('❌ انصراف'));

        if ($this->agentId > 0) {
            $bot->sendMessage("📝 <b>ساخت حساب کاربری جدید</b>\n\n👤 لطفاً <b>نام و نام خانوادگی</b> خود را ارسال کنید:", parse_mode: 'HTML', reply_markup: $keyboard);
            $this->next('getEmail');
        }
        else {
            $bot->sendMessage($this->agentId."📝 <b>ساخت حساب کاربری جدید</b>\n\n🤝 لطفاً <b>کد معرف</b> خود را وارد کنید:\n\n<i>⚠️ نکته: ثبت‌نام در سیستم فقط با داشتن کد معرف امکان‌پذیر است.</i>", parse_mode: 'HTML', reply_markup: $keyboard);
            $this->next('processReferralCode');
        }
    }

    /**
     * بررسی کد معرف (اگر کاربر با لینک نیامده باشد)
     */
    public function processReferralCode(Nutgram $bot)
    {
        $code = $bot->message()?->text;

        if ($code === '❌ انصراف' || $code === '/start') {
            $this->cancelRegistration($bot);
            return;
        }

        // رمزگشایی کد معرف
        $salt = 852963;
        $decodedAgentId = (int) (base_convert($code, 36, 10) / $salt);

        $agent = User::find($decodedAgentId);

        if (!$agent) {
            $bot->sendMessage("❌ <b>کد معرف نامعتبر است!</b>\nلطفاً کد صحیح را مجدداً ارسال کنید (یا دکمه انصراف را بزنید):", parse_mode: 'HTML');
            return; // منتظر می‌ماند تا کد درست وارد شود
        }

        // ذخیره آیدی در کلاس
        $this->agentId = $agent->id;

        // عبور از مرحله کد معرف و درخواست نام
        $bot->sendMessage("✅ کد معرف تایید شد.\n\n👤 حالا لطفاً <b>نام و نام خانوادگی</b> خود را ارسال کنید:", parse_mode: 'HTML');
        $this->next('getEmail');
    }

    /**
     * دریافت نام و درخواست ایمیل
     */
    public function getEmail(Nutgram $bot)
    {
        $text = $bot->message()?->text;

        if ($text === '❌ انصراف' || $text === '/start') {
            $this->cancelRegistration($bot);
            return;
        }

        $this->name = $text;

        $bot->sendMessage("📧 لطفاً <b>آدرس ایمیل</b> خود را ارسال کنید:\n(این ایمیل برای ورود به پنل استفاده خواهد شد)", parse_mode: 'HTML');
        $this->next('getPassword');
    }

    /**
     * دریافت ایمیل و درخواست پسورد
     */
    public function getPassword(Nutgram $bot)
    {
        $text = $bot->message()?->text;

        if ($text === '❌ انصراف' || $text === '/start') {
            $this->cancelRegistration($bot);
            return;
        }

        // بررسی فرمت ایمیل
        if (!filter_var($text, FILTER_VALIDATE_EMAIL)) {
            $bot->sendMessage("⚠️ <b>فرمت ایمیل نامعتبر است!</b>\nلطفاً یک ایمیل صحیح (مانند info@gmail.com) ارسال کنید:", parse_mode: 'HTML');
            return;
        }

        // بررسی تکراری نبودن ایمیل
        if (User::where('email', $text)->exists()) {
            $bot->sendMessage("⚠️ <b>این ایمیل قبلاً در سیستم ثبت شده است!</b>\nلطفاً ایمیل دیگری ارسال کنید یا برای لغو، انصراف را بزنید:", parse_mode: 'HTML');
            return;
        }

        $this->email = $text;

        $bot->sendMessage("🔑 بسیار عالی! حالا یک <b>کلمه عبور (پسورد)</b> برای حساب خود تعیین کنید:", parse_mode: 'HTML');
        $this->next('finalizeRegistration');
    }

    /**
     * دریافت پسورد و ثبت نهایی کاربر
     */
    public function finalizeRegistration(Nutgram $bot)
    {
        $password = $bot->message()?->text;

        if ($password === '❌ انصراف' || $password === '/start') {
            $this->cancelRegistration($bot);
            return;
        }

        $bot->sendMessage("⏳ در حال ساخت حساب کاربری...");

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'creator' => $this->agentId, // 🎯 ثبت با آیدی نماینده
            'password' => Hash::make($password),
            'role' => 'customer',
            'telegram_id' => $bot->userId(),
        ]);

        $bot->sendMessage("🎉 <b>تبریک!</b> حساب کاربری شما با موفقیت ایجاد شد.", parse_mode: 'HTML');

        BotMenuService::showMainMenu($bot, $user);
        $this->end();
    }

    /**
     * لغو عملیات ثبت‌نام
     */
    protected function cancelRegistration(Nutgram $bot)
    {
        $bot->sendMessage('❌ عملیات ثبت‌نام لغو شد.');
        BotMenuService::showGuestMenu($bot, $bot->user()->first_name ?? 'کاربر');
        $this->end();
    }
}
