<?php
namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\Accounts;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use App\Telegram\Services\BotMenuService;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class AdminAccountActionHandler
{
    public function onlineCount(Nutgram $bot)
    {
        // ۱. بستن لودینگ اولیه (فقط برای دکمه شیشه‌ای)
        if ($bot->isCallbackQuery()) {
            try {
                $bot->answerCallbackQuery();
            } catch (\Exception $e) {}
        }

        $user = \App\Models\User::where('telegram_id', $bot->userId())->first();

        // ۲. بررسی دسترسی با پاسخ هوشمند
        if (!$user || !in_array($user->role, ['admin', 'manager'])) {
            if ($bot->isCallbackQuery()) {
                $bot->answerCallbackQuery(text: '⛔ شما دسترسی به این بخش را ندارید.', show_alert: true);
            } else {
                $bot->sendMessage('⛔ شما دسترسی به این بخش را ندارید.');
            }
            return;
        }

        $totalAccounts = \App\Models\Accounts::count();
        $onlineAccounts = \Illuminate\Support\Facades\DB::table('radacct')->whereNull('acctstoptime')->count();
        $offlineAccounts = $totalAccounts - $onlineAccounts;
        $time = date('H:i:s');

        $text = "📊 <b>آمار لحظه‌ای اکانت‌های سیستم</b>\n➖➖➖➖➖➖➖➖➖➖\n\n";
        $text .= "👥 <b>کل اکانت‌ها:</b> {$totalAccounts} کاربر\n";
        $text .= "🟢 <b>آنلاین‌ها:</b> {$onlineAccounts} نفر\n";
        $text .= "🔴 <b>آفلاین‌ها:</b> {$offlineAccounts} نفر\n\n";
        $text .= "⏱ <i>آخرین بروزرسانی: {$time}</i>";

        $keyboard = \SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup::make()
            ->addRow(\SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton::make('🔄 بروزرسانی آمار', callback_data: 'admin_online_count'))
            // دکمه بازگشت در این حالت هم مفیده
            ->addRow(\SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu'));

        // ۳. ارسال یا ویرایش هوشمند
        try {
            if ($bot->isCallbackQuery()) {
                $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
                // نمایش پاپ‌آپ کوچیک که بروز شد
                try { $bot->answerCallbackQuery(text: '✅ آمار با موفقیت بروز شد.'); } catch (\Exception $e) {}
            } else {
                // اگر از کیبورد پایینی اومده، پیام جدید بفرست
                $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
            }
        } catch (\Exception $e) {
            // این ارور زمانی رخ میده که آمار تغییری نکرده باشه و تلگرام اجازه ویرایش تکراری نده
            if ($bot->isCallbackQuery()) {
                try {
                    $bot->answerCallbackQuery(text: '⚠️ آمار تغییری نکرده است!');
                } catch (\Exception $e) {}
            }
        }
    }

    public function showRechargeGroups(Nutgram $bot, $id)
    {
        $account = Accounts::find($id);
        if (!$account) {
            $bot->answerCallbackQuery(text: '❌ اکانت یافت نشد!', show_alert: true);
            return;
        }

        $groups = Group::where('is_enabled', 1)->get();
        $keyboard = InlineKeyboardMarkup::make();

        foreach ($groups as $group) {
            $keyboard->addRow(InlineKeyboardButton::make("📦 {$group->name} (" . number_format($group->price) . " تومان)", callback_data: "admin_do_recharge:{$account->id}:{$group->id}"));
        }
        $keyboard->addRow(InlineKeyboardButton::make('🔙 انصراف و بازگشت', callback_data: 'admin_manage_acc'));

        $bot->answerCallbackQuery();
        $bot->editMessageText("🔄 <b>شارژ مجدد اکانت:</b> <code>{$account->username}</code>\n\nلطفاً پکیج مورد نظر را انتخاب کنید:", parse_mode: 'HTML', reply_markup: $keyboard);
    }

    public function doRecharge(Nutgram $bot, $acc_id, $grp_id)
    {
        $admin = User::where('telegram_id', $bot->userId())->first();
        $account = Accounts::find($acc_id);
        $group = Group::find($grp_id);

        if (!$account || !$group || !$admin || !in_array($admin->role, ['admin', 'manager'])) return;

        $result = \App\Services\VpnManagerService::rechargeAccount($account, $group, true, $admin->id, false);

        if (!$result['status']) {
            $bot->answerCallbackQuery(text: '❌ خطا: ' . $result['message'], show_alert: true);
            return;
        }

        $account->refresh();
        $bot->answerCallbackQuery(text: '✅ ' . $result['message'], show_alert: true);
        BotMenuService::renderAccountCard($bot, $account, isEdit: true);
    }

    public function toggleStatus(Nutgram $bot, $id)
    {
        $account = Accounts::find($id);
        if (!$account) return;

        $success = \App\Services\VpnManagerService::toggleAccount($account);
        if (!$success) {
            $bot->answerCallbackQuery(text: '❌ خطا در ارتباط با سرور!', show_alert: true);
            return;
        }

        $account->refresh();
        $bot->answerCallbackQuery(text: $account->is_enabled ? "🟢 وضعیت اکانت فعال شد." : "🔴 وضعیت اکانت مسدود شد.", show_alert: true);
        BotMenuService::renderAccountCard($bot, $account, isEdit: true);
    }
}
