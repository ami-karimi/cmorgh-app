<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\Group;
use App\Telegram\Services\BotMenuService;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use App\Services\AccountProvisioningService;
use Illuminate\Support\Facades\DB;

class CustomerOrderServiceConversation extends Conversation
{
    protected ?string $selectedType = null;
    protected ?int $selectedGroupId = null; // ذخیره آیدی پکیج انتخاب شده برای مرحله تایید

    /**
     * مرحله ۱: شروع و انتخاب نوع پروتکل
     */
    public function start(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            try { $bot->answerCallbackQuery(); } catch (\Exception $e) {}
        }

        $keyboardCancel = ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(KeyboardButton::make('❌ انصراف از خرید'));

        $text = "🛍 <b>سفارش سرویس جدید</b>\n\nلطفاً پروتکل یا نوع سرویس مورد نظر خود را انتخاب کنید:";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('🔒 WireGuard', callback_data: 'type:wireguard'),
                InlineKeyboardButton::make('🌐 L2TP / Cisco', callback_data: 'type:l2tp_cisco')
            )
            ->addRow(
                InlineKeyboardButton::make('🏠 انصراف و منوی اصلی', callback_data: 'cancel_order')
            );

        if ($bot->isCallbackQuery()) {
            $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
        } else {
            $bot->sendMessage("🛍 <b>سفارش سرویس جدید</b>", parse_mode: 'HTML', reply_markup: $keyboardCancel);
            $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        }

        $this->next('handleServiceTypeSelection');
    }

    /**
     * مرحله ۲: دریافت نوع پروتکل و ساخت لیست پکیج‌ها
     */
    public function handleServiceTypeSelection(Nutgram $bot)
    {
        if ($bot->message()?->text === '❌ انصراف از خرید' || $bot->message()?->text === '/start') {
            $this->cancel($bot);
            return;
        }

        if ($bot->isCallbackQuery()) {
            try { $bot->answerCallbackQuery(); } catch (\Exception $e) {}

            $data = $bot->callbackQuery()?->data;

            if ($data === 'cancel_order') {
                $this->cancel($bot);
                return;
            }

            if ($data && str_starts_with($data, 'type:')) {
                $this->selectedType = str_replace('type:', '', $data);

                // هدایت به متد کمکی برای نمایش پکیج‌ها
                $this->showPackagesList($bot);
            }
        }
    }

    /**
     * متد کمکی: واکشی و نمایش لیست پکیج‌ها بر اساس پروتکل
     */
    private function showPackagesList(Nutgram $bot)
    {
        $query = Group::where('is_enabled', true);

        if ($this->selectedType === 'wireguard') {
            $query->where(function($q) {
                $q->where('name', 'LIKE', '%wireguard%')
                    ->orWhere('name', 'LIKE', '%وایرگارد%');
            });
        } else {
            $query->where(function($q) {
                $q->where('name', 'NOT LIKE', '%wireguard%')
                    ->where('name', 'NOT LIKE', '%وایرگارد%');
            });
        }
        $user = User::where('telegram_id', $bot->userId())->first();
        $groups = $query->get();

        if ($groups->isEmpty()) {
            $emptyKeyboard = InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('🔙 بازگشت به انتخاب پروتکل', callback_data: 'back_to_protocols'))
                ->addRow(InlineKeyboardButton::make('🏠 انصراف و منوی اصلی', callback_data: 'cancel_order'));

            $bot->editMessageText("❌ <b>در حال حاضر هیچ پکیج فعالی برای این سرویس وجود ندارد.</b>", parse_mode: 'HTML', reply_markup: $emptyKeyboard);
            $this->next('handleGroupSelection');
            return;
        }

        $inlineKeyboard = InlineKeyboardMarkup::make();
        foreach ($groups as $group) {
            $price = number_format($group->getSellingPriceFor($user->parentAgent) ?? 0);
            $name = $group->name ?? 'سرویس';

            $inlineKeyboard->addRow(
                InlineKeyboardButton::make("📦 {$name} - {$price} تومان", callback_data: "buy_group:{$group->id}")
            );
        }

        $inlineKeyboard->addRow(
            InlineKeyboardButton::make('🔙 تغییر نوع سرویس (پروتکل)', callback_data: 'back_to_protocols')
        );
        $inlineKeyboard->addRow(
            InlineKeyboardButton::make('🏠 انصراف و منوی اصلی', callback_data: 'cancel_order')
        );

        $bot->editMessageText("📦 <b>لیست پکیج‌های فعال:</b>\n\nلطفاً پکیج مورد نظر خود را انتخاب کنید:", parse_mode: 'HTML', reply_markup: $inlineKeyboard);
        $this->next('handleGroupSelection');
    }

    /**
     * مرحله ۳: دریافت کلیک روی پکیج و هدایت به مرحله تایید
     */
    public function handleGroupSelection(Nutgram $bot)
    {
        if ($bot->message()?->text === '❌ انصراف از خرید' || $bot->message()?->text === '/start') {
            $this->cancel($bot);
            return;
        }

        if ($bot->isCallbackQuery()) {
            $data = $bot->callbackQuery()?->data;

            if ($data === 'back_to_protocols') {
                $this->start($bot);
                return;
            }

            if ($data === 'cancel_order') {
                $this->cancel($bot);
                return;
            }

            // کاربر روی پکیج کلیک کرده است -> هدایت به مرحله تایید
            if ($data && str_starts_with($data, 'buy_group:')) {
                $this->selectedGroupId = (int) str_replace('buy_group:', '', $data);
                $this->askConfirmation($bot);
                return;
            }
        }
    }

    /**
     * مرحله ۴: نمایش فاکتور و درخواست تایید از کاربر
     */
    public function askConfirmation(Nutgram $bot)
    {
        $group = Group::find($this->selectedGroupId);

        if (!$group || !$group->is_enabled) {
            $bot->sendMessage("❌ پکیج مورد نظر نامعتبر یا غیرفعال است!");
            $this->end();
            return;
        }

        $price = number_format($group->price ?? 0);
        $name = $group->name ?? 'سرویس';

        $text = "🛒 <b>تایید نهایی سفارش</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "📦 <b>پکیج انتخابی:</b> {$name}\n";
        $text .= "💵 <b>مبلغ پرداختی:</b> {$price} تومان\n\n";
        $text .= "آیا از کسر موجودی و صدور این سرویس اطمینان دارید؟";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('✅ بله، کسر از کیف پول و صدور', callback_data: 'confirm_buy')
            )
            ->addRow(
                InlineKeyboardButton::make('🔙 انصراف و بازگشت به لیست پکیج‌ها', callback_data: 'back_to_packages')
            );

        $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('handleConfirmation');
    }

    /**
     * مرحله ۵: دریافت تایید نهایی کاربر
     */
    public function handleConfirmation(Nutgram $bot)
    {
        if ($bot->message()?->text === '❌ انصراف از خرید' || $bot->message()?->text === '/start') {
            $this->cancel($bot);
            return;
        }

        if ($bot->isCallbackQuery()) {
            $data = $bot->callbackQuery()?->data;

            if ($data === 'back_to_packages') {
                $this->showPackagesList($bot);
                return;
            }

            // کاربر خرید را تایید کرد -> پردازش نهایی
            if ($data === 'confirm_buy') {
                $this->processPurchase($bot, $this->selectedGroupId);
                return;
            }
        }
    }

    /**
     * مرحله ۶: بررسی موجودی و ساخت نهایی سرویس در سرور
     */
    public function processPurchase(Nutgram $bot, $groupId)
    {
        if ($bot->isCallbackQuery()) {
            try { $bot->answerCallbackQuery(); } catch (\Exception $e) {}
        }

        $user = User::where('telegram_id', $bot->userId())->first();
        $group = Group::find($groupId);

        if (!$user || !$group || !$group->is_enabled) {
            $bot->sendMessage("❌ پکیج مورد نظر نامعتبر یا غیرفعال است!");
            $this->end();
            return;
        }

        $price = $group->getSellingPriceFor($user->parentAgent) ?? 0;

        if ($user->balance < $price) {
            $text = "❌ <b>موجودی کیف پول شما کافی نیست!</b>\n";
            $text .= "➖➖➖➖➖➖➖➖➖➖\n";
            $text .= "💰 <b>موجودی فعلی شما:</b> " . number_format($user->balance) . " تومان\n";
            $text .= "💵 <b>قیمت پکیج انتخابی:</b> " . number_format($price) . " تومان\n\n";
            $text .= "لطفاً برای تکمیل خرید، ابتدا حساب خود را شارژ کنید.";

            $keyboard = InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('🔙 بازگشت به لیست پکیج‌ها', callback_data: 'back_to_packages'),
                    InlineKeyboardButton::make('🏠 منوی اصلی', callback_data: 'cancel_order')
                );

            $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
            $this->next('handleConfirmation');
            return;
        }

        // ساخت اکانت
        try {
            $bot->editMessageText("⏳ <b>در حال کسر مبلغ از ولت و صدور اتوماتیک سرویس...</b>", parse_mode: 'HTML');

            DB::transaction(function () use ($user, $group, $price) {

                $accService = new AccountProvisioningService();
                $phone = $user->phone ?? '09' . rand(100000000, 999999999);

                $preparedData = $accService->prepareAccountData($group, $user, $phone);

                $create = $accService->createFullAccount(
                    $preparedData['userData'],
                    $preparedData['configData'],
                    $user->id,
                    true,
                    true
                );

                if (is_array($create) && isset($create['status']) && $create['status'] === false) {
                    throw new \Exception($create['message'] ?? 'خطا در ارتباط با سرور صدور اکانت.');
                }


            });

            $successKeyboard = InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('⚙️ مشاهده و مدیریت سرویس‌ها', callback_data: 'cust_services_list'))
                ->addRow(InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'cancel_order'));

            $bot->editMessageText("🎉 <b>خرید با موفقیت انجام شد!</b>\n\nسرویس شما صادر شد. جهت دریافت لینک اتصال به بخش مدیریت سرویس‌ها مراجعه کنید.", parse_mode: 'HTML', reply_markup: $successKeyboard);

        } catch (\Exception $e) {
            $failKeyboard = InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('🔙 بازگشت به لیست پکیج‌ها', callback_data: 'back_to_packages'));

            $bot->editMessageText("❌ <b>خطا در صدور سرویس:</b>\n" . $e->getMessage(), parse_mode: 'HTML', reply_markup: $failKeyboard);
        }

        $this->end();
    }

    private function cancel(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            try { $bot->answerCallbackQuery(); } catch (\Exception $e) {}
            $bot->sendMessage('❌ عملیات خرید لغو شد.');
        } else {
            $bot->sendMessage('❌ عملیات خرید لغو شد.');
        }

        $user = User::where('telegram_id', $bot->userId())->first();
        BotMenuService::showCustomerMenu($bot, $user, $bot->user()->first_name ?? 'کاربر');
        $this->end();
    }
}
