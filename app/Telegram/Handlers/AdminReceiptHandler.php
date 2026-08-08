<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\Financial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminReceiptHandler
{
    /**
     * متد تایید فیش واریزی
     */
    public function approve(Nutgram $bot, $id)
    {
        $admin = $this->getAdmin($bot);
        if (!$admin) return;

        $receipt = Financial::find($id);

        if (!$receipt) {
            $bot->answerCallbackQuery(text: '❌ فیش یافت نشد یا قبلاً حذف شده است.', show_alert: true);
            return;
        }

        if ($receipt->approved == 1) {
            $bot->answerCallbackQuery(text: '⚠️ این فیش قبلاً تایید شده است.', show_alert: true);
            return;
        }

        try {
            DB::beginTransaction();

            $receipt->approved = 1;
            $receipt->save();
            $agent = User::find($receipt->for);


            DB::commit();

            $bot->answerCallbackQuery(text: '✅ فیش تایید شد و موجودی اضافه شد.');
            $bot->editMessageCaption(
                caption: $bot->callbackQuery()->message->caption . "\n\n✅ <b>تایید شده توسط:</b> {$admin->name}",
                parse_mode: 'HTML'
            );

            // ۴. ارسال پیام مژدگانی به نماینده
            if ($agent && $agent->telegram_id) {
                $bot->sendMessage(
                    "🎉 <b>فیش واریزی شما تایید شد!</b>\n\nمبلغ " . number_format($receipt->price) . " تومان به حساب کاربری شما افزوده شد.\nموجودی فعلی شما: " . number_format($agent->balance) . " تومان",
                    chat_id: $agent->telegram_id,
                    parse_mode: 'HTML'
                );
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطا در تایید فیش توسط ادمین در تلگرام: ' . $e->getMessage());
            $bot->answerCallbackQuery(text: '❌ خطا در تایید فیش! به لاگ‌ها مراجعه کنید.', show_alert: true);
        }
    }

    /**
     * متد رد کردن فیش واریزی
     */
    public function reject(Nutgram $bot, $id)
    {
        $admin = $this->getAdmin($bot);
        if (!$admin) return;

        $receipt = Financial::find($id);

        if (!$receipt) {
            $bot->answerCallbackQuery(text: '❌ فیش یافت نشد یا قبلاً حذف شده است.', show_alert: true);
            return;
        }

        if ($receipt->approved != 0) {
            $bot->answerCallbackQuery(text: '⚠️ این فیش قبلاً بررسی شده است.', show_alert: true);
            return;
        }

        // تغییر وضعیت به رد شده
        $receipt->approved = -1;
        $receipt->save();

        $bot->answerCallbackQuery(text: '❌ فیش رد شد.');

        // آپدیت پیام ادمین (حذف دکمه‌ها و افزودن وضعیت)
        $bot->editMessageCaption(
            caption: $bot->callbackQuery()->message->caption . "\n\n❌ <b>رد شده توسط:</b> {$admin->name}",
            parse_mode: 'HTML'
        );

        // اطلاع‌رسانی به نماینده
        $agent = User::find($receipt->for);
        if ($agent && $agent->telegram_id) {
            $bot->sendMessage(
                "❌ <b>فیش واریزی شما رد شد!</b>\n\nفیش شما به مبلغ " . number_format($receipt->price) . " تومان توسط مدیریت تایید نشد. در صورت نیاز با پشتیبانی در تماس باشید.",
                chat_id: $agent->telegram_id,
                parse_mode: 'HTML'
            );
        }
    }

    /**
     * متد کمکی برای بررسی دسترسی ادمین
     */
    private function getAdmin(Nutgram $bot): ?User
    {
        $admin = User::where('telegram_id', $bot->userId())->first();

        if (!$admin || !in_array($admin->role, ['admin', 'manager'])) {
            $bot->answerCallbackQuery(text: '⛔ شما دسترسی لازم را ندارید.', show_alert: true);
            return null;
        }

        return $admin;
    }
}
