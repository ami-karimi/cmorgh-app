<?php

use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\Accounts;
use App\Models\Financial;
use App\Models\Group;
use App\Telegram\Services\BotMenuService;
use App\Telegram\Conversations\LoginConversation;
use App\Telegram\Conversations\AdminSearchAccountConversation;
use Illuminate\Support\Facades\DB;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Morilog\Jalali\Jalalian;
use App\Telegram\Conversations\CreateAccountConversation;
use App\Telegram\Handlers\AgentWalletHandler;
use App\Telegram\Conversations\AgentSubmitReceiptConversation;
use App\Telegram\Handlers\AdminReceiptHandler;
use App\Telegram\Conversations\AgentSearchAccountConversation;
use App\Telegram\Handlers\AgentAccountActionHandler;

/** @var SergiX44\Nutgram\Nutgram $bot */

// ==========================================
// ۱. استارت ربات و بازگشت به منوها
// ==========================================
$bot->onCommand('start', function (Nutgram $bot) {
    $user = User::where('telegram_id', $bot->userId())->first();

    if ($user) {
        BotMenuService::showMainMenu($bot, $user);
    } else {
        BotMenuService::showGuestMenu($bot, $bot->user()->first_name);
    }
});

// بازگشت به منوی اصلی ادمین
$bot->onCallbackQueryData('back_to_admin_menu', function (Nutgram $bot) {
    $bot->answerCallbackQuery();
    $user = User::where('telegram_id', $bot->userId())->first();
    if ($user) {
        BotMenuService::showMainMenu($bot, $user, isEdit: true);
    }
});

// ==========================================
// ۲. خروج از حساب کاربری
// ==========================================
$bot->onCallbackQueryData('logout_account', function (Nutgram $bot) {
    $user = User::where('telegram_id', $bot->userId())->first();

    if ($user) {
        $user->telegram_id = null;
        $user->save();

        $bot->answerCallbackQuery(text: 'با موفقیت از حساب کاربری خارج شدید!', show_alert: true);

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make('🔄 ورود مجدد / تغییر حساب', callback_data: 'start_login'));

        $bot->editMessageText("شما با موفقیت از حساب کاربری خود خارج شدید. 👋\n\nبرای ورود مجدد روی دکمه زیر کلیک کنید:", reply_markup: $keyboard);
    } else {
        $bot->answerCallbackQuery(text: 'شما به هیچ حسابی متصل نیستید!', show_alert: true);
    }
});

// ==========================================
// ۳. آمار آنلاین‌ها
// ==========================================
$bot->onCallbackQueryData('admin_online_count', function (Nutgram $bot) {
    $user = User::where('telegram_id', $bot->userId())->first();
    if (!$user || !in_array($user->role, ['admin', 'manager'])) {
        $bot->answerCallbackQuery(text: '⛔ شما دسترسی به این بخش را ندارید.', show_alert: true);
        return;
    }

    $totalAccounts = Accounts::count();
    $onlineAccounts = DB::table('radacct')->whereNull('acctstoptime')->count();
    $offlineAccounts = $totalAccounts - $onlineAccounts;
    $time = date('H:i:s');

    $text = "📊 <b>آمار لحظه‌ای اکانت‌های سیستم</b>\n";
    $text .= "➖➖➖➖➖➖➖➖➖➖\n\n";
    $text .= "👥 <b>کل اکانت‌ها:</b> {$totalAccounts} کاربر\n";
    $text .= "🟢 <b>آنلاین‌ها:</b> {$onlineAccounts} نفر\n";
    $text .= "🔴 <b>آفلاین‌ها:</b> {$offlineAccounts} نفر\n\n";
    $text .= "⏱ <i>آخرین بروزرسانی: {$time}</i>";

    $keyboard = InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make('🔙 بازگشت به منوی مدیریت', callback_data: 'back_to_admin_menu'))
        ->addRow(InlineKeyboardButton::make('🔄 بروزرسانی آمار', callback_data: 'admin_online_count'));

    try {
        $bot->editMessageText($text, parse_mode: 'HTML', reply_markup: $keyboard);
        $bot->answerCallbackQuery(text: '✅ آمار با موفقیت بروز شد.');
    } catch (\Exception $e) {
        $bot->answerCallbackQuery(text: '⚠️ آمار تغییری نکرده است!');
    }
});

