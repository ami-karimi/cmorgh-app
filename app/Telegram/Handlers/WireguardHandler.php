<?php
namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use App\Models\Accounts;
use App\Models\WireGuardUsers;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;

class WireguardHandler
{
    public function downloadConf(Nutgram $bot, $id)
    {
        $account = Accounts::find($id);
        if (!$account || $account->service_group !== 'wireguard') return;

        $wgUser = WireGuardUsers::where('user_id', $account->id)->first();
        if (!$wgUser) {
            $bot->answerCallbackQuery(text: '❌ پروفایل یافت نشد.', show_alert: true);
            return;
        }

        $bot->answerCallbackQuery(text: '⏳ در حال ارسال فایل...');
        $confPath = public_path("configs/{$wgUser->profile_name}.conf");

        if (file_exists($confPath)) {
            $bot->sendDocument(InputFile::make($confPath, filename: "{$wgUser->profile_name}.conf"), caption: "📄 <b>فایل کانفیگ وایرگارد</b>", parse_mode: 'HTML');
        } else {
            $bot->sendMessage("⚠️ فایل یافت نشد.");
        }
    }

    public function downloadQr(Nutgram $bot, $id)
    {
        $account = Accounts::find($id);
        if (!$account || $account->service_group !== 'wireguard') return;

        $wgUser = WireGuardUsers::where('user_id', $account->id)->first();
        if (!$wgUser) return;

        $bot->answerCallbackQuery(text: '⏳ در حال ارسال QR Code...');
        $qrPath = public_path("configs/{$wgUser->profile_name}.png");

        if (file_exists($qrPath)) {
            $bot->sendPhoto(InputFile::make($qrPath), caption: "📱 <b>اسکن جهت اتصال سریع</b>", parse_mode: 'HTML');
        } else {
            $bot->sendMessage("⚠️ تصویر QR یافت نشد.");
        }
    }
}
