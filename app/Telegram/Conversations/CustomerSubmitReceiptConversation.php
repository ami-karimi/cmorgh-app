<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use App\Models\User;
use App\Models\Financial;
use App\Models\AgentBankAccount;
use App\Telegram\Services\BotMenuService;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;

class CustomerSubmitReceiptConversation extends Conversation
{
    protected $photoPath;
    protected $uplineId;

    /**
     * مرحله ۱: نمایش شماره حساب و درخواست عکس فیش
     */
    public function start(Nutgram $bot)
    {
        $user = User::where('telegram_id', $bot->userId())->first();

        $this->uplineId = $user->creator;
        if (!$this->uplineId) {
            $this->uplineId = User::whereIn('role', ['admin', 'manager'])->value('id');
        }

        $bankAccount = AgentBankAccount::where('user_id', $this->uplineId)->first();
        if (!$bankAccount) {
            $adminId = User::whereIn('role', ['admin', 'manager'])->value('id');
            $bankAccount = AgentBankAccount::where('user_id', $adminId)->first();
        }

        $text = "💰 <b>افزایش موجودی حساب</b>\n";
        $text .= "➖➖➖➖➖➖➖➖➖➖\n";
        $text .= "جهت افزایش موجودی، لطفاً مبلغ مورد نظر را به حساب زیر واریز نمایید:\n\n";

        if ($bankAccount) {
            if ($bankAccount->bank_name) $text .= "🏛 <b>بانک:</b> {$bankAccount->bank_name}\n";
            if ($bankAccount->account_name) $text .= "👤 <b>به نام:</b> {$bankAccount->account_name}\n";
            if ($bankAccount->card_number) $text .= "💳 <b>شماره کارت:</b> <code>{$bankAccount->card_number}</code>\n";
            if ($bankAccount->sheba_number) $text .= "🔢 <b>شماره شبا:</b> <code>{$bankAccount->sheba_number}</code>\n";
        } else {
            $text .= "⚠️ <i>اطلاعات حسابی ثبت نشده است. لطفاً با پشتیبانی تماس بگیرید.</i>\n";
        }

        $text .= "\n📸 <b>پس از واریز، لطفاً تصویر فیش واریزی خود را ارسال کنید:</b>\n";
        $text .= "💡 <i>نکته: می‌توانید مبلغ واریزی را در کپشن عکس بنویسید.</i>";

        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true)
            ->addRow(KeyboardButton::make('❌ انصراف از ثبت فیش'));