// ==========================================
// ۴. مدیریت و جستجوی اکانت‌ها (ادمین)
// ==========================================
$bot->onCallbackQueryData('admin_manage_acc', AdminSearchAccountConversation::class);

// شارژ مجدد - انتخاب پکیج
$bot->onCallbackQueryData('admin_recharge_acc:{id}', function (Nutgram $bot, $id) {
    $account = Accounts::find($id);
    if (!$account) {
        $bot->answerCallbackQuery(text: '❌ اکانت یافت نشد!', show_alert: true);
        return;
    }

    $groups = Group::where('is_enabled', 1)->get();
    $keyboard = InlineKeyboardMarkup::make();

    foreach ($groups as $group) {
        $keyboard->addRow(InlineKeyboardButton::make(
            "📦 {$group->name} (" . number_format($group->price) . " تومان)",
            callback_data: "admin_do_recharge:{$account->id}:{$group->id}"
        ));
    }
    $keyboard->addRow(InlineKeyboardButton::make('🔙 انصراف و بازگشت', callback_data: 'admin_manage_acc'));

    $bot->answerCallbackQuery();
    $bot->editMessageText("🔄 <b>شارژ مجدد اکانت:</b> <code>{$account->username}</code>\n\nلطفاً پکیج مورد نظر را انتخاب کنید:", parse_mode: 'HTML', reply_markup: $keyboard);
});

// اجرای شارژ مجدد
$bot->onCallbackQueryData('admin_do_recharge:{acc_id}:{grp_id}', function (Nutgram $bot, $acc_id, $grp_id) {
    $admin = User::where('telegram_id', $bot->userId())->first();
    $account = Accounts::find($acc_id);
    $group = Group::find($grp_id);

    if (!$account || !$group || !$admin || !in_array($admin->role, ['admin', 'manager'])) {
        $bot->answerCallbackQuery(text: '❌ اطلاعات نامعتبر است!', show_alert: true);
        return;
    }

    $result = \App\Services\VpnManagerService::rechargeAccount($account, $group, true, $admin->id, false);

    if (!$result['status']) {
        $bot->answerCallbackQuery(text: '❌ خطا: ' . $result['message'], show_alert: true);
        return;
    }

    $account->refresh();
    $bot->answerCallbackQuery(text: '✅ ' . $result['message'], show_alert: true);

    $keyboard = InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make('🔍 جستجوی اکانت دیگر', callback_data: 'admin_manage_acc'))
        ->addRow(InlineKeyboardButton::make('🏠 منوی اصلی ادمین', callback_data: 'back_to_admin_menu'));

    $bot->editMessageText(
        "🎉 <b>شارژ مجدد با موفقیت انجام شد!</b>\n\n" .
        "👤 <b>اکانت:</b> <code>{$account->username}</code>\n" .
        "📦 <b>پکیج جدید:</b> {$group->name}\n\n" .
        "پیام سیستم: {$result['message']}",
        parse_mode: 'HTML',
        reply_markup: $keyboard
    );
});

// تغییر وضعیت (فعال / مسدود)
$bot->onCallbackQueryData('admin_toggle_acc:{id}', function (Nutgram $bot, $id) {
    $account = Accounts::find($id);
    if (!$account) {
        $bot->answerCallbackQuery(text: '❌ اکانت یافت نشد!', show_alert: true);
        return;
    }

    $success = \App\Services\VpnManagerService::toggleAccount($account);
    if (!$success) {
        $bot->answerCallbackQuery(text: '❌ خطا در ارتباط با سرور. وضعیت تغییر نکرد!', show_alert: true);
        return;
    }

    $account->refresh();
    $statusMsg = $account->is_enabled ? "🟢 وضعیت اکانت فعال شد." : "🔴 وضعیت اکانت مسدود شد.";
    $bot->answerCallbackQuery(text: $statusMsg, show_alert: true);

    // رندر مجدد کارت اطلاعات اکانت با استفاده از سرویس
    BotMenuService::renderAccountCard($bot, $account, isEdit: true);
});

