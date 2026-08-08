<?php

namespace App\Telegram\Services;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use App\Models\User;
use App\Models\Accounts;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class BotMenuService
{
    /**
     * نمایش هوشمند منوی اصلی بر اساس نقش کاربر (با قابلیت ارسال جدید یا ویرایش پیام قبلی)
     */
    public static function showMainMenu(Nutgram $bot, User $user, bool $isEdit = false): void
    {
        $firstName = $bot->user()->first_name;

        if (in_array($user->role, ['admin', 'manager'])) {
            self::showAdminMenu($bot, $firstName, $isEdit);
            return;
        }

        if (in_array($user->role, ['agent', 'subagent'])) {
            self::showAgentMenu($bot, $firstName, $isEdit);
            return;
        }

        if ($user->role === 'customer') {
            self::showCustomerMenu($bot, $user, $firstName, $isEdit);
            return;
        }
    }

    /**
     * ۱. منوی مدیریت
     */
    public static function showAdminMenu(Nutgram $bot, string $firstName, bool $isEdit = false): void
    {
        $text = "سلام <b>{$firstName}</b> عزیز (مدیر سیستم) 👑\nبه پنل مدیریت کل خوش آمدید:";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('🧾 رسیدهای در انتظار', callback_data: 'admin_receipts'),
                InlineKeyboardButton::make('🛒 سفارشات در انتظار', callback_data: 'admin_orders')
            )
            ->addRow(
                InlineKeyboardButton::make('🟢 آمار اکانت‌های آنلاین', callback_data: 'admin_online_count'),
                InlineKeyboardButton::make('➕ صدور اکانت جدید', callback_data: 'admin_create_acc')
            )
            ->addRow(
                InlineKeyboardButton::make('🔍 جستجو و مدیریت اکانت (شارژ/مسدود)', callback_data: 'admin_manage_acc')
            )
            ->addRow(
                InlineKeyboardButton::make('🚪 خروج از حساب', callback_data: 'logout_account')
            );

        self::sendOrEdit($bot, $text, $keyboard, $isEdit);
    }

    /**
     * ۲. منوی نمایندگان
     */
    public static function showAgentMenu(Nutgram $bot, string $firstName, bool $isEdit = false): void
    {
        $text = "سلام <b>{$firstName}</b> عزیز (نماینده) 💼\nبه پنل مدیریت نمایندگی خوش آمدید:";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('👥 لیست مشتریان', callback_data: 'agent_customers'),
                InlineKeyboardButton::make('🛒 سفارشات فروشگاه', callback_data: 'agent_orders')
            )
            ->addRow(
                InlineKeyboardButton::make('🔍 مدیریت اکانت (تمدید/مسدود/اطلاعات)', callback_data: 'agent_manage_acc'),
                InlineKeyboardButton::make('➕ ایجاد اکانت جدید', callback_data: 'agent_create_acc')
            )
            ->addRow(
                InlineKeyboardButton::make('💰 موجودی ولت', callback_data: 'agent_wallet')
            )
            ->addRow(
                InlineKeyboardButton::make('🚪 خروج از حساب', callback_data: 'logout_account')
            );

        self::sendOrEdit($bot, $text, $keyboard, $isEdit);
    }

    /**
     * ۳. منوی مشتریان
     */
    public static function showCustomerMenu(Nutgram $bot, User $user, string $firstName, bool $isEdit = false): void
    {
        $brandName = 'پشتیبانی سرویس';
        if ($user->creator) {
            $agent = User::find($user->creator);
            $agentStore = DB::table('agent_stores')->where('user_id', $agent?->id)->first();
            $brandName = $agentStore->title ?? $agent?->brand_name ?? $agent?->name ?? 'سامانه VPN';
        }

        $text = "سلام <b>{$firstName}</b> عزیز 🌹\nبه پنل کاربری <b>«{$brandName}»</b> خوش آمدید:";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('📋 لیست اکانت‌ها همراه با وضعیت', callback_data: 'cust_accounts'),
                InlineKeyboardButton::make('🛍 سفارش سرویس جدید', callback_data: 'cust_order')
            )
            ->addRow(
                InlineKeyboardButton::make('💳 موجودی ولت', callback_data: 'cust_wallet'),
                InlineKeyboardButton::make('➕ افزایش موجودی حساب', callback_data: 'cust_add_balance')
            )
            ->addRow(
                InlineKeyboardButton::make('🚪 خروج / تغییر حساب', callback_data: 'logout_account')
            );

        self::sendOrEdit($bot, $text, $keyboard, $isEdit);
    }

    /**
     * ۴. منوی مهمان (ورود / بدون حساب)
     */
    public static function showGuestMenu(Nutgram $bot, string $firstName): void
    {
        $text = "سلام <b>{$firstName}</b> عزیز! 👋\nبه ربات ما خوش آمدید.\n\nآیا از قبل حساب کاربری دارید؟";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('✅ بله، حساب دارم (ورود)', callback_data: 'start_login')
            )
            ->addRow(
                InlineKeyboardButton::make('❌ خیر، حساب ندارم', callback_data: 'no_account')
            );

        $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
    }

    /**
     * ۵. رندر کارت اطلاعات اکانت برای ادمین
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
                InlineKeyboardButton::make('🔍 جستجوی اکانت دیگر', callback_data: 'admin_manage_acc'),
                InlineKeyboardButton::make('🏠 منوی اصلی ادمین', callback_data: 'back_to_admin_menu')
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
            // اگر ویرایش پیام با خطا مواجه شد، پیام جدید ارسال می‌شود
            $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }
}
