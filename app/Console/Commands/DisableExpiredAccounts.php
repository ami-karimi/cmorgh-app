<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Accounts;
use App\Services\VpnManagerService; // اضافه کردن سرویس قدرتمند شما
use Carbon\Carbon;

class DisableExpiredAccounts extends Command
{
    // نامی که در کرون‌جاب یا ترمینال صدا می‌زنیم
    protected $signature = 'vpn:disable-expired';

    // توضیحات کامند
    protected $description = 'غیرفعال‌سازی خودکار اکانت‌های منقضی شده (وایرگارد، سیسکو و...) از طریق VpnManagerService';

    public function handle()
    {
        $this->info("شروع بررسی اکانت‌های منقضی شده...");

        $now = Carbon::now();

        $expiredAccounts = Accounts::where('is_enabled', 1)
            ->whereNotNull('expire_date')
            ->where('expire_date', '<', $now);

        $count = $expiredAccounts->count();

        if ($count === 0) {
            $this->info("هیچ اکانت منقضی شده‌ای برای غیرفعال‌سازی یافت نشد.");
            return;
        }

        $expiredAccounts->chunkById(50, function ($accounts) {
            foreach ($accounts as $account) {

                $success = VpnManagerService::toggleAccount($account, false);

                if ($success) {
                    $this->line("اکانت غیرفعال شد: {$account->username}");
                } else {
                    $this->error("خطا در غیرفعال‌سازی اکانت: {$account->username}");
                }
            }
        });

        $this->info("عملیات با موفقیت پایان یافت. تعداد {$count} اکانت مسدود شدند.");
    }
}