// ==========================================
// ۵. مدیریت رسیدهای مالی
// ==========================================
$bot->onCallbackQueryData('admin_receipts', function (Nutgram $bot) {
    $receipt = Financial::where('approved', 0)->where('type', 'plus')->whereNotNull('attachment')->oldest()->first();
    if (!$receipt) {
        $bot->answerCallbackQuery(text: '🎉 هیچ فیش واریزی در انتظاری وجود ندارد!', show_alert: true);
        return;
    }

    $bot->answerCallbackQuery();
    showReceiptToAdmin($bot, $receipt);
});

$bot->onCallbackQueryData('admin_handle_receipt:{id}:{action}', function (Nutgram $bot, $id, $action) {
    $receipt = Financial::find($id);
    if (!$receipt || $receipt->approved != 0) {
        $bot->answerCallbackQuery(text: '❌ فیش یافت نشد یا قبلاً بررسی شده است.', show_alert: true);
        checkNextReceipt($bot);
        return;
    }

    $status = ($action === 'approve') ? 1 : 2;
    $receipt->update(['approved' => $status]);

    $msg = ($status === 1) ? '✅ فیش با موفقیت تایید شد.' : '❌ فیش رد شد.';
    $bot->answerCallbackQuery(text: $msg, show_alert: true);

    checkNextReceipt($bot);
});

function checkNextReceipt(Nutgram $bot) {
    $nextReceipt = Financial::where('approved', 0)->where('type', 'plus')->whereNotNull('attachment')->oldest()->first();
    if ($nextReceipt) {
        showReceiptToAdmin($bot, $nextReceipt);
    } else {
        $keyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu'));
        $bot->sendMessage("🎉 <b>تبریک!</b>\nتمام فیش‌های در انتظار بررسی شدند و صف خالی است.", parse_mode: 'HTML', reply_markup: $keyboard);
    }
}

function showReceiptToAdmin(Nutgram $bot, Financial $receipt) {
    $user = User::find($receipt->for);
    $userName = $user ? $user->name . " (@" . ($user->username ?? 'بدون_یوزرنیم') . ")" : 'کاربر نامشخص';
    $amount = number_format($receipt->price);
    $jalaliDate = Jalalian::forge($receipt->created_at)->format('Y/m/d - H:i');
    $desc = $receipt->description ?? 'ثبت فیش واریزی';

    $caption = "🧾 <b>بررسی فیش واریزی جدید</b>\n";
    $caption .= "➖➖➖➖➖➖➖➖➖➖\n";
    $caption .= "👤 <b>متقاضی:</b> {$userName}\n";
    $caption .= "💰 <b>مبلغ فیش:</b> {$amount} تومان\n";
    $caption .= "📝 <b>توضیحات:</b> {$desc}\n";
    $caption .= "📅 <b>تاریخ ثبت:</b> {$jalaliDate}\n";

    $keyboard = InlineKeyboardMarkup::make()
        ->addRow(
            InlineKeyboardButton::make('✅ تایید فیش', callback_data: "admin_handle_receipt:{$receipt->id}:approve"),
            InlineKeyboardButton::make('❌ رد فیش', callback_data: "admin_handle_receipt:{$receipt->id}:reject")
        )
        ->addRow(InlineKeyboardButton::make('🏠 انصراف و بازگشت به منو', callback_data: 'back_to_admin_menu'));

    $filePath = storage_path('app/public/' . $receipt->attachment);

    if (file_exists($filePath)) {
        $photo = \SergiX44\Nutgram\Telegram\Types\Internal\InputFile::make($filePath);
        $bot->sendPhoto($photo, caption: $caption, parse_mode: 'HTML', reply_markup: $keyboard);
    } else {
        $caption .= "\n⚠️ <i>تصویر فیش در سرور یافت نشد.</i>";
        $bot->sendMessage($caption, parse_mode: 'HTML', reply_markup: $keyboard);
    }
}
$bot->onCallbackQueryData('admin_create_acc', CreateAccountConversation::class);
$bot->onCallbackQueryData('agent_create_acc', CreateAccountConversation::class);

