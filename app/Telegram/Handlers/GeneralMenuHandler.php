<?php
namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Telegram\Services\BotMenuService;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;

class GeneralMenuHandler
{
    // 🔴 این متد برای زمانی است که کاربر دستور /start را می‌فرستد
    public function start(Nutgram $bot, ?string $payload = null)
    {
        $telegramId = $bot->userId();
        $user = User::where('telegram_id', $telegramId)->first();

        // ۱. اگر کاربر از قبل اکانت دارد (نمایش منوی اصلی)
        if ($user) {
            BotMenuService::showMainMenu($bot, $user);
            return;
        }

        // ۲. اگر کاربر لینک اختصاصی ندارد (نمایش منوی مهمان جهت لاگین)
        if (!$payload || !str_starts_with($payload, 'ref_')) {
            BotMenuService::showGuestMenu($bot, $bot->user()->first_name ?? 'کاربر');
            return;
        }

        // ۳. استخراج هش و پیدا کردن نماینده
        $referralCode = str_replace('ref_', '', $payload);
        $salt = 852963;
        $agentId = (int) (base_convert($referralCode, 36, 10) / $salt);

        $agent = User::find($agentId);

        // اگر نماینده پیدا نشد
        if (!$agent) {
            $bot->sendMessage("❌ لینک دعوت نامعتبر است یا نماینده یافت نشد.");
            BotMenuService::showGuestMenu($bot, $bot->user()->first_name ?? 'کاربر');
            return;
        }
        $registerConv = new \App\Telegram\Conversations\RegisterConversation();
        $registerConv->agentId = $agent->id; // مقداردهی مستقیم به متغیر کلاس
        $registerConv($bot);
    }

    public function getPassword(Nutgram $bot)
    {
        $text = $bot->message()?->text;

        // بررسی دکمه انصراف
        if ($text === '❌ انصراف از ورود' || $text === '/start') {
            $bot->sendMessage('عملیات ورود لغو شد.');
            BotMenuService::showGuestMenu($bot, $bot->user()->first_name ?? 'کاربر');
            $this->end();
            return;
        }

        if (empty($text)) {
            $bot->sendMessage('⚠️ لطفاً یک متن معتبر به عنوان نام کاربری ارسال کنید:');
            return;
        }

        // ذخیره نام کاربری در کلاس
        $this->username = $text;

        $bot->sendMessage("🔑 حالا لطفاً <b>رمز عبور (Password)</b> خود را ارسال کنید:", parse_mode: 'HTML');

        // رفتن به مرحله بررسی
        $this->next('authenticate');
    }

    /**
     * مرحله ۳: دریافت رمز و اعتبارسنجی
     */
    public function authenticate(Nutgram $bot)
    {
        $password = $bot->message()?->text;

        // بررسی دکمه انصراف
        if ($password === '❌ انصراف از ورود' || $password === '/start') {
            $bot->sendMessage('عملیات ورود لغو شد.');
            BotMenuService::showGuestMenu($bot, $bot->user()->first_name ?? 'کاربر');
            $this->end();
            return;
        }

        if (empty($password)) {
            $bot->sendMessage('⚠️ لطفاً رمز عبور را به صورت متنی ارسال کنید:');
            return;
        }

        $bot->sendMessage('⏳ در حال بررسی اطلاعات...');

        // جستجوی کاربر بر اساس نام کاربری یا شماره تلفن
        $user = User::where('username', $this->username)
            ->orWhere('phone', $this->username)
            ->first();

        // بررسی صحت کاربر و رمز عبور
        if ($user && Hash::check($password, $user->password)) {

            // آپدیت کردن آیدی تلگرام کاربر
            $user->telegram_id = $bot->userId();
            $user->save();

            $bot->sendMessage("✅ <b>ورود موفقیت‌آمیز بود!</b>\n\nخوش آمدید، {$user->name} عزیز.", parse_mode: 'HTML');

            // نمایش منوی اصلی بر اساس نقش کاربر
            BotMenuService::showMainMenu($bot, $user);

        } else {
            $bot->sendMessage("❌ <b>اطلاعات ورود اشتباه است!</b>\nنام کاربری یا رمز عبور یافت نشد.\n\nلطفاً مجدداً نام کاربری خود را ارسال کنید (یا برای لغو، دکمه انصراف را بزنید):", parse_mode: 'HTML');

           $this->start($bot);
            return;
        }

        // پایان مکالمه
        $this->end();
    }

    public function backToMenu(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            try {
                $bot->answerCallbackQuery();
            } catch (\Exception $e) {}
        }

        $user = User::where('telegram_id', $bot->userId())->first();
        if ($user) {
            BotMenuService::showMainMenu($bot, $user, isEdit: true);
        } else {
            BotMenuService::showGuestMenu($bot, $bot->user()->first_name ?? 'کاربر');
        }
    }

    public function logout(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            try {
                $bot->answerCallbackQuery();
            } catch (\Exception $e) {}
        }

        $user = User::where('telegram_id', $bot->userId())->first();

        if ($user) {
            $user->telegram_id = null;
            $user->save();

            if ($bot->isCallbackQuery()) {
                $bot->answerCallbackQuery(text: 'با موفقیت از حساب کاربری خارج شدید!', show_alert: true);
                try {
                    $bot->deleteMessage($bot->chatId(), $bot->messageId());
                } catch (\Exception $e) {}
            } else {
                $bot->sendMessage('✅ شما با موفقیت از حساب کاربری خود خارج شدید. 👋');
            }

            BotMenuService::showGuestMenu($bot, $bot->user()->first_name ?? 'کاربر');

        } else {
            if ($bot->isCallbackQuery()) {
                $bot->answerCallbackQuery(text: 'شما به هیچ حسابی متصل نیستید!', show_alert: true);
            } else {
                $bot->sendMessage('⚠️ شما به هیچ حسابی متصل نیستید!');
            }
        }
    }

    public function noAccount(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            try {
                $bot->answerCallbackQuery();
            } catch (\Exception $e) {}
        }
        $bot->sendMessage("برای تهیه اکانت یا اخذ نمایندگی، لطفاً با پشتیبانی در ارتباط باشید یا از طریق وب‌سایت اقدام کنید.");
    }
}
