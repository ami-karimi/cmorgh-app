<?php

use SergiX44\Nutgram\Nutgram;
use App\Telegram\Conversations\LoginConversation;
use App\Telegram\Conversations\AdminSearchAccountConversation;
use App\Telegram\Conversations\CreateAccountConversation;
use App\Telegram\Conversations\AgentSubmitReceiptConversation;
use App\Telegram\Conversations\AgentSearchAccountConversation;
use App\Telegram\Conversations\AgentRenewAccountConversation;
use App\Telegram\Handlers\GeneralMenuHandler;
use App\Telegram\Handlers\AdminAccountActionHandler;
use App\Telegram\Handlers\AdminReceiptReviewHandler;
use App\Telegram\Handlers\AgentWalletHandler;
use App\Telegram\Handlers\AgentAccountActionHandler;
use App\Telegram\Handlers\WireguardHandler;
use App\Telegram\Handlers\AdminReceiptHandler;
use App\Telegram\Conversations\RegisterConversation;
use App\Telegram\Conversations\CustomerOrderServiceConversation;

/** @var SergiX44\Nutgram\Nutgram $bot */

// ==========================================
// ۱. مسیرهای عمومی و ورود/خروج
// ==========================================
$bot->onText('🔐 ورود به حساب کاربری', LoginConversation::class);
$bot->onText('📝 ساخت حساب کاربری', RegisterConversation::class);

use App\Telegram\Handlers\CustomerServiceHandler;

$bot->onText('⚙️ مدیریت سرویس ها', [CustomerServiceHandler::class, 'listServices']);

// بازگشت به لیست سرویس‌ها از روی دکمه شیشه‌ای
$bot->onCallbackQueryData('cust_services_list', [CustomerServiceHandler::class, 'listServices']);

// انتخاب یک سرویس خاص از لیست
$bot->onCallbackQueryData('cust_show_service:{id}', [CustomerServiceHandler::class, 'showServiceDetail']);

$bot->onText('🎁 دریافت اشتراک رایگان', function (Nutgram $bot) {
    $bot->sendMessage("🎁 بخش <b>دریافت اشتراک رایگان</b> به زودی فعال می‌شود.", parse_mode: 'HTML');
});
$bot->onText('💰 افزایش موجودی', \App\Telegram\Conversations\CustomerSubmitReceiptConversation::class);

$bot->onText('📞 ارتباط با پشتیبان', function (Nutgram $bot) {
    $bot->sendMessage("📞 <b>پشتیبانی:</b>\nلطفاً پیام خود را به آیدی @YourSupportID ارسال کنید.", parse_mode: 'HTML');
});

$bot->onText('🌐 ورود به پنل کاربری', function (Nutgram $bot) {
    $bot->sendMessage("🌐 <b>پنل کاربری:</b>\nجهت ورود به پنل وب‌سایت روی لینک زیر کلیک کنید:\nhttps://yourwebsite.com/login", parse_mode: 'HTML');
});

$bot->onText('🛍 سفارش سرویس جدید', CustomerOrderServiceConversation::class);



$bot->onCommand('start', [GeneralMenuHandler::class, 'start']);
$bot->onCallbackQueryData('back_to_admin_menu', [GeneralMenuHandler::class, 'backToMenu']);$bot->onCallbackQueryData('logout_account', [GeneralMenuHandler::class, 'logout']);
$bot->onCallbackQueryData('start_login', LoginConversation::class);$bot->onCallbackQueryData('no_account', [GeneralMenuHandler::class, 'noAccount']);


$bot->onCallbackQueryData('admin_online_count', [AdminAccountActionHandler::class, 'onlineCount']);


