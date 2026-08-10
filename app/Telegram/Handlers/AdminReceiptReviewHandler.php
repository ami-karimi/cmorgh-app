<?php
namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\Financial;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Morilog\Jalali\Jalalian;

class AdminReceiptReviewHandler
{
    public function startReview(Nutgram $bot)
    {
        $receipt = Financial::where('approved', 0)->where('type', 'plus')->whereNotNull('attachment')->oldest()->first();
        if (!$receipt) {
            $bot->answerCallbackQuery(text: '🎉 هیچ فیش واریزی در انتظاری وجود ندارد!', show_alert: true);
            return;
        }

        $bot->answerCallbackQuery();
        $this->showReceiptToAdmin($bot, $receipt);
    }

    public function handle(Nutgram $bot, $id, $action)
    {
        $receipt = Financial::find($id);
        if (!$receipt || $receipt->approved != 0) {
            $bot->answerCallbackQuery(text: '❌ فیش یافت نشد یا قبلاً بررسی شده است.', show_alert: true);
            $this->checkNextReceipt($bot);
            return;
        }

        $status = ($action === 'approve') ? 1 : 2;
        $receipt->update(['approved' => $status]);

        $bot->answerCallbackQuery(text: ($status === 1) ? '✅ فیش با موفقیت تایید شد.' : '❌ فیش رد شد.', show_alert: true);
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

        $filePath = storage_path('app/public/' . $receipt->attachment);
        if (file_exists($filePath)) {
            $bot->sendPhoto(\SergiX44\Nutgram\Telegram\Types\Internal\InputFile::make($filePath), caption: $caption, parse_mode: 'HTML', reply_markup: $keyboard);
        } else {
            $bot->sendMessage($caption . "\n⚠️ <i>تصویر فیش در سرور یافت نشد.</i>", parse_mode: 'HTML', reply_markup: $keyboard);
        }
    }
}