$bot->onCallbackQueryData('dl_wg_conf:{id}', function (\SergiX44\Nutgram\Nutgram $bot, $id) {
    $account = \App\Models\Accounts::find($id);

    if (!$account || $account->service_group !== 'wireguard') {
        $bot->answerCallbackQuery(text: '❌ اکانت نامعتبر است یا وایرگارد نیست.', show_alert: true);
        return;
    }

    $wgUser = \App\Models\WireGuardUsers::where('user_id', $account->id)->first();
    if (!$wgUser) {
        $bot->answerCallbackQuery(text: '❌ پروفایل وایرگارد این کاربر یافت نشد.', show_alert: true);
        return;
    }

    $bot->answerCallbackQuery(text: '⏳ در حال ارسال فایل...');

    $profileName = $wgUser->profile_name;
    $confPath = public_path("configs/{$profileName}.conf");

    if (file_exists($confPath)) {
        $doc = \SergiX44\Nutgram\Telegram\Types\Internal\InputFile::make($confPath, filename: "{$profileName}.conf");
        $bot->sendDocument($doc, caption: "📄 <b>فایل کانفیگ:</b> <code>{$profileName}.conf</code>", parse_mode: 'HTML');
    } else {
        $bot->sendMessage("⚠️ فایل کانفیگ در مسیر سرور یافت نشد.", parse_mode: 'HTML');
    }
});


// ==========================================
// دریافت عکس QR Code وایرگارد
// ==========================================
$bot->onCallbackQueryData('dl_wg_qr:{id}', function (\SergiX44\Nutgram\Nutgram $bot, $id) {
    $account = \App\Models\Accounts::find($id);

    if (!$account || $account->service_group !== 'wireguard') {
        $bot->answerCallbackQuery(text: '❌ اکانت نامعتبر است یا وایرگارد نیست.', show_alert: true);
        return;
    }

    $wgUser = \App\Models\WireGuardUsers::where('user_id', $account->id)->first();
    if (!$wgUser) {
        $bot->answerCallbackQuery(text: '❌ پروفایل وایرگارد این کاربر یافت نشد.', show_alert: true);
        return;
    }

    $bot->answerCallbackQuery(text: '⏳ در حال ارسال QR Code...');

    $profileName = $wgUser->profile_name;
    $qrPath = public_path("configs/{$profileName}.png");

    if (file_exists($qrPath)) {
        $photo = \SergiX44\Nutgram\Telegram\Types\Internal\InputFile::make($qrPath);
        $bot->sendPhoto($photo, caption: "📱 <b>اسکن جهت اتصال سریع</b>\nکاربر: <code>{$account->username}</code>", parse_mode: 'HTML');
    } else {
        $bot->sendMessage("⚠️ تصویر QR Code در مسیر سرور یافت نشد.", parse_mode: 'HTML');
    }
});
$bot->onCallbackQueryData('agent_wallet', AgentWalletHandler::class);

$bot->onCallbackQueryData('agent_submit_receipt', AgentSubmitReceiptConversation::class);

$bot->onCallbackQueryData('admin_approve_receipt:{id}', [AdminReceiptHandler::class, 'approve']);
$bot->onCallbackQueryData('admin_reject_receipt:{id}', [AdminReceiptHandler::class, 'reject']);


$bot->onCallbackQueryData('agent_manage_acc', AgentSearchAccountConversation::class);

// ==========================================
// ⚙️ تغییر وضعیت (فعال/غیرفعال)
// ==========================================
$bot->onCallbackQueryData('agent_toggle_acc:{id}', [AgentAccountActionHandler::class, 'toggleStatus']);

// ==========================================
// 🔄 تمدید اکانت
// ==========================================
$bot->onCallbackQueryData('agent_renew_acc:{id}', \App\Telegram\Conversations\AgentRenewAccountConversation::class);

// ==========================================
// ۶. مسیرهای عمومی
// ==========================================
$bot->onCallbackQueryData('start_login', LoginConversation::class);


$bot->onCallbackQueryData('no_account', function (Nutgram $bot) {
    $bot->answerCallbackQuery();
    $bot->sendMessage("برای تهیه اکانت یا اخذ نمایندگی، لطفاً با پشتیبانی در ارتباط باشید یا از طریق وب‌سایت اقدام کنید.");
});
