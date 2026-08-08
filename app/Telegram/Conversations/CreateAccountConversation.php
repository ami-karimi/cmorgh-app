<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Group;
use App\Models\Nas;
use App\Services\AccountProvisioningService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateAccountConversation extends Conversation
{
    // متغیرهای ذخیره‌سازی اطلاعات در طول مکالمه
    public $creatorId;
    public $customerId; // 'me' یا آیدی مشتری
    public $serviceGroup;
    public $groupId;
    public $username;
    public $password;

    // متغیرهای موقت برای فرم مشتری جدید
    public $newCustomerName;
    public $newCustomerPhone;

    /**
     * مرحله ۱: شروع صدور - انتخاب مشتری
     */
    public function start(Nutgram $bot)
    {
        $this->creatorId = User::where('telegram_id', $bot->userId())->value('id');

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('🗂️ صدور در آرشیو خودم (بدون مشتری)', callback_data: 'cust_me')
            )
            ->addRow(
                InlineKeyboardButton::make('➕ ایجاد مشتری جدید', callback_data: 'cust_new')
            )
            ->addRow(
                InlineKeyboardButton::make('❌ لغو عملیات', callback_data: 'cancel_creation')
            );

        $bot->sendMessage("➕ <b>مرحله ۱: انتخاب مشتری</b>\n\nاین اکانت قرار است به نام چه کسی ثبت شود؟", parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('handleCustomerChoice');
    }

    /**
     * هندلر دکمه‌های انتخاب مشتری
     */
    public function handleCustomerChoice(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $data = $bot->callbackQuery()->data;

            if ($data === 'cancel_creation') {
                $bot->answerCallbackQuery('عملیات لغو شد.');
                $bot->deleteMessage($bot->chatId(), $bot->messageId());
                $this->end();
                return;
            }

            if ($data === 'cust_me') {
                $this->customerId = 'me';
                $bot->answerCallbackQuery();
                $this->askServiceGroup($bot);
                return;
            }

            if ($data === 'cust_new') {
                $this->customerId = 'new';
                $bot->answerCallbackQuery();
                $bot->sendMessage("👤 لطفاً **نام کامل مشتری** جدید را تایپ و ارسال کنید:");
                $this->next('askCustomerPhone');
                return;
            }
        }
    }

    /**
     * هندلر دریافت نام و پرسش تلفن مشتری
     */
    public function askCustomerPhone(Nutgram $bot)
    {
        $this->newCustomerName = $bot->message()?->text;

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make('⏩ رد کردن (بدون شماره)', callback_data: 'skip_phone'));

        $bot->sendMessage("📱 لطفاً **شماره موبایل** مشتری را وارد کنید (یا رد کنید):", reply_markup: $keyboard);
        $this->next('askServiceGroupFromPhone');
    }

    /**
     * هندلر دریافت تلفن
     */
    public function askServiceGroupFromPhone(Nutgram $bot)
    {
        if ($bot->isCallbackQuery() && $bot->callbackQuery()->data === 'skip_phone') {
            $this->newCustomerPhone = null;
            $bot->answerCallbackQuery();
        } else {
            $this->newCustomerPhone = $bot->message()?->text;
        }

        $this->askServiceGroup($bot);
    }

    /**
     * مرحله ۲: انتخاب سرویس (پروتکل)
     */
    public function askServiceGroup(Nutgram $bot)
    {
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('🟣 وایرگارد (Wireguard)', callback_data: 'srv_wireguard'),
                InlineKeyboardButton::make('🔵 سیسکو (L2TP/Cisco)', callback_data: 'srv_l2tp_cisco')
            )

        $bot->sendMessage("📦 <b>مرحله ۲: انتخاب پروتکل اتصال</b>\n\nنوع سرویس این اکانت را انتخاب کنید:", parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('handleServiceChoice');
    }

    /**
     * هندلر دریافت سرویس و نمایش پکیج‌ها
     */
    public function handleServiceChoice(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $data = $bot->callbackQuery()->data;
            $this->serviceGroup = str_replace('srv_', '', $data);
            $bot->answerCallbackQuery();

            // دریافت لیست گروه‌ها (تعرفه‌ها)
            $groups = Group::where('is_enabled', 1)->get();
            $keyboard = InlineKeyboardMarkup::make();

            foreach ($groups as $group) {
                $keyboard->addRow(
                    InlineKeyboardButton::make("{$group->name} (" . number_format($group->price) . " تومان)", callback_data: "grp_{$group->id}")
                );
            }

            $bot->sendMessage("🛒 <b>مرحله ۳: انتخاب پکیج/تعرفه</b>\n\nلطفاً پکیج مورد نظر را انتخاب کنید:", parse_mode: 'HTML', reply_markup: $keyboard);
            $this->next('handleGroupChoice');
        }
    }

    /**
     * هندلر دریافت گروه و پرسش یوزرنیم
     */
    public function handleGroupChoice(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $data = $bot->callbackQuery()->data;
            $this->groupId = str_replace('grp_', '', $data);
            $bot->answerCallbackQuery();

            $keyboard = InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('🎲 تولید تصادفی', callback_data: 'rand_username'));

            $bot->sendMessage("✍️ <b>مرحله ۴: تعیین نام‌کاربری</b>\n\nنام‌کاربری دلخواه (انگلیسی) را وارد کنید یا روی تولید تصادفی کلیک کنید:", parse_mode: 'HTML', reply_markup: $keyboard);
            $this->next('handleUsernameChoice');
        }
    }

    /**
     * هندلر دریافت یوزرنیم و پرسش پسورد
     */
    public function handleUsernameChoice(Nutgram $bot)
    {
        if ($bot->isCallbackQuery() && $bot->callbackQuery()->data === 'rand_username') {
            $this->username = strtolower(Str::random(6) . rand(10,99));
            $bot->answerCallbackQuery();
        } else {
            $input = trim(strtolower($bot->message()?->text ?? ''));
            // چک کردن تکراری نبودن
            if (\App\Models\Accounts::where('username', $input)->exists()) {
                $bot->sendMessage("⚠️ این نام‌کاربری قبلاً ثبت شده! یک نام دیگر وارد کنید:");
                return;
            }
            $this->username = $input;
        }

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make('🎲 تولید تصادفی', callback_data: 'rand_password'));

        $bot->sendMessage("🔑 <b>مرحله ۵: تعیین رمز عبور</b>\n\nرمز عبور را وارد کنید (حداقل 4 کاراکتر) یا تصادفی بزنید:", parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('showFinalConfirmation');
    }

    /**
     * مرحله نهایی: پیش‌فاکتور و تایید
     */
    public function showFinalConfirmation(Nutgram $bot)
    {
        if ($bot->isCallbackQuery() && $bot->callbackQuery()->data === 'rand_password') {
            $this->password = (string) rand(100000, 999999);
            $bot->answerCallbackQuery();
        } else {
            $this->password = trim($bot->message()?->text ?? '');
        }

        $group = Group::find($this->groupId);
        $customerName = $this->customerId === 'me' ? '🗂️ آرشیو خودم' : $this->newCustomerName;

        $text = "🧾 <b>پیش‌فاکتور صدور اکانت جدید</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "👤 <b>مشتری:</b> {$customerName}\n";
        $text .= "📦 <b>سرویس:</b> {$this->serviceGroup}\n";
        $text .= "🛒 <b>پکیج:</b> {$group->name} (" . number_format($group->price) . "ت)\n";
        $text .= "📝 <b>نام‌کاربری:</b> <code>{$this->username}</code>\n";
        $text .= "🔑 <b>رمز عبور:</b> <code>{$this->password}</code>\n\n";
        $text .= "⚠️ <i>در صورت تایید، مبلغ پکیج از ولت شما کسر می‌شود.</i>";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('✅ تایید و صدور نهایی', callback_data: 'confirm_create'),
                InlineKeyboardButton::make('❌ لغو', callback_data: 'cancel_creation')
            );

        $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('executeCreation');
    }

    /**
     * اجرای منطق ساخت اکانت (مشابه متد Save کنترلر)
     */
    public function executeCreation(Nutgram $bot)
    {
        if (!$bot->isCallbackQuery()) return;

        $data = $bot->callbackQuery()->data;
        $bot->answerCallbackQuery();

        if ($data === 'cancel_creation') {
            $bot->editMessageText("❌ عملیات صدور اکانت لغو شد.");
            $this->end();
            return;
        }

        if ($data === 'confirm_create') {
            $bot->editMessageText("⏳ در حال ارتباط با سرور و ساخت اکانت...");

            try {
                DB::beginTransaction();

                $agent = User::find($this->creatorId);
                $group = Group::find($this->groupId);
                $provisioningService = app(AccountProvisioningService::class);

                $targetUser = null;
                $existingUserId = null;

                if ($this->customerId === 'me') {
                    $targetUser = User::firstOrCreate(
                        ['creator' => $agent->id, 'role' => 'customer', 'email' => 'archive_' . $agent->id . '@local.system'],
                        ['name' => '🗂️ آرشیو اکانت‌های من', 'username' => 'archive_agent_' . $agent->id, 'password' => Hash::make(Str::random(16)), 'is_active' => 1]
                    );
                    $existingUserId = $targetUser->id;
                } elseif ($this->customerId === 'new') {
                    $targetUser = new User([
                        'name'     => $this->newCustomerName,
                        'username' => $this->newCustomerPhone ?? Str::random(10),
                        'phone'    => $this->newCustomerPhone,
                        'role'     => 'customer',
                        'creator'  => $agent->id,
                    ]);
                }

                // ۲. اورراید داده‌ها
                $overrides = [
                    'username'      => $this->username,
                    'password'      => $this->password,
                    'service_group' => $this->serviceGroup,
                ];

                if ($this->serviceGroup === 'wireguard') {
                    // گرفتن سرور پیش فرض وایرگارد
                    $defaultWgServerId = Nas::where('type', 'wireguard')->value('id');
                    $overrides['wg_server_id'] = $defaultWgServerId;
                } elseif ($this->serviceGroup === 'v2ray') {
                    $overrides['protocol_v2ray'] = 'vless'; // پیش فرض
                }

                $preparedData = $provisioningService->prepareAccountData($group, $targetUser, $this->newCustomerPhone, $overrides);

                // ۳. صدور کامل اکانت (کسر ولت نماینده = true)
                $result = $provisioningService->createFullAccount(
                    $preparedData['userData'],
                    $preparedData['configData'],
                    $existingUserId,
                    true,  // payFromAgentWallet
                    false  // payFromUserWallet
                );

                if (is_array($result) && isset($result['status']) && !$result['status']) {
                    throw new \Exception($result['message']);
                }

                DB::commit();

                // 🔴 نمایش موفقیت و اطلاعات
                $successText = "🎉 <b>اکانت با موفقیت صادر و ثبت شد!</b>\n";
                $successText .= "➖➖➖➖➖➖➖➖➖➖\n";
                $successText .= "👤 <b>مشتری:</b> " . ($this->customerId === 'me' ? 'آرشیو من' : $this->newCustomerName) . "\n";
                $successText .= "📦 <b>سرویس:</b> {$this->serviceGroup}\n";
                $successText .= "📝 <b>نام‌کاربری:</b> <code>{$this->username}</code>\n";
                $successText .= "🔑 <b>رمز عبور:</b> <code>{$this->password}</code>\n";

                $bot->sendMessage($successText, parse_mode: 'HTML');

            } catch (\Exception $e) {
                DB::rollBack();
                $bot->sendMessage("❌ <b>خطا در صدور اکانت:</b>\n" . $e->getMessage(), parse_mode: 'HTML');
            }

            $this->end();
        }
    }
}