        $bot->sendMessage($text, parse_mode: 'HTML', reply_markup: $keyboard);
        $this->next('getReceiptPhoto');
    }

    /**
     * مرحله ۲: دریافت عکس (و بررسی هوشمند کپشن برای پیدا کردن مبلغ)
     */
    public function getReceiptPhoto(Nutgram $bot)
    {
        if ($bot->message()?->text === '❌ انصراف از ثبت فیش' || $bot->message()?->text === '/start') {
            $this->cancel($bot);
            return;
        }

        if (!$bot->message()?->photo) {
            $bot->sendMessage('⚠️ لطفاً فقط یک تصویر (عکس فیش) ارسال کنید:');
            return;
        }

        $bot->sendMessage('⏳ در حال پردازش فیش...');

        $photo = $bot->message()->photo;
        $fileId = end($photo)->file_id;

        $directory = public_path('storage/attachments/financial');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = '/financial/' . uniqid() . '.jpg';
        $bot->downloadFile($bot->getFile($fileId), public_path('storage/attachments' . $filename));
        $this->photoPath = $filename;

        // 🔴 بررسی هوشمند: آیا کاربر مبلغ رو توی کپشن عکس نوشته؟
        $caption = $bot->message()?->caption ?? '';
        $amountFromCaption = preg_replace('/\D/', '', $caption); // استخراج فقط اعداد از کپشن

        // اگر عددی بزرگتر از 1000 تومان تو کپشن بود، همون رو به عنوان مبلغ در نظر بگیر و تموم!
        if (!empty($amountFromCaption) && $amountFromCaption >= 1000) {
            $this->finalizeReceipt($bot, $amountFromCaption);
            return;
        }

        // اگر کپشن خالی بود یا عدد درستی نداشت، تازه الان ازش مبلغ رو می‌پرسیم
        $bot->sendMessage("✅ تصویر فیش دریافت شد.\n\n💰 حالا لطفاً <b>مبلغ واریزی</b> را به تومان (مثلاً 50000) تایپ کرده و بفرستید:");
        $this->next('getAmount');
    }

    /**
     * مرحله ۳: دریافت دستی مبلغ (فقط در صورتی اجرا میشه که کپشن عکس خالی بوده باشه)
     */
    public function getAmount(Nutgram $bot)
    {
        $text = $bot->message()?->text ?? '';

        if ($text === '❌ انصراف از ثبت فیش' || $text === '/start') {
            $this->cancel($bot);
            return;
        }

        $amount = preg_replace('/\D/', '', $text);

        if (empty($amount) || $amount < 1000) {
            $bot->sendMessage('⚠️ مبلغ نامعتبر است. لطفاً فقط یک عدد معتبر (حداقل 1000 تومان) وارد کنید:');
            return;
        }

        $this->finalizeReceipt($bot, $amount);
    }

    /**
     * متد نهایی: ثبت فیش در دیتابیس و ارسال نوتیفیکیشن برای ادمین/نماینده
     */
    private function finalizeReceipt(Nutgram $bot, $amount)
    {
        $user = User::where('telegram_id', $bot->userId())->first();

        $financial = Financial::create([
            'creator' => $user->id,
            'for' => $user->id,
            'type' => 'plus',
            'price' => $amount,
            'approved' => 0,
            'description' => 'شارژ حساب توسط مشتری',
            'attachment' => $this->photoPath,
        ]);

        $bot->sendMessage("🎉 <b>فیش شما با موفقیت ثبت شد!</b>\n\nپس از بررسی و تایید، مبلغ " . number_format($amount) . " تومان به کیف پول شما اضافه خواهد شد.", parse_mode: 'HTML');
        BotMenuService::showCustomerMenu($bot, $user, $bot->user()->name ?? 'کاربر');

        // ارسال برای مدیریت
        $this->notifyUplineAndAdmins($bot, $financial, $user, $amount);
        $this->end();
    }

    private function notifyUplineAndAdmins(Nutgram $bot, Financial $financial, User $user, $amount)
    {
        $upline = User::find($user->creator);

        $userName = $user->name . " (@" . ($user->username ?? 'بدون_یوزرنیم') . ")";
        $agentName = $upline ? $upline->name : 'مشتری مستقیم (بدون نماینده)';
        $amountFormatted = number_format($amount);
        $jalaliDate = \Morilog\Jalali\Jalalian::forge($financial->created_at)->format('Y/m/d - H:i');

        $caption = "🧾 <b>فیش واریزی جدید (مشتری)</b>\n";
        $caption .= "➖➖➖➖➖➖➖➖➖➖\n";
        $caption .= "👤 <b>متقاضی:</b> {$userName}\n";
        $caption .= "👨‍💼 <b>نماینده/سازنده:</b> {$agentName}\n";
        $caption .= "💰 <b>مبلغ فیش:</b> {$amountFormatted} تومان\n";
        $caption .= "📅 <b>تاریخ ثبت:</b> {$jalaliDate}\n";

        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('✅ تایید فیش', callback_data: "admin_handle_receipt:{$financial->id}:approve"),
                InlineKeyboardButton::make('❌ رد فیش', callback_data: "admin_handle_receipt:{$financial->id}:reject")
            );

        $photoPath = public_path('storage/attachments/' . $financial->attachment);

        if ($upline && $upline->telegram_id) {
            try { $bot->sendPhoto(InputFile::make($photoPath), caption: $caption, chat_id: $upline->telegram_id, parse_mode: 'HTML', reply_markup: $keyboard); } catch (\Exception $e) {}
        }

        $admins = User::whereIn('role', ['admin', 'manager'])->whereNotNull('telegram_id')->get();
        foreach ($admins as $admin) {
            try { $bot->sendPhoto(InputFile::make($photoPath), caption: $caption, chat_id: $admin->telegram_id, parse_mode: 'HTML', reply_markup: $keyboard); } catch (\Exception $e) {}
        }
    }

    public function cancel(Nutgram $bot)
    {
        $bot->sendMessage('❌ عملیات ثبت فیش لغو شد.');
        $user = User::where('telegram_id', $bot->userId())->first();
        BotMenuService::showCustomerMenu($bot, $user, $bot->user()->name ?? 'کاربر');
        $this->end();
    }
}