$bot->onCallbackQueryData('admin_manage_acc', AdminSearchAccountConversation::class);
$bot->onCallbackQueryData('admin_create_acc', CreateAccountConversation::class);$bot->onCallbackQueryData('admin_toggle_acc:{id}', [AdminAccountActionHandler::class, 'toggleStatus']);
$bot->onCallbackQueryData('admin_recharge_acc:{id}', [AdminAccountActionHandler::class, 'showRechargeGroups']);$bot->onCallbackQueryData('admin_do_recharge:{acc_id}:{grp_id}', [AdminAccountActionHandler::class, 'doRecharge']);

// ==========================================
// ۴. بررسی رسیدهای مالی (ادمین)
// ==========================================
$bot->onCallbackQueryData('admin_receipts', [AdminReceiptReviewHandler::class, 'startReview']);$bot->onCallbackQueryData('admin_handle_receipt:{id}:{action}', [AdminReceiptReviewHandler::class, 'handle']);
// (نکته: دکمه‌های تایید سریع از روی کارت که قبلا ساختیم)
$bot->onCallbackQueryData('admin_approve_receipt:{id}', [AdminReceiptHandler::class, 'approve']);
$bot->onCallbackQueryData('admin_reject_receipt:{id}', [AdminReceiptHandler::class, 'reject']);

// ==========================================
// ۵. مدیریت نمایندگان (Agent)
// ==========================================
$bot->onCallbackQueryData('agent_create_acc', CreateAccountConversation::class);$bot->onCallbackQueryData('agent_wallet', AgentWalletHandler::class);
$bot->onCallbackQueryData('agent_submit_receipt', AgentSubmitReceiptConversation::class);$bot->onCallbackQueryData('agent_manage_acc', AgentSearchAccountConversation::class);
$bot->onCallbackQueryData('agent_toggle_acc:{id}', [AgentAccountActionHandler::class, 'toggleStatus']);$bot->onCallbackQueryData('agent_renew_acc:{id}', AgentRenewAccountConversation::class);



$bot->onCallbackQueryData('dl_wg_conf:{id}', [WireguardHandler::class, 'downloadConf']);$bot->onCallbackQueryData('dl_wg_qr:{id}', [WireguardHandler::class, 'downloadQr']);


$bot->onText('🔍 مدیریت و جستجوی اکانت', \App\Telegram\Conversations\AgentSearchAccountConversation::class);
$bot->onText('🚪 خروج از حساب کاربری', [\App\Telegram\Handlers\GeneralMenuHandler::class, 'logout']);
$bot->onText('🟢 آمار اکانت‌های آنلاین', [AdminAccountActionHandler::class, 'onlineCount']);
$bot->onText('🧾 بررسی فیش‌های واریزی', [\App\Telegram\Handlers\AdminReceiptHandler::class, 'startReview']);
$bot->onText('🔍 جستجو و مدیریت اکانت', AdminSearchAccountConversation::class);
$bot->onText('➕ صدور اکانت جدید', CreateAccountConversation::class);

$bot->onText('🔍 مدیریت و جستجوی اکانت', AgentSearchAccountConversation::class);
$bot->onText('➕ ایجاد اکانت جدید', CreateAccountConversation::class);
$bot->onText('💰 موجودی ولت', AgentWalletHandler::class);
$bot->onText('👥 لیست مشتریان', function (Nutgram $bot) {
    $bot->sendMessage("👥 <b>لیست مشتریان شما</b>\n\nجهت مشاهده اطلاعات مشتریان می‌توانید از بخش «🔍 مدیریت و جستجوی اکانت» استفاده کنید.", parse_mode: 'HTML');
});
$bot->onText('🛒 سفارشات در انتظار', [\App\Telegram\Handlers\AdminStoreOrderHandler::class, 'startReview']);
$bot->onText('💳 موجودی ولت', AgentWalletHandler::class);


$bot->onCallbackQueryData('admin_orders', [\App\Telegram\Handlers\AdminStoreOrderHandler::class, 'startReview']);
$bot->onCallbackQueryData('admin_handle_order:{id}:{action}', [\App\Telegram\Handlers\AdminStoreOrderHandler::class, 'handle']);
