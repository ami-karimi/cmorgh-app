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
use App\Models\Accounts;
use App\Telegram\Services\BotMenuService;
use App\Services\AccountProvisioningService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateAccountConversation extends Conversation
{
    public $creatorId;
    public $customerId; // 'me' یا 'new'
    public $serviceGroup; // 'wireguard' یا 'l2tp_cisco'
    public $groupId;
    public $username;
    public $password;

    public $newCustomerName;
    public $newCustomerPhone;

    /**
     * مرحله ۱: انتخاب مشتری
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
                InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu')
            );

        $text = "➕ <b>مرحله ۱ از ۵: انتخاب مشتری</b>\n\nاین اکانت قرار است به نام چه کسی ثبت شود؟";

        if ($bot->isCallbackQuery()) {
            $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
        } else {
            $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        }

        $this->next('handleCustomerChoice');
    }

    /**
     * پردازش مرحله ۱
     */
    public function handleCustomerChoice(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $data = $bot->callbackQuery()->data;

            if ($data === 'back_to_admin_menu') {
                $bot->answerCallbackQuery();
                $user = User::find($this->creatorId);
                if ($user) {
                    BotMenuService::showMainMenu($bot, $user, isEdit: true);
                }
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

                $keyboard = InlineKeyboardMarkup::make()
                    ->addRow(InlineKeyboardButton::make('🔙 مرحله قبل', callback_data: 'step_back_to_start'));

                $bot->editMessageText("👤 لطفاً **نام کامل مشتری** جدید را تایپ و ارسال کنید:", reply_markup: $keyboard);
                $this->next('askCustomerPhone');
                return;
            }
        }
    }

    /**
     * دریافت نام مشتری و پرسش شماره تلفن
     */
    public function askCustomerPhone(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            if ($bot->callbackQuery()->data === 'step_back_to_start') {
                $bot->answerCallbackQuery();
                $this->start($bot);
                return;
            }
        }

        $this->newCustomerName = $bot->message()?->text;

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make('⏩ رد کردن (بدون شماره)', callback_data: 'skip_phone'))
            ->addRow(InlineKeyboardButton::make('🔙 مرحله قبل', callback_data: 'step_back_to_cust_choice'));

        $bot->sendMessage("📱 لطفاً **شماره موبایل** مشتری را وارد کنید (یا رد کنید):", reply_markup: $keyboard);
        $this->next('askServiceGroupFromPhone');
    }

    /**
     * پردازش شماره تلفن
     */
    public function askServiceGroupFromPhone(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $data = $bot->callbackQuery()->data;

            if ($data === 'step_back_to_cust_choice') {
                $bot->answerCallbackQuery();
                $this->start($bot);
                return;
            }

            if ($data === 'skip_phone') {
                $this->newCustomerPhone = null;
                $bot->answerCallbackQuery();
                $this->askServiceGroup($bot);
                return;
            }
        } else {
            $this->newCustomerPhone = $bot->message()?->text;
            $this->askServiceGroup($bot);
        }
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
            ->addRow(
                InlineKeyboardButton::make('🔙 مرحله قبل', callback_data: 'step_back_to_cust_choice')
            );

        $text = "📦 <b>مرحله ۲ از ۵: انتخاب پروتکل اتصال</b>\n\nنوع سرویس این اکانت را انتخاب کنید:";

        if ($bot->isCallbackQuery()) {
            $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
        } else {
            $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        }

        $this->next('handleServiceChoice');
    }

    /**
     * پردازش سرویس و مرحله ۳: انتخاب پکیج
     */
    public function handleServiceChoice(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $data = $bot->callbackQuery()->data;

            if ($data === 'step_back_to_cust_choice') {
                $bot->answerCallbackQuery();
                $this->start($bot);
                return;
            }

            $this->serviceGroup = str_replace('srv_', '', $data);
            $bot->answerCallbackQuery();

            $query = Group::where('is_enabled', 1);

            if ($this->serviceGroup === 'wireguard') {
                $query->where('name', 'like', '%وایرگارد%');
            } else {
                $query->where('name', 'not like', '%وایرگارد%');
            }

            $groups = $query->get();

            if ($groups->isEmpty()) {
                $keyboard = InlineKeyboardMarkup::make()
                    ->addRow(InlineKeyboardButton::make('🔙 انتخاب سرویس دیگر', callback_data: 'step_back_to_service'));

                $bot->editMessageText("⚠️ هیچ پکیجی برای این سرویس یافت نشد.", reply_markup: $keyboard);
                return;
            }

            $keyboard = InlineKeyboardMarkup::make();
            $agent = User::find($this->creatorId);

            foreach ($groups as $group) {
                $finalPrice = method_exists($group, 'getFinalPriceFor') ? $group->getFinalPriceFor($agent) : $group->price;
                $keyboard->addRow(
                    InlineKeyboardButton::make("{$group->name} (" . number_format($finalPrice) . " تومان)", callback_data: "grp_{$group->id}")
                );
            }

            $keyboard->addRow(InlineKeyboardButton::make('🔙 مرحله قبل', callback_data: 'step_back_to_service'));

            $bot->editMessageText("🛒 <b>مرحله ۳ از ۵: انتخاب پکیج/تعرفه</b>\n\nلطفاً پکیج مورد نظر را انتخاب کنید:", parse_mode: 'HTML', reply_markup: $keyboard);
            $this->next('handleGroupChoice');
        }
    }

    /**
     * پردازش پکیج و مرحله ۴: تعیین یوزرنیم
     */
    public function handleGroupChoice(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $data = $bot->callbackQuery()->data;

            if ($data === 'step_back_to_service') {
                $bot->answerCallbackQuery();
                $this->askServiceGroup($bot);
                return;
            }

            $this->groupId = str_replace('grp_', '', $data);
            $bot->answerCallbackQuery();

            $keyboard = InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('🎲 تولید تصادفی', callback_data: 'rand_username'))
                ->addRow(InlineKeyboardButton::make('🔙 مرحله قبل', callback_data: 'step_back_to_group'));

            $bot->editMessageText("✍️ <b>مرحله ۴ از ۵: تعیین نام‌کاربری</b>\n\nنام‌کاربری دلخواه (انگلیسی) را وارد کنید یا روی تولید تصادفی کلیک کنید:", parse_mode: 'HTML', reply_markup: $keyboard);
            $this->next('handleUsernameChoice');
        }
    }

    /**
     * پردازش یوزرنیم و مرحله ۵: تعیین پسورد
     */
    public function handleUsernameChoice(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $data = $bot->callbackQuery()->data;

            if ($data === 'step_back_to_group') {
                $bot->answerCallbackQuery();
                // ساخت مجدد کال‌بک انتخاب سرویس برای نمایش پکیج‌ها
                $fakeBot = clone $bot;
                $fakeBot->callbackQuery()->data = 'srv_' . $this->serviceGroup;
                $this->handleServiceChoice($fakeBot);
                return;
            }

            if ($data === 'rand_username') {
                $this->username = strtolower(Str::random(6) . rand(10, 99));
                $bot->answerCallbackQuery();
            }
        } else {
            $input = trim(strtolower($bot->message()?->text ?? ''));
            if (Accounts::where('username', $input)->exists()) {
                $keyboard = InlineKeyboardMarkup::make()
                    ->addRow(InlineKeyboardButton::make('🎲 تولید تصادفی', callback_data: 'rand_username'))
                    ->addRow(InlineKeyboardButton::make('🔙 مرحله قبل', callback_data: 'step_back_to_group'));

                $bot->sendMessage("⚠️ این نام‌کاربری قبلاً ثبت شده! یک نام دیگر وارد کنید:", reply_markup: $keyboard);
                return;
            }
            $this->username = $input;
        }

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make('🎲 تولید تصادفی', callback_data: 'rand_password'))
            ->addRow(InlineKeyboardButton::make('🔙 مرحله قبل', callback_data: 'step_back_to_username'));

        $bot->sendMessage("🔑 <b>مرحله ۵ از ۵: تعیین رمز عبور</b>\n\nرمز عبور را وارد کنید (حداقل 4 کاراکتر) یا تصادفی بزنید:", parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('showFinalConfirmation');
    }

    /**
     * نمایش پیش‌فاکتور نهایی
     */
    public function showFinalConfirmation(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $data = $bot->callbackQuery()->data;

            if ($data === 'step_back_to_username') {
                $bot->answerCallbackQuery();
                $fakeBot = clone $bot;
                $fakeBot->callbackQuery()->data = 'grp_' . $this->groupId;
                $this->handleGroupChoice($fakeBot);
                return;
            }

            if ($data === 'rand_password') {
                $this->password = (string) rand(100000, 999999);
                $bot->answerCallbackQuery();
            }
        } else {
            $this->password = trim($bot->message()?->text ?? '');
        }

        $group = Group::find($this->groupId);
        $agent = User::find($this->creatorId);
        $costPrice = method_exists($group, 'getFinalPriceFor') ? $group->getFinalPriceFor($agent) : $group->price;
        $customerName = $this->customerId === 'me' ? '🗂️ آرشیو خودم' : $this->newCustomerName;

        $text = "🧾 <b>پیش‌فاکتور صدور اکانت جدید</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "👤 <b>مشتری:</b> {$customerName}\n";
        $text .= "📦 <b>سرویس:</b> {$this->serviceGroup}\n";
        $text .= "🛒 <b>پکیج:</b> {$group->name}\n";
        $text .= "📝 <b>نام‌کاربری:</b> <code>{$this->username}</code>\n";
        $text .= "🔑 <b>رمز عبور:</b> <code>{$this->password}</code>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "💰 <b>هزینه کسر از ولت:</b> " . number_format($costPrice) . " تومان\n\n";
        $text .= "⚠️ <i>در صورت تایید، مبلغ فوق از ولت شما کسر می‌شود.</i>";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('✅ تایید و صدور نهایی', callback_data: 'confirm_create')
            )
            ->addRow(
                InlineKeyboardButton::make('🔙 اصلاح اطلاعات (مرحله قبل)', callback_data: 'step_back_to_password'),
                InlineKeyboardButton::make('❌ لغو کامل', callback_data: 'back_to_admin_menu')
            );

        $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('executeCreation');
    }

    /**
     * اجرا و صدور نهایی
     */
    public function executeCreation(Nutgram $bot)
    {
        if (!$bot->isCallbackQuery()) return;

        $data = $bot->callbackQuery()->data;

        if ($data === 'step_back_to_password') {
            $bot->answerCallbackQuery();
            $fakeBot = clone $bot;
            $fakeBot->callbackQuery()->data = 'rand_username';
            $this->handleUsernameChoice($fakeBot);
            return;
        }

        if ($data === 'back_to_admin_menu') {
            $bot->answerCallbackQuery('عملیات لغو شد.');
            $user = User::find($this->creatorId);
            if ($user) {
                BotMenuService::showMainMenu($bot, $user, isEdit: true);
            }
            $this->end();
            return;
        }

        if ($data === 'confirm_create') {
            $bot->answerCallbackQuery();
            $bot->sendMessage("⏳ در حال ارتباط با سرور و ساخت اکانت...");

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

                $overrides = [
                    'username'      => $this->username,
                    'password'      => $this->password,
                    'service_group' => $this->serviceGroup,
                ];

                $preparedData = $provisioningService->prepareAccountData($group, $targetUser, $this->newCustomerPhone, $overrides);
                $preparedData['userData']['custom_creator'] = $agent->id;
                $result = $provisioningService->createFullAccount(
                    $preparedData['userData'],
                    $preparedData['configData'],
                    $existingUserId,
                    ($agent->role === 'manager' || $agent->admin) ? false : true,
                    false
                );

                if (is_array($result) && isset($result['status']) && !$result['status']) {
                    throw new \Exception($result['message']);
                }

                DB::commit();

                $successText = "🎉 <b>اکانت با موفقیت صادر و ثبت شد!</b>\n";
                $successText .= "➖➖➖➖➖➖➖➖➖➖\n";
                $successText .= "👤 <b>مشتری:</b> " . ($this->customerId === 'me' ? 'آرشیو من' : $this->newCustomerName) . "\n";
                $successText .= "📦 <b>سرویس:</b> {$this->serviceGroup}\n";
                $successText .= "📝 <b>نام‌کاربری:</b> <code>{$this->username}</code>\n";
                $successText .= "🔑 <b>رمز عبور:</b> <code>{$this->password}</code>\n";


                // ==========================================================
                // 🔴 ارسال فایل .conf و QR Code اختصاصی برای سرویس وایرگارد
                // ==========================================================
                if ($this->serviceGroup === 'wireguard' && $result) {
                    $wgUser = \App\Models\WireGuardUsers::where('user_id', $result->id)->first();

                    if ($wgUser) {
                        $profileName = $wgUser->profile_name; // مثلاً username123

                        // ۱. مسیر دقیق فایل .conf در پوشه public/.configs
                        $confPath = public_path("configs/{$profileName}.conf");

                        // ۲. مسیر دقیق فایل عکس QR در پوشه public/.configs
                        $qrPath = public_path("configs/{$profileName}.png");

                        // 📤 ارسال فایل کانفیگ (.conf)
                        if (file_exists($confPath)) {
                            $doc = \SergiX44\Nutgram\Telegram\Types\Internal\InputFile::make($confPath, filename: "{$profileName}.conf");
                            $bot->sendDocument($doc, caption: "📄 <b>فایل کانفیگ وایرگارد:</b> <code>{$profileName}.conf</code>", parse_mode: 'HTML');
                        } else {
                            $bot->sendMessage("⚠️ فایل کانفیگ (<code>{$profileName}.conf</code>) در مسیر سرور یافت نشد.", parse_mode: 'HTML');
                        }

                        // 📤 ارسال عکس QR Code
                        if (file_exists($qrPath)) {
                            $photo = \SergiX44\Nutgram\Telegram\Types\Internal\InputFile::make($qrPath);
                            $bot->sendPhoto($photo, caption: "📱 <b>QR Code جهت اسکن سریع در اپلیکیشن WireGuard</b>", parse_mode: 'HTML');
                        }
                    }
                }

                $keyboard = InlineKeyboardMarkup::make()
                    ->addRow(InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu'));

                $bot->sendMessage($successText, parse_mode: 'HTML', reply_markup: $keyboard);

            } catch (\Exception $e) {
                DB::rollBack();
                $keyboard = InlineKeyboardMarkup::make()
                    ->addRow(InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu'));

                $bot->sendMessage("❌ <b>خطا در صدور اکانت:</b>\n" . $e->getMessage(), parse_mode: 'HTML', reply_markup: $keyboard);
            }

            $this->end();
        }
    }
}
