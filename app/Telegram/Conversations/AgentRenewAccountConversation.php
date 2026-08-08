<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use App\Models\Accounts;
use App\Models\User;
use App\Models\Group;
use App\Models\Financial;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Illuminate\Support\Facades\DB;

class AgentRenewAccountConversation extends Conversation
{
    public $accountId;
    public $groupId;

    /**
     * مرحله ۱: دریافت آیدی اکانت و نمایش پکیج‌های مجاز
     */
    public function start(Nutgram $bot, $id)
    {
        $this->accountId = $id;
        $agent = User::where('telegram_id', $bot->userId())->first();
        $account = Accounts::find($this->accountId);

        // بررسی دسترسی
        if (!$this->hasAccess($agent, $account)) {
            $bot->answerCallbackQuery(text: '❌ شما به این اکانت دسترسی ندارید.', show_alert: true);
            $this->end();
            return;
        }

        $bot->answerCallbackQuery();

        // واکشی گروه‌ها بر اساس نوع سرویس اکانت (مثلا فقط وایرگاردها رو بیاره اگه اکانت وایرگارده)
        $query = Group::where('is_enabled', 1);
        if ($account->service_group === 'wireguard') {
            $query->where('name', 'like', '%وایرگارد%');
        } else {
            $query->where('name', 'not like', '%وایرگارد%');
        }
        $groups = $query->get();

        if ($groups->isEmpty()) {
            $bot->sendMessage("⚠️ هیچ پکیجی برای تمدید این سرویس یافت نشد.");
            $this->end();
            return;
        }

        $keyboard = InlineKeyboardMarkup::make();
        foreach ($groups as $group) {
            // محاسبه قیمت اختصاصی این نماینده
            $finalPrice = method_exists($group, 'getFinalPriceFor') ? $group->getFinalPriceFor($agent) : $group->price;

            // اگر گروه فعلی کاربر است، یک علامت کنارش بگذاریم
            $isCurrent = ($account->group_id == $group->id) ? '📌 ' : '';

            $keyboard->addRow(
                InlineKeyboardButton::make($isCurrent . "{$group->name} (" . number_format($finalPrice) . "ت)", callback_data: "renew_grp_{$group->id}")
            );
        }

        $keyboard->addRow(InlineKeyboardButton::make('❌ لغو عملیات', callback_data: 'cancel_renew'));

        $bot->sendMessage("🔄 <b>تمدید اکانت:</b> <code>{$account->username}</code>\n\nلطفاً پکیج جدید را جهت تمدید انتخاب کنید:\n(پکیج فعلی با 📌 مشخص شده است)", parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('showConfirmation');
    }

    /**
     * مرحله ۲: نمایش پیش‌فاکتور تمدید
     */
    public function showConfirmation(Nutgram $bot)
    {
        if (!$bot->isCallbackQuery()) return;
        $data = $bot->callbackQuery()->data;

        if ($data === 'cancel_renew') {
            $bot->editMessageText("❌ عملیات تمدید لغو شد.");
            $this->end();
            return;
        }

        $this->groupId = str_replace('renew_grp_', '', $data);
        $bot->answerCallbackQuery();

        $agent = User::where('telegram_id', $bot->userId())->first();
        $account = Accounts::find($this->accountId);
        $group = Group::find($this->groupId);

        $finalPrice = method_exists($group, 'getFinalPriceFor') ? $group->getFinalPriceFor($agent) : $group->price;

        $text = "🧾 <b>پیش‌فاکتور تمدید اکانت</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "👤 <b>اکانت:</b> <code>{$account->username}</code>\n";
        $text .= "📦 <b>پکیج انتخابی:</b> {$group->name}\n";
        $text .= "💰 <b>هزینه کسر از ولت:</b> " . number_format($finalPrice) . " تومان\n";
        $text .= "💳 <b>موجودی فعلی شما:</b> " . number_format($agent->balance) . " تومان\n\n";

        if ($agent->balance < $finalPrice) {
            $text .= "⚠️ <b>موجودی شما کافی نیست!</b> لطفاً ابتدا کیف پول خود را شارژ کنید.";
            $keyboard = InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('💰 شارژ کیف پول', callback_data: 'agent_wallet'))
                ->addRow(InlineKeyboardButton::make('❌ لغو', callback_data: 'cancel_renew'));
        } else {
            $text .= "⚠️ <i>با تایید این بخش، هزینه از ولت شما کسر شده و ترافیک اکانت صفر می‌شود.</i>";
            $keyboard = InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('✅ تایید و کسر از ولت', callback_data: 'confirm_renew'))
                ->addRow(InlineKeyboardButton::make('🔙 بازگشت به انتخاب پکیج', callback_data: 'back_to_groups'));
        }

