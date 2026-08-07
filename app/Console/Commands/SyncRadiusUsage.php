<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Accounts;

class SyncRadiusUsage extends Command
{
    /**
     * نام و امضای کامند آرتیسان
     *
     * @var string
     */
    protected $signature = 'vpn:sync-radius-usage';

    /**
     * توضیح کامند
     *
     * @var string
     */
    protected $description = 'Synchronize L2TP/Cisco user usage volume from radacct table';

    /**
     * منطق اصلی اجرای کامند
     */
    public function handle()
    {
        $this->info('Starting to synchronize the consumption of radio users...');

        // گرفتن اکانت‌های حجمی L2TP که هنوز محدود نشده‌اند
        $data = Accounts::whereHas('group', function ($query) {
            $query->where('group_type', 'volume');
        })->where('service_group', 'l2tp_cisco')->where('limited', 0)->get();

        $updatedCount = 0;

        foreach ($data as $item) {
            $findUser = DB::table('radacct')
                ->where('saved', 0)
                ->whereNotNull('acctstoptime')
                ->where('username', $item->username)
                ->get();

            $download = $findUser->sum('acctoutputoctets');
            $upload = $findUser->sum('acctinputoctets');

            if ($findUser->count() && ($upload + $download) > 0) {
                $item->usage += $download + $upload;
                $item->download_usage += $download;
                $item->upload_usage += $upload;

                if ($item->max_usage > 0 && $item->usage >= $item->max_usage) {
                    $item->limited = 1;
                    $item->is_enabled = 0;
                }
                $item->save();

                DB::table('radacct')
                    ->where('username', $item->username)
                    ->where('saved', 0)
                    ->update(['saved' => 1]);

                $updatedCount++;
            }
        }

        $this->info("Synchronization was successful. Number  {$updatedCount} Accounts have been updated.");

        return Command::SUCCESS;
    }
}
