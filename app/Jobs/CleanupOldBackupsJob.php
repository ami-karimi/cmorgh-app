<?php

namespace App\Jobs;

use App\Models\Backup;
use App\Models\BackupSetting;
use App\Services\Backup\BackupStorageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanupOldBackupsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(BackupStorageService $storage): void
    {
        $settings = BackupSetting::current();
        $days = max(1, (int) $settings->retention_days);

        Backup::query()
            ->whereIn('status', ['success', 'failed'])
            ->where('created_at', '<', now()->subDays($days))
            ->orderBy('id')
            ->chunkById(100, function ($backups) use ($storage) {
                foreach ($backups as $backup) {
                    try {
                        $storage->delete($backup->path);
                    } catch (\Throwable) {
                        // اگر فایل روی هاست نبود، فقط رکورد را منقضی میکنیم
                    }

                    $backup->update([
                        'status' => 'expired',
                        'error_message' => null,
                    ]);
                }
            });
    }
}
