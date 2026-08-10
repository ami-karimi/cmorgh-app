<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Models\Accounts;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AgentAccountActionHandler
{
    /**
     * تغییر وضعیت (فعال/غیرفعال) اکانت
     */
    public function toggleStatus(Nutgram $bot, $id)
    {
        $agent = User::where('telegram_id', $bot->userId())->first();
        $account = Accounts::find($id);

        if (!$this->hasAccess($agent, $account)) {
            $bot->answerCallbackQuery(text: '❌ شما به این اکانت دسترسی ندارید.', show_alert: true);
            return;


        $success = \App\Services\VpnManagerService::toggleAccount($account);
        if (!$success) {
            $bot->answerCallbackQuery(text: '❌ خطا در ارتباط با سرور. وضعیت تغییر نکرد!', show_alert: true);
            return;
        }
        $account->refresh();

        $status = $account->is_enabled ? 'فعال' : 'مسدود';
        $bot->answerCallbackQuery(text: "✅ اکانت با موفقیت {$status} شد.", show_alert: true);

        \App\Telegram\Services\BotMenuService::renderAccountCardAgent($bot, $account, isEdit: true);
    }

    /**
     * تمدید اکانت (شارژ مجدد)
     */
    public function renewAccount(Nutgram $bot, $id)
    {
        $agent = User::where('telegram_id', $bot->userId())->first();
        $account = Accounts::find($id);

        if (!$this->hasAccess($agent, $account)) {
            $bot->answerCallbackQuery(text: '❌ شما به این اکانت دسترسی ندارید.', show_alert: true);
            return;
        }


        $bot->answerCallbackQuery(text: '⏳ این بخش در حال توسعه است...', show_alert: true);
    }

    /**
     * متد کمکی برای بررسی دسترسی نماینده به اکانت
     */
    private function hasAccess(?User $agent, ?Accounts $account): bool
    {
        if (!$agent || !$account) return false;
        if ($account->creatorUser && $account->creatorUser->creator == $agent->id) {
            return true;
        }

        return false;
    }
}
