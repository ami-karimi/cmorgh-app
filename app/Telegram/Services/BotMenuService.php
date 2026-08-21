<?php

namespace App\Telegram\Services;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use App\Models\User;
use App\Models\Accounts;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class BotMenuService
{
    /**
     * نمایش هوشمند منوی اصلی بر اساس نقش کاربر
     */
    public static function showMainMenu(Nutgram $bot, User $user, bool $isEdit = false): void
    {
        // اگر کاربر از دکمه شیشه‌ای (مثل بازگشت) استفاده کرده، پیام شیشه‌ای را پاک می‌کنیم
        if ($isEdit && $bot->isCallbackQuery()) {
            try {
                $bot->deleteMessage($bot->chatId(), $bot->messageId());
                $bot->answerCallbackQuery();
            } catch (\Exception $e) {}
        }

        $firstName = $bot->user()->first_name ?? 'کاربر';

        if (in_array($user->role, ['admin', 'manager'])) {
            self::showAdminMenu($bot, $firstName);
            return;
        }

        if (in_array($user->role, ['agent', 'subagent'])) {
            self::showAgentMenu($bot, $firstName);
            return;
        }

        if ($user->role === 'customer') {
            self::showCustomerMenu($bot, $user, $firstName);
            return;
        }
    }

    /**
     * ۱. منوی مدیریت (کیبورد ثابت)
     */
    public static function showAdminMenu(Nutgram $bot, string $firstName): void
    {
        $text = "سلام <b>{$firstName}</b> عزیز (مدیر سیستم) 👑\nبه پنل مدیریت کل خوش آمدید:\n\n👇 لطفاً از منوی پایین انتخاب کنید:";

        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(
                KeyboardButton::make('🟢 آمار اکانت‌های آنلاین'),
                KeyboardButton::make('🧾 بررسی فیش‌های واریزی')
            )
            ->addRow(
                KeyboardButton::make('🔍 جستجو و مدیریت اکانت'),
                KeyboardButton::make('➕ صدور اکانت جدید')
            )
            ->addRow(
                KeyboardButton::make('🛒 سفارشات در انتظار')
            )
            ->addRow(
                KeyboardButton::make('🚪 خروج از حساب کاربری')
            );

        $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
    }

    /**
     * ۲. منوی نمایندگان (کیبورد ثابت)
     */
    public static function showAgentMenu(Nutgram $bot, string $firstName): void
    {
        $text = "سلام <b>{$firstName}</b> عزیز (نماینده) 💼\nبه پنل مدیریت نمایندگی خوش آمدید:\n\n👇 لطفاً از منوی پایین انتخاب کنید:";

        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(
                KeyboardButton::make('🔍 مدیریت و جستجوی اکانت'),
                KeyboardButton::make('➕ ایجاد اکانت جدید')
            )
            ->addRow(
                KeyboardButton::make('👥 لیست مشتریان'),
                KeyboardButton::make('💰 موجودی ولت')
            )
            ->addRow(
                KeyboardButton::make('🛒 سفارشات فروشگاه')
            )
            ->addRow(
                KeyboardButton::make('🔑 دریافت کد دعوت'),
                KeyboardButton::make('🚪 خروج از حساب کاربری')
            );

        $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
    }




    /**
     * ۵. رندر کارت اطلاعات اکانت برای ادمین (دکمه شیشه‌ای)
     */
    public static function renderAccountCard(Nutgram $bot, Accounts $account, bool $isEdit = true): void
    {
        $toggleBtnText = $account->is_enabled ? "🔴 مسدودسازی اکانت" : "🟢 فعال‌سازی اکانت";
        $statusText = $account->is_enabled ? "🟢 فعال" : "🔴 مسدود / غیرفعال";
        $onlineText = $account->is_online ? "⚡ آنلاین" : "💤 آفلاین";

        $usageFormatted = method_exists($account, 'formatBytes') ? $account->formatBytes($account->usage) : round($account->usage / (1024*1024*1024), 2) . " GB";
        $maxUsageFormatted = $account->max_usage == 0 ? "نامحدود" : (method_exists($account, 'formatBytes') ? $account->formatBytes($account->max_usage) : round($account->max_usage / (1024*1024*1024), 2) . " GB");

        $expireText = "شروع نشده / بدون انقضا";
        if ($account->expire_date) {
            $jalaliDate = Jalalian::forge($account->expire_date)->format('Y/m/d - H:i');
            $daysLeft = (int) now()->diffInDays($account->expire_date, false);
            $expireText = $daysLeft > 0 ? "{$jalaliDate} ({$daysLeft} روز مانده)" : "{$jalaliDate} (منقضی شده)";
        }

        $text = "👤 <b>اطلاعات اکانت:</b> <code>{$account->username}</code>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "🔑 <b>رمز عبور:</b> <code>{$account->password}</code>\n";
        $text .= "📊 <b>وضعیت حساب:</b> {$statusText} | {$onlineText}\n";
        $text .= "📦 <b>سرویس:</b> <code>{$account->service_group}</code>\n";
        $text .= "👨‍💼 <b>نماینده/سازنده:</b> " . ($account->creatorUser?->name ?? 'سیستم') . "\n";
        $text .= "💾 <b>مصرف ترافیک:</b> {$usageFormatted} / {$maxUsageFormatted}\n";
        $text .= "📅 <b>تاریخ انقضا:</b> {$expireText}\n";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make($toggleBtnText, callback_data: "admin_toggle_acc:{$account->id}"),
                InlineKeyboardButton::make('🔄 شارژ مجدد', callback_data: "admin_recharge_acc:{$account->id}")
            )
            ->addRow(
            // دکمه بازگشت حالا فقط یه دستور کال‌بک ساده میده که تو روت هندل میشه
                InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu')
            );

        self::sendOrEdit($bot, $text, $keyboard, $isEdit);
    }

    /**
     * ۶. رندر کارت اطلاعات اکانت برای نماینده (دکمه شیشه‌ای)
     */
    public static function renderAccountCardAgent(Nutgram $bot, Accounts $account, bool $isEdit = true)
    {
        $statusText = $account->is_enabled ? "🟢 فعال" : "🔴 مسدود / غیرفعال";
        $onlineText = $account->is_online ? "⚡ آنلاین" : "💤 آفلاین";
        $usageFormatted = method_exists($account, 'formatBytes') ? $account->formatBytes($account->usage) : round($account->usage / (1024*1024*1024), 2) . " GB";
        $maxUsageFormatted = $account->max_usage == 0 ? "نامحدود" : (method_exists($account, 'formatBytes') ? $account->formatBytes($account->max_usage) : round($account->max_usage / (1024*1024*1024), 2) . " GB");

        $expireText = "شروع نشده / بدون انقضا";
        if ($account->expire_date) {
            $jalaliDate = Jalalian::forge($account->expire_date)->format('Y/m/d - H:i');
            $daysLeft = (int) now()->diffInDays($account->expire_date, false);
            $expireText = $daysLeft > 0 ? "{$jalaliDate} ({$daysLeft} روز مانده)" : "{$jalaliDate} (منقضی شده)";
        }

        $GroupText = $account->group ? $account->group->name : "بدون گروه کاربری";
        $easyAccessLink = $account->subscription_url;

        $text = "👤 <b>اطلاعات اکانت مشتری</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "👥 <b>گروه کاربری:</b> <code>{$GroupText}</code>\n";
        $text .= "📝 <b>نام‌کاربری:</b> <code>{$account->username}</code>\n";
        $text .= "🔑 <b>رمز عبور:</b> <code>{$account->password}</code>\n";
        $text .= "📊 <b>وضعیت:</b> {$statusText} | {$onlineText}\n";
        $text .= "📦 <b>سرویس:</b> {$account->service_group}\n";
        $text .= "💾 <b>مصرف ترافیک:</b> {$usageFormatted} / {$maxUsageFormatted}\n";
        $text .= "📅 <b>تاریخ انقضا:</b> {$expireText}\n\n";
        $text .= "🔗 <b>لینک دسترسی آسان کاربر (پنل کاربری):</b>\n{$easyAccessLink}";

        $toggleBtnText = $account->is_enabled ? "🔴 مسدودسازی" : "🟢 فعال‌سازی";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make($toggleBtnText, callback_data: "agent_toggle_acc:{$account->id}"),
                InlineKeyboardButton::make('🔄 تمدید اکانت', callback_data: "agent_renew_acc:{$account->id}")
            );

        if ($account->service_group === 'wireguard') {
            $keyboard->addRow(
                InlineKeyboardButton::make('📥 فایل کانفیگ', callback_data: "dl_wg_conf:{$account->id}"),
                InlineKeyboardButton::make('📱 کد QR', callback_data: "dl_wg_qr:{$account->id}")
            );
        }

        $keyboard->addRow(
            InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu')
        );

        self::sendOrEdit($bot, $text, $keyboard, $isEdit);
    }

    /**
     * متد عمومی کمکی جهت ارسال یا ویرایش پیام بدون ارور
     */
    public static function sendOrEdit(Nutgram $bot, string $text, InlineKeyboardMarkup $keyboard, bool $isEdit = false): void
    {
        try {
            if ($isEdit) {
                $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
            } else {
                $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
            }
        } catch (\Exception $e) {
            $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }


    /**
     * ۴. منوی مهمان (ورود / ثبت‌نام) کاملاً متنی
     */
    public static function showGuestMenu(Nutgram $bot, string $firstName): void
    {
        $text = "سلام <b>{$firstName}</b> عزیز! 👋\nبه ربات ما خوش آمدید.\n\nلطفاً یکی از گزینه‌های زیر را انتخاب کنید:";

        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(
                KeyboardButton::make('🔐 ورود به حساب کاربری')
            )
            ->addRow(
                KeyboardButton::make('📝 ساخت حساب کاربری')
            );

        $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
    }

    /**
     * ۳. منوی مشتریان (مخصوص role customer)
     */
    public static function showCustomerMenu(Nutgram $bot, User $user, string $firstName): void
    {
        $text = "سلام <b>{$firstName}</b> عزیز 🌹\nبه پنل کاربری خود خوش آمدید:\n\n👇 لطفاً از منوی پایین انتخاب کنید:";

        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(
                KeyboardButton::make('⚙️ مدیریت سرویس ها'),
                KeyboardButton::make('🎁 دریافت اشتراک رایگان')
            )
            ->addRow(
                KeyboardButton::make('💰 افزایش موجودی'),
                KeyboardButton::make('📞 ارتباط با پشتیبان')
            )
            ->addRow(
                KeyboardButton::make('🌐 ورود به پنل کاربری'),
                KeyboardButton::make('🛍 سفارش سرویس جدید')
            )
            ->addRow(
                KeyboardButton::make('🚪 خروج از حساب کاربری')
            );

        $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
    }


    /**
     * رندر کارت اطلاعات اکانت برای مشتری (Customer)
     */
    public static function renderCustomerAccountCard(Nutgram $bot, Accounts $account, bool $isEdit = false): void
    {
        $statusText = $account->is_enabled ? "🟢 فعال" : "🔴 مسدود / غیرفعال";
        $onlineText = $account->is_online ? "⚡ آنلاین" : "💤 آفلاین";

        $usageFormatted = method_exists($account, 'formatBytes') ? $account->formatBytes($account->usage) : round($account->usage / (1024*1024*1024), 2) . " GB";
        $maxUsageFormatted = $account->max_usage == 0 ? "نامحدود" : (method_exists($account, 'formatBytes') ? $account->formatBytes($account->max_usage) : round($account->max_usage / (1024*1024*1024), 2) . " GB");

        $expireText = "شروع نشده / بدون انقضا";
        if ($account->expire_date) {
            $jalaliDate = \Morilog\Jalali\Jalalian::forge($account->expire_date)->format('Y/m/d - H:i');
            $daysLeft = (int) now()->diffInDays($account->expire_date, false);
            $expireText = $daysLeft > 0 ? "{$jalaliDate} ({$daysLeft} روز مانده)" : "{$jalaliDate} (منقضی شده)";
        }

        $easyAccessLink = $account->subscription_url ?? '';

        $text = "🌐 <b>اطلاعات سرویس شما</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "📝 <b>نام‌کاربری:</b> <code>{$account->username}</code>\n";
        $text .= "🔑 <b>رمز عبور:</b> <code>{$account->password}</code>\n";
        $text .= "📊 <b>وضعیت:</b> {$statusText} | {$onlineText}\n";
        $text .= "📦 <b>سرویس:</b> {$account->service_group}\n";
        $text .= "💾 <b>مصرف ترافیک:</b> {$usageFormatted} / {$maxUsageFormatted}\n";
        $text .= "📅 <b>تاریخ انقضا:</b> {$expireText}\n\n";

        if ($easyAccessLink) {
            $text .= "🔗 <b>لینک اتصال هوشمند / سابسکریپشن:</b>\n<code>{$easyAccessLink}</code>";
        }

        $keyboard = \SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup::make();

        // دکمه تمدید سرویس
        $keyboard->addRow(
            \SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton::make('🔄 تمدید سرویس', callback_data: "cust_renew_acc:{$account->id}")
        );

        // دکمه‌های کانفیگ و QR کد برای سرویس‌های وایرگارد
        if ($account->service_group === 'wireguard') {
            $keyboard->addRow(
                \SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton::make('📥 فایل کانفیگ', callback_data: "dl_wg_conf:{$account->id}"),
                \SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton::make('📱 کد QR', callback_data: "dl_wg_qr:{$account->id}")
            );
        }

        // 🔴 دکمه‌های بازگشت
        $keyboard->addRow(
            \SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton::make('🔙 بازگشت به لیست سرویس‌ها', callback_data: 'cust_services_list'),
            \SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton::make('🏠 منوی اصلی', callback_data: 'back_to_admin_menu')
        );

        self::sendOrEdit($bot, $text, $keyboard, $isEdit);
    }

}
