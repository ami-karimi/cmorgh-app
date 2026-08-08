<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class LoginConversation extends Conversation
{
    public ?string $loginInput = null;

    /**
     * شروع مکالمه: دریافت ایمیل / نام‌کاربری
     */
    public function start(Nutgram $bot)
    {
        $bot->sendMessage("📧 لطفاً **ایمیل** (یا نام کاربری) خود را وارد کنید:");
        $this->next('collectPassword');
    }

    /**
     * مرحله اول: ذخیره ایمیل و دریافت رمز عبور
     */
    public function collectPassword(Nutgram $bot)
    {
        $input = trim($bot->message()?->text ?? '');

        if (empty($input)) {
            $bot->sendMessage("⚠️ ورود این فیلد الزامی است. لطفاً مجدداً ایمیل/نام‌کاربری خود را وارد کنید:");
            return;
        }

        $this->loginInput = $input;
        $bot->sendMessage("🔑 لطفاً **رمز عبور** خود را وارد کنید:");
        $this->next('authenticate');
    }

    /**
     * مرحله دوم: بررسی در جدول users و هدایت بر اساس role
     */
    public function authenticate(Nutgram $bot)
    {
        $password = trim($bot->message()?->text ?? '');
        $telegramId = $bot->userId();

        $user = User::where('email', $this->loginInput)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            // اتصال آیدی تلگرام به کاربر در دیتابیس
            $user->telegram_id = $telegramId;
            $user->save();

            $bot->sendMessage("✅ **احراز هویت با موفقیت انجام شد!**\n\nحساب کاربری شما متصل گردید.");

            // نمایش منوی اختصاصی بر اساس Role کاربر
            $this->renderMenuByRole($bot, $user);
            $this->end();
            return;
        }

        // در صورت اشتباه بودن اطلاعات
        $bot->sendMessage("❌ **ایمیل یا رمز عبور اشتباه است.**\n\nلطفاً مجدداً **ایمیل (یا نام کاربری)** خود را وارد کنید:");
        $this->next('collectPassword');
    }

    /**
     * نمایش فرم/منوی متناسب با نقش کاربر
     */
    private function renderMenuByRole(Nutgram $bot, User $user)
    {
        $firstName = $bot->user()->first_name;

        // ۱. منوی مدیران کل (admin / manager)
        if (in_array($user->role, ['admin', 'manager'])) {
            $text = "👑 **پنل مدیریت ارشد**\nسلام {$firstName} عزیز، خوش آمدید. دسترسی‌های شما:";
            $keyboard = InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('🧾 رسیدهای در انتظار', callback_data: 'admin_receipts'),
                    InlineKeyboardButton::make('🛒 سفارشات در انتظار', callback_data: 'admin_orders')
                )
                ->addRow(
                    InlineKeyboardButton::make('🟢 آمار آنلاین‌ها', callback_data: 'admin_online_count'),
                    InlineKeyboardButton::make('➕ صدور اکانت جدید', callback_data: 'admin_create_acc')
                )
                ->addRow(
                    InlineKeyboardButton::make('🔍 جستجو و مدیریت اکانت (شارژ/مسدود)', callback_data: 'admin_manage_acc')
                )
                ->addRow(
                    InlineKeyboardButton::make('🚪 خروج از حساب', callback_data: 'logout_account')
                );

            $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
            return;
        }

        // ۲. منوی نمایندگان (agent / subagent)
        if (in_array($user->role, ['agent', 'subagent'])) {
            $text = "💼 **پنل مدیریت نمایندگی**\nسلام {$firstName} عزیز، خوش آمدید. دسترسی‌های شما:";
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

            $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
            return;
        }

        // ۳. منوی مشتریان نهایی (customer)
        if ($user->role === 'customer') {
            // استخراج برندینگ نماینده بالادستی (creator)
            $brandName = 'پشتیبانی سرویس';
            if ($user->creator) {
                $agent =  $user->creatorUser->parentAgent;

                $agentStore = DB::table('agent_stores')->where('user_id', $agent?->id)->first();
                $brandName = $agent->brand_name ?? 'سامانه VPN';
            }

            $text = "🌹 **پنل مشترکین «{$brandName}»**\nسلام {$firstName} عزیز، لطفا یکی از گزینه‌ها را انتخاب کنید:";
            $keyboard = InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('📋 لیست اکانت‌ها (حجم و انقضا)', callback_data: 'cust_accounts'),
                    InlineKeyboardButton::make('🛍 سفارش سرویس جدید', callback_data: 'cust_order')
                )
                ->addRow(
                    InlineKeyboardButton::make('💳 موجودی ولت', callback_data: 'cust_wallet'),
                    InlineKeyboardButton::make('➕ افزایش موجودی', callback_data: 'cust_add_balance')
                )
                ->addRow(
                    InlineKeyboardButton::make('🚪 خروج / تغییر حساب', callback_data: 'logout_account')
                );

            $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
            return;
        }
    }
}
