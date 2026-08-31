<?php

namespace App\Jobs;

use App\Models\Accounts;
use App\Models\SystemMaintenanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\VpnManagerService;

class DeleteExpiredUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userIds;
    public $adminId;

    public function __construct(array $userIds, int $adminId)
    {
        $this->userIds = $userIds;
        $this->adminId = $adminId;
    }

    public function handle()
    {
        $deleted = 0;
        $failed = 0;

        foreach ($this->userIds as $userId) {
            try {
                $account = Accounts::find($userId);
                if (!$account) continue;

                $result = VpnManagerService::deleteAccount($account, true);

                if (!$result['status']) {
                    $failed++;
                    Log::error("حذف اکانت {$account->username} ناموفق: " . $result['message']);
                } else {
                    $deleted++;
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error("خطا در حذف کاربر {$userId}: " . $e->getMessage());
            }
        }

        SystemMaintenanceLog::create([
            'admin_id' => $this->adminId,
            'action' => 'delete_expired_users_bulk',
            'target' => count($this->userIds) . ' users',
            'status' => $failed > 0 ? 'partial' : 'success',
            'message' => "{$deleted} کاربر حذف شد، {$failed} کاربر با خطا مواجه شد.",
            'started_at' => now(),
            'finished_at' => now(),
        ]);
    }

    protected function deleteFromExternalServers($account)
    {
        // پیاده‌سازی حذف از سرورهای خارجی
    }
}
