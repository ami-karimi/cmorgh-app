<?php

namespace App\Jobs;

use App\Models\Accounts;
use App\Models\SystemMaintenanceLog;
use App\Services\VpnManagerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteExpiredUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userIds;
    public $adminId;

    // افزایش Timeout به 30 دقیقه (1800 ثانیه)
    public $timeout = 1800;

    // تعداد دفعات تلاش مجدد
    public $tries = 3;

    public function __construct(array $userIds, int $adminId)
    {
        $this->userIds = $userIds;
        $this->adminId = $adminId;
    }

    public function handle()
    {
        $deleted = 0;
        $failed = 0;
        $errors = [];

        Log::info("شروع عملیات حذف کاربران منقضی", ['count' => count($this->userIds)]);

        // پردازش تکه‌تکه (هر بار ۱۰۰ کاربر)
        $chunkSize = 100;
        $chunks = array_chunk($this->userIds, $chunkSize);

        foreach ($chunks as $index => $chunk) {
            Log::info("پردازش تکه شماره " . ($index + 1) . " با " . count($chunk) . " کاربر");

            foreach ($chunk as $userId) {
                try {
                    $account = Accounts::find($userId);
                    if (!$account) {
                        Log::warning("اکانت با ID {$userId} یافت نشد.");
                        $failed++;
                        continue;
                    }

                    Log::info("در حال حذف اکانت: " . $account->username);

                    $result = VpnManagerService::deleteAccount($account, true);

                    if (!$result['status']) {
                        $failed++;
                        $errorMsg = "حذف اکانت {$account->username} ناموفق: " . ($result['message'] ?? 'خطای ناشناخته');
                        $errors[] = $errorMsg;
                        Log::error($errorMsg);
                    } else {
                        $deleted++;
                        Log::info("اکانت {$account->username} با موفقیت حذف شد.");
                    }

                } catch (\Exception $e) {
                    $failed++;
                    $errorMsg = "خطا در حذف کاربر {$userId}: " . $e->getMessage();
                    $errors[] = $errorMsg;
                    Log::error($errorMsg);
                    Log::error($e->getTraceAsString());
                }
            }

            // بعد از هر تکه، یک تاخیر کوتاه برای جلوگیری از فشار زیاد روی سرور
            if ($index < count($chunks) - 1) {
                sleep(2);
            }
        }

        $status = $failed > 0 ? ($deleted > 0 ? 'partial' : 'failed') : 'success';
        $message = "{$deleted} کاربر حذف شد، {$failed} کاربر با خطا مواجه شد.";

        if (!empty($errors)) {
            $message .= " خطاها: " . implode(' | ', array_slice($errors, 0, 5));
        }

        SystemMaintenanceLog::create([
            'admin_id' => $this->adminId,
            'action' => 'delete_expired_users_bulk',
            'target' => count($this->userIds) . ' users',
            'status' => $status,
            'message' => $message,
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        Log::info("عملیات حذف کاربران منقضی به پایان رسید", [
            'deleted' => $deleted,
            'failed' => $failed,
            'status' => $status
        ]);
    }

    public function failed(\Throwable $exception)
    {
        Log::error("Job DeleteExpiredUsersJob با خطا مواجه شد: " . $exception->getMessage());
        Log::error($exception->getTraceAsString());

        SystemMaintenanceLog::create([
            'admin_id' => $this->adminId,
            'action' => 'delete_expired_users_bulk',
            'target' => count($this->userIds) . ' users',
            'status' => 'failed',
            'message' => 'Job با خطا مواجه شد: ' . $exception->getMessage(),
            'started_at' => now(),
            'finished_at' => now(),
        ]);
    }
}