        $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('executeRenewal');
    }

    /**
     * مرحله ۳: کسر موجودی و اعمال تغییرات روی اکانت
     */
    /**
     * مرحله ۳: کسر موجودی و اعمال تغییرات روی اکانت (با استفاده از سرویس اصلی سایت)
     */
    public function executeRenewal(Nutgram $bot)
    {
        if (!$bot->isCallbackQuery()) return;
        $data = $bot->callbackQuery()->data;

        if ($data === 'cancel_renew') {
            $bot->editMessageText("❌ عملیات تمدید لغو شد.");
            $this->end();
            return;
        }

        if ($data === 'back_to_groups') {
            $bot->answerCallbackQuery();
            // برگشت به مرحله انتخاب گروه
            $this->start($bot, $this->accountId);
            return;
        }

        if ($data === 'confirm_renew') {
            $bot->answerCallbackQuery();
            $bot->editMessageText("⏳ در حال ارتباط با سرور و تمدید اکانت...");

            $agent = User::where('telegram_id', $bot->userId())->first();
            $account = Accounts::find($this->accountId);
            $group = Group::find($this->groupId);

            $result = \App\Services\VpnManagerService::rechargeAccount(
                $account,
                $group,
                true, // payFromAgentWallet = true (کسر از ولت نماینده)
                $agent->id, // executedByUserId = آیدی ادمین/نماینده فعلی
                false // payFromUserWallet = false
            );

            // اگر عملیات موفقیت‌آمیز بود
            if ($result['status'] === true) {

                $account->refresh();

                $expireDateText = "پس از اولین اتصال";
                if ($account->expire_date) {
                    $expireDateText = \Morilog\Jalali\Jalalian::forge($account->expire_date)->format('Y/m/d - H:i');
                }

                $successMsg = "🎉 <b>اکانت با موفقیت تمدید شد!</b>\n";
                $successMsg .= "➖➖➖➖➖➖➖➖➖➖\n";
                $successMsg .= "👤 <b>اکانت:</b> <code>{$account->username}</code>\n";
                $successMsg .= "📦 <b>پکیج اعمال شده:</b> {$group->name}\n";
                $successMsg .= "📅 <b>انقضای جدید:</b> {$expireDateText}\n\n";
                if (!empty($result['message'])) {
                    $successMsg .= "💡 <i>{$result['message']}</i>";
                }

                $keyboard = InlineKeyboardMarkup::make()
                    ->addRow(InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu'));

                $bot->sendMessage($successMsg, parse_mode: 'HTML', reply_markup: $keyboard);

            } else {
                // اگر عملیات ناموفق بود (مثلاً موجودی کافی نبود یا خطای میکروتیک)
                $keyboard = InlineKeyboardMarkup::make()
                    ->addRow(InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu'));

                $bot->sendMessage("❌ <b>خطا در تمدید:</b>\n" . ($result['message'] ?? 'خطای ناشناخته در ارتباط با سرور'), parse_mode: 'HTML', reply_markup: $keyboard);
            }

            $this->end();
        }
    }

    /**
     * متد کمکی برای بررسی دسترسی
     */
    private function hasAccess(?User $agent, ?Accounts $account): bool
    {
        if (!$agent || !$account) return false;
        if ($account->creatorUser && $account->creatorUser->creator == $agent->id) {
            return true;
        }
        return false;
    }
}
