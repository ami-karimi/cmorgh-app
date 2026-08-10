<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\StoreOrder;
use App\Services\AccountProvisioningService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;
use Morilog\Jalali\Jalalian;

class AdminStoreOrderHandler
{
    /**
     * شروع بررسی سفارشات در انتظار
     */
    public function startReview(Nutgram $bot)
    {
        // دریافت قدیمی‌ترین سفارش در انتظار (وضعیت pending)
        $order = StoreOrder::with(['user', 'agent', 'group'])->where('status', 'pending')->oldest()->first();

        if (!$order) {
            $bot->sendMessage("🎉 <b>تبریک!</b>\nهیچ سفارش بررسی‌نشده‌ای در فروشگاه وجود ندارد.", parse_mode: 'HTML');
            return;
        }

        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $this->showOrderToAdmin($bot, $order);
    }

    /**
     * پردازش تایید یا رد سفارش توسط ادمین
     */
    public function handle(Nutgram $bot, $id, $action)
    {
        $order = StoreOrder::with(['user', 'group'])->find($id);

        if (!$order || $order->status !== 'pending') {
            $bot->answerCallbackQuery(text: '❌ سفارش یافت نشد یا قبلاً پردازش شده است.', show_alert: true);
            $this->checkNextOrder($bot);
            return;
        }

        if ($action === 'approve') {
            $this->approveOrder($bot, $order);
        } else {
            $this->rejectOrder($bot, $order);
        }
    }

    /**
     * منطق تایید سفارش و صدور اکانت (دقیقاً مشابه متد approveOrder در Livewire)
     */
    private function approveOrder(Nutgram $bot, StoreOrder $order)
    {
        try {
            $bot->answerCallbackQuery(text: '⏳ در حال صدور اکانت و ارتباط با سرور...');

            DB::transaction(function () use ($order) {
                $user = $order->user;
                $group = $order->group;

                // انتساب کاربر به نماینده (اگر سفارشی از سمت نماینده ثبت شده باشد)
                if ($order->agent_id && $user->creator != $order->agent_id) {
                    $user->creator = $order->agent_id;
                    $user->save();
                }

                $accService = new AccountProvisioningService();

                // آماده‌سازی داده‌های اکانت
                $preparedData = $accService->prepareAccountData($group, $user, $order->phone);

                // صدور اکانت روی سرور و دیتابیس
                $create = $accService->createFullAccount(
                    $preparedData['userData'],
                    $preparedData['configData'],
                    $user->id,
                    true,
                    false
                );

                if (is_array($create) && isset($create['status']) && $create['status'] === false) {
                    throw new \Exception($create['message'] ?? 'خطایی در صدور اکانت رخ داد.');
                }

                // به‌روزرسانی وضعیت سفارش
                $order->update([
                    'status' => 'approved',
                    'account_id' => $create->id ?? null
                ]);
            });

            // پیام موفقیت به ادمین و رفتن به سفارش بعدی
            $bot->sendMessage("✅ <b>سفارش با موفقیت تایید و اکانت صادر شد.</b>", parse_mode: 'HTML');


            if ($order->user->telegram_id) {
                $bot->sendMessage("🎉 <b>سفارش شما تایید شد!</b>\nاکانت شما با موفقیت ساخته شد. جهت مشاهده اطلاعات به بخش اکانت‌های من مراجعه کنید.", chat_id: $order->user->telegram_id, parse_mode: 'HTML');
            }


            $this->checkNextOrder($bot);

        } catch (\Exception $e) {
            Log::error("خطا در تایید سفارش تلگرام: " . $e->getMessage());
            $bot->sendMessage("❌ <b>خطا در پردازش سفارش:</b>\n" . $e->getMessage(), parse_mode: 'HTML');
        }
    }

    /**
     * منطق رد سفارش
     */
    private function rejectOrder(Nutgram $bot, StoreOrder $order)
    {
        $order->update(['status' => 'rejected']);
        $bot->answerCallbackQuery(text: '❌ سفارش با موفقیت رد شد.');

        $bot->sendMessage("❌ <b>سفارش رد شد.</b>", parse_mode: 'HTML');

        // پیام به خریدار
        if ($order->user->telegram_id) {
            $bot->sendMessage("❌ <b>سفارش شما رد شد.</b>\nمتاسفانه فیش/سفارش شما تایید نشد. در صورت نیاز با پشتیبانی تماس بگیرید.", chat_id: $order->user->telegram_id, parse_mode: 'HTML');
        }

        $this->checkNextOrder($bot);
    }

    /**
     * بررسی و نمایش سفارش بعدی در صف
     */
    private function checkNextOrder(Nutgram $bot)
    {
        $nextOrder = StoreOrder::with(['user', 'agent', 'group'])->where('status', 'pending')->oldest()->first();
        if ($nextOrder) {
            $this->showOrderToAdmin($bot, $nextOrder);
        } else {
            $bot->sendMessage("🎉 <b>عالی!</b>\nتمام سفارشات بررسی شدند و صف خالی است.", parse_mode: 'HTML');
        }
    }

    /**
     * ساخت کارت نمایش اطلاعات سفارش به ادمین
     */
    private function showOrderToAdmin(Nutgram $bot, StoreOrder $order)
    {
        $userName = $order->user->name ?? 'کاربر ناشناس';
        $agentName = $order->agent ? $order->agent->name : 'بدون نماینده (مستقیم)';
        $groupName = $order->group->name ?? 'پکیج نامشخص';
        $jalaliDate = Jalalian::forge($order->created_at)->format('Y/m/d - H:i');

        // فرض بر این است که قیمت را یا از گروه یا از خود سفارش می‌خوانید
        $price = number_format($order->price ?? ($order->group->price ?? 0));

        $caption = "🛒 <b>بررسی سفارش جدید فروشگاه</b>\n";
        $caption .= "➖➖➖➖➖➖➖➖➖➖\n";
        $caption .= "👤 <b>مشتری:</b> {$userName}\n";
        $caption .= "📱 <b>شماره تماس:</b> <code>{$order->phone}</code>\n";
        $caption .= "👨‍💼 <b>نماینده فروش:</b> {$agentName}\n";
        $caption .= "📦 <b>پکیج درخواستی:</b> {$groupName}\n";
        $caption .= "💰 <b>مبلغ پرداختی:</b> {$price} تومان\n";
        $caption .= "📅 <b>تاریخ ثبت:</b> {$jalaliDate}\n";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('✅ تایید و صدور اکانت', callback_data: "admin_handle_order:{$order->id}:approve"),
                InlineKeyboardButton::make('❌ رد سفارش', callback_data: "admin_handle_order:{$order->id}:reject")
            )
            ->addRow(InlineKeyboardButton::make('🏠 انصراف و بازگشت به منو', callback_data: 'back_to_admin_menu'));

        // اگر سفارش دارای تصویر فیش واریزی (attachment/receipt) بود:
        // (نام فیلد را بر اساس دیتابیس خود تنظیم کنید، مثلا receipt_file یا attachment)
        $receiptField = $order->attachment ?? $order->receipt_file ?? null;

        if ($receiptField && file_exists(storage_path('app/public/' . $receiptField))) {
            $filePath = storage_path('app/public/' . $receiptField);
            $bot->sendPhoto(InputFile::make($filePath), caption: $caption, parse_mode: 'HTML', reply_markup: $keyboard);
        } else {
            // اگر فایلی نداشت فقط متن را می‌فرستیم
            $bot->sendMessage($caption, parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }
}
