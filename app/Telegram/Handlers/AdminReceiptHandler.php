<?php
namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\Financial;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Morilog\Jalali\Jalalian;

class AdminReceiptHandler
{
    public function startReview(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            try {
                $bot->answerCallbackQuery();
            } catch (\Exception $e) {}
        }

        $receipt = Financial::where('approved', 0)->where('type', 'plus')->whereNotNull('attachment')->oldest()->first();

        if (!$receipt) {
            // 🔴 ۲. پاسخ هوشمندانه بر اساس نوع دکمه
            if ($bot->isCallbackQuery()) {
                $bot->answerCallbackQuery(text: '🎉 هیچ فیش واریزی در انتظاری وجود ندارد!', show_alert: true);
            } else {
                $bot->sendMessage('🎉 هیچ فیش واریزی در انتظاری وجود ندارد!');
            }
            return;
        }

        $this->showReceiptToAdmin($bot, $receipt);
    }

    public function handle(Nutgram $bot, $id, $action)
    {
        // این متد همیشه از دکمه شیشه‌ای (تایید/رد زیر عکس فیش) صدا زده میشه
        if ($bot->isCallbackQuery()) {
            try {
                $bot->answerCallbackQuery();
            } catch (\Exception $e) {}
        }

        $receipt = Financial::find($id);
        if (!$receipt || $receipt->approved != 0) {
            $bot->sendMessage('❌ فیش یافت نشد یا قبلاً بررسی شده است.');
            $this->checkNextReceipt($bot);
            return;
        }

        $status = ($action === 'approve') ? 1 : 2;
        $receipt->update(['approved' => $status]);
        $customer = User::find($receipt->for);
        if ($status === 1) {
            if ($customer) {
                if ($customer->telegram_id) {
                    try {
                        $bot->sendMessage(
                            "🎉 <b>فیش واریزی شما تایید شد!</b>\n\nمبلغ <b>" . number_format($receipt->price) . " تومان</b> به کیف پول شما اضافه گردید.",
                            chat_id: $customer->telegram_id,
                            parse_mode: 'HTML'
                        );
                    } catch (\Exception $e) {}
                }
            }
            $bot->sendMessage('✅ فیش با موفقیت تایید شد و کیف پول کاربر شارژ گردید.');
        } else {
            if ($customer && $customer->telegram_id) {
                try {
                    $bot->sendMessage(
                        "❌ <b>فیش واریزی شما رد شد!</b>\n\nمتاسفانه فیش واریزی شما به مبلغ " . number_format($receipt->price) . " تومان مورد تایید قرار نگرفت.",
                        chat_id: $customer->telegram_id,
                        parse_mode: 'HTML'
                    );
                } catch (\Exception $e) {}
            }
            $bot->sendMessage('❌ فیش رد شد.');
        }


        $this->checkNextReceipt($bot);
    }

    private function checkNextReceipt(Nutgram $bot)
    {
        $nextReceipt = Financial::where('approved', 0)->where('type', 'plus')->whereNotNull('attachment')->oldest()->first();
        if ($nextReceipt) {
            $this->showReceiptToAdmin($bot, $nextReceipt);
        } else {
            $keyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make('🏠 بازگشت به منوی اصلی', callback_data: 'back_to_admin_menu'));
            $bot->sendMessage("🎉 <b>تبریک!</b>\nتمام فیش‌های در انتظار بررسی شدند و صف خالی است.", parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }

    private function showReceiptToAdmin(Nutgram $bot, Financial $receipt)
    {
        $user = User::find($receipt->for);
        $userName = $user ? $user->name . " (@" . ($user->username ?? 'بدون_یوزرنیم') . ")" : 'کاربر نامشخص';
        $amount = number_format($receipt->price);
        $jalaliDate = Jalalian::forge($receipt->created_at)->format('Y/m/d - H:i');

        $caption = "🧾 <b>بررسی فیش واریزی جدید</b>\n➖➖➖➖➖➖➖➖➖➖\n";
        $caption .= "👤 <b>متقاضی:</b> {$userName}\n💰 <b>مبلغ فیش:</b> {$amount} تومان\n";
        $caption .= "📅 <b>تاریخ ثبت:</b> {$jalaliDate}\n";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('✅ تایید فیش', callback_data: "admin_handle_receipt:{$receipt->id}:approve"),
                InlineKeyboardButton::make('❌ رد فیش', callback_data: "admin_handle_receipt:{$receipt->id}:reject")
            )->addRow(InlineKeyboardButton::make('🏠 انصراف و بازگشت به منو', callback_data: 'back_to_admin_menu'));

        $filePath = public_path('storage/attachments/' . $receipt->attachment);

        if (file_exists($filePath)) {
            $bot->sendPhoto(\SergiX44\Nutgram\Telegram\Types\Internal\InputFile::make($filePath), caption: $caption, parse_mode: 'HTML', reply_markup: $keyboard);
        } else {
            $bot->sendMessage($caption . "\n⚠️ <i>تصویر فیش در سرور یافت نشد.</i>", parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }
}
