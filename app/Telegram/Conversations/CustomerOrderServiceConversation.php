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

    public function start(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            try { $bot->answerCallbackQuery(); } catch (\Exception $e) {}
        }

        $keyboardCancel = ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(KeyboardButton::make('❌ انصراف از خرید'));

        $bot->sendMessage("🛍 <b>سفارش سرویس جدید</b>", parse_mode: 'HTML', reply_markup: $keyboardCancel);

        // مرحله ۱: انتخاب نوع سرویس توسط کاربر
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('🔒 WireGuard', callback_data: 'type:wireguard'),
                InlineKeyboardButton::make('🌐 L2TP / Cisco', callback_data: 'type:l2tp_cisco')
            );

        $bot->sendMessage("لطفاً پروتکل یا نوع سرویس مورد نظر خود را انتخاب کنید:", parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('handleServiceTypeSelection');
    }

    /**
     * مرحله ۲: دریافت نوع پروتکل و فیلتر گروه‌های فعال متناسب با آن
     */
    public function handleServiceTypeSelection(Nutgram $bot)
    {
        $text = $bot->message()?->text;

        if ($text === '❌ انصراف از خرید' || $text === '/start') {
            $this->cancel($bot);
            return;
        }

        if ($bot->isMessage()) {
            $bot->sendMessage("⚠️ لطفاً یکی از دکمه‌های شیشه‌ای نوع سرویس را انتخاب کنید.");
            return;
        }

        if ($bot->isCallbackQuery()) {
            try { $bot->answerCallbackQuery(); } catch (\Exception $e) {}

            $data = $bot->callbackQuery()->data;
            if (str_starts_with($data, 'type:')) {
                $this->selectedType = str_replace('type:', '', $data);

                // پایه‌ریزی کوئری برای مدل Group
                $query = Group::where('is_enabled', true);

                // فیلتر کردن بر اساس نام یا نوع سرویس انتخاب شده
                if ($this->selectedType === 'wireguard') {
                    $query->where(function($q) {
                        $q->where('name', 'LIKE', '%wireguard%')
                            ->orWhere('name', 'LIKE', '%وایرگارد%');
                    });
                } else {
                    $query->where(function($q) {
                        $q->where('name', 'LIKE', '%l2tp%')
                            ->orWhere('name', 'LIKE', '%cisco%')
                            ->orWhere('name', 'LIKE', '%سیسکو%');
                    });
                }

                $groups = $query->get();

                if ($groups->isEmpty()) {
                    $bot->sendMessage("❌ در حال حاضر هیچ پکیج فعالی برای این پروتکل وجود ندارد.");
                    $this->cancel($bot);
                    return;
                }

                $inlineKeyboard = InlineKeyboardMarkup::make();
                foreach ($groups as $group) {
                    $price = number_format($group->price ?? 0);
                    $name = $group->name ?? 'سرویس';

                    $inlineKeyboard->addRow(
                        InlineKeyboardButton::make("📦 {$name} - {$price} تومان", callback_data: "buy_group:{$group->id}")
                    );
                }

                $bot->sendMessage("📦 <b>لیست پکیج‌های فعال ($this->selectedType):</b>\n\nلطفاً پکیج مورد نظر خود را انتخاب کنید:", parse_mode: 'HTML', reply_markup: $inlineKeyboard);
                $this->next('handleGroupSelection');
            }
        }
    }

    /**
     * مرحله ۳: انتظار برای انتخاب پکیج
     */
    public function handleGroupSelection(Nutgram $bot)
    {
        $text = $bot->message()?->text;

        if ($text === '❌ انصراف از خرید' || $text === '/start') {
            $this->cancel($bot);
            return;
        }

        if ($bot->isMessage()) {
            $bot->sendMessage("⚠️ لطفاً یکی از پکیج‌های لیست را انتخاب کنید.");
            return;
        }
    }

    /**
     * مرحله ۴: پردازش خرید نهایی
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

        $price = $group->price ?? 0;

        if ($user->balance < $price) {
            $bot->sendMessage("❌ <b>موجودی کیف پول شما کافی نیست!</b>\n\n💰 موجودی فعلی: " . number_format($user->balance) . " تومان\n💵 مبلغ پکیج: " . number_format($price) . " تومان\n\nلطفاً ابتدا حساب خود را شارژ کنید.", parse_mode: 'HTML');
            $this->cancel($bot);
            return;
        }

        try {
            $bot->sendMessage("⏳ در حال کسر مبلغ از ولت و صدور اتوماتیک سرویس...");

            DB::transaction(function () use ($user, $group, $price) {
                $user->decrement('balance', $price);

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

                if (method_exists($user, 'vpnAccounts')) {
                    $user->vpnAccounts()->attach($create->id);
                }
            });

            $bot->sendMessage("🎉 <b>خرید با موفقیت انجام شد!</b>\n\nسرویس شما صادر شد. جهت مشاهده جزئیات به بخش «⚙️ مدیریت سرویس ها» مراجعه کنید.", parse_mode: 'HTML');

        } catch (\Exception $e) {
            $bot->sendMessage("❌ <b>خطا در پردازش سفارش:</b>\n" . $e->getMessage(), parse_mode: 'HTML');
        }

        BotMenuService::showCustomerMenu($bot, $user, $bot->user()->first_name ?? 'کاربر');
        $this->end();
    }

    private function cancel(Nutgram $bot)
    {
        $bot->sendMessage('❌ عملیات خرید لغو شد.');
        $user = User::where('telegram_id', $bot->userId())->first();
        BotMenuService::showCustomerMenu($bot, $user, $bot->user()->first_name ?? 'کاربر');
        $this->end();
    }
}
