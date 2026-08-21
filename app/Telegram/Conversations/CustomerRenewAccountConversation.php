<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\Accounts;
use App\Services\VpnManagerService;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class CustomerRenewAccountConversation extends Conversation
{
    public ?int $accountId = null;

    /**
     * مرحله ۱: دریافت آیدی اکانت، نمایش فاکتور و درخواست تایید از کاربر
     */
    public function start(Nutgram $bot, $id)
    {
        if ($bot->isCallbackQuery()) {
            try { $bot->answerCallbackQuery(); } catch (\Exception $e) {}
        }

        $this->accountId = (int) $id;

        $account = Accounts::find($this->accountId);
        $user = User::where('telegram_id', $bot->userId())->first();

        if (!$account || !$user) {
            $bot->sendMessage("❌ اکانت یافت نشد یا شما دسترسی ندارید.");
            $this->end();
            return;
        }

        $group = $account->group; // پکیجی که این اکانت روی آن قرار دارد

        if (!$group) {
            $bot->sendMessage("❌ پکیج متصل به این اکانت در سیستم یافت نشد.");
            $this->end();
            return;
        }

        $price = $group->getSellingPriceFor($user->parentAgent)?? 0;
        $groupName = $group->name ?? 'سرویس';

        $text = "🔄 <b>تایید تمدید سرویس</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "👤 <b>نام‌کاربری اکانت:</b> <code>{$account->username}</code>\n";
        $text .= "📦 <b>پکیج اعمال شونده:</b> {$groupName}\n";
        $text .= "💵 <b>مبلغ تمدید:</b> " . number_format($price) . " تومان\n";
        $text .= "💰 <b>موجودی فعلی شما:</b> " . number_format($user->balance) . " تومان\n\n";
        $text .= "آیا از تمدید این سرویس و کسر مبلغ از کیف پول خود اطمینان دارید؟";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('✅ بله، کسر از ولت و تمدید', callback_data: 'confirm_renew')
            )
            ->addRow(
                InlineKeyboardButton::make('🔙 انصراف و بازگشت', callback_data: "cust_show_service:{$account->id}")
            );

        if ($bot->isCallbackQuery()) {
            $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
        } else {
            $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        }

        $this->next('handleConfirmation');
    }

    /**
     * مرحله ۲: فراخوانی متد VpnManagerService::rechargeAccount و نمایش پاسخ
     */
    public function handleConfirmation(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            try { $bot->answerCallbackQuery(); } catch (\Exception $e) {}

            $data = $bot->callbackQuery()->data;

            // اگر کاربر انصراف داد
            if (str_starts_with($data, 'cust_show_service:')) {
                $this->end();
                return;
            }

            // اگر کاربر تمدید را تایید کرد
            if ($data === 'confirm_renew') {
                $account = Accounts::find($this->accountId);
                $user = User::where('telegram_id', $bot->userId())->first();

                if (!$account || !$user) {
                    $this->end();
                    return;
                }

                $group = $account->group;

                if (!$group) {
                    $bot->sendMessage("❌ پکیج متصل به این اکانت یافت نشد.");
                    $this->end();
                    return;
                }

                try {
                    $bot->editMessageText("⏳ <b>در حال ارتباط با سرور و تمدید اکانت...</b>", parse_mode: 'HTML');

                    // 🔴 فراخوانی متد اصلی سیستم شما جهت تمدید
                    $result = VpnManagerService::rechargeAccount(
                        $account,
                        $group,
                        true,      // payFromAgentWallet = false (کسر از نماینده نیست)
                        $user->id,  // executedByUserId = آیدی کاربر خریدار
                        true        // payFromUserWallet = true (کسر از ولت خود کاربر)
                    );

                    // بررسی خروجی سرویس
                    if (isset($result['status']) && $result['status'] === true) {
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
                            ->addRow(InlineKeyboardButton::make('🔙 مشاهده وضعیت سرویس', callback_data: "cust_show_service:{$account->id}"))
                            ->addRow(InlineKeyboardButton::make('🏠 منوی اصلی', callback_data: 'back_to_admin_menu'));

                        $bot->editMessageText($successMsg, parse_mode: 'HTML', reply_markup: $keyboard);

                    } else {
                        // در صورت بروز خطا (مثلاً عدم موجودی، خطای ارتباط با سرور و...)
                        $failMsg = "❌ <b>خطا در تمدید سرویس:</b>\n" . ($result['message'] ?? 'خطای ناشناخته در ارتباط با سرور');

                        $keyboard = InlineKeyboardMarkup::make()
                            ->addRow(InlineKeyboardButton::make('💰 افزایش موجودی', callback_data: 'start_deposit_flow'))
                            ->addRow(InlineKeyboardButton::make('🔙 بازگشت به سرویس', callback_data: "cust_show_service:{$account->id}"));

                        $bot->editMessageText($failMsg, parse_mode: 'HTML', reply_markup: $keyboard);
                    }

                } catch (\Exception $e) {
                    $failKeyboard = InlineKeyboardMarkup::make()
                        ->addRow(InlineKeyboardButton::make('🔙 بازگشت به سرویس', callback_data: "cust_show_service:{$account->id}"));

                    $bot->editMessageText("❌ <b>خطا در پردازش تمدید:</b>\n" . $e->getMessage(), parse_mode: 'HTML', reply_markup: $failKeyboard);
                }

                $this->end();
            }
        }
    }
}
