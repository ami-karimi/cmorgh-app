<?php

namespace App\Jobs;

use App\Models\Backup;
use App\Models\BackupSetting;
use App\Services\Backup\BackupStorageService;
use App\Services\Backup\DatabaseBackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Carbon\Carbon;

class BackupDatabaseJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 1800;

    public int $tries = 2;

    public int $backoff = 120;

    public function __construct(
        public ?string $runAt = null
    ) {
    }

    public function handle(
        DatabaseBackupService $database,
        BackupStorageService $storage,
    ): void {
        $settings = BackupSetting::current();

        $record = Backup::create([
            'type' => 'database',
            'device_id' => null,
            'device_name' => null,
            'disk' => $storage->diskName(),
            'folder' => null,
            'filename' => 'pending',
            'path' => 'pending',
            'status' => 'running',
            'started_at' => now(),
        ]);

        $localPath = null;

        try {
            /*
            |--------------------------------------------------------------------------
            | ساخت بکاپ دیتابیس روی سرور Laravel
            |--------------------------------------------------------------------------
            */

            $localPath = $database->create();

            /*
            |--------------------------------------------------------------------------
            | ساخت مسیر هاست دانلود
            |--------------------------------------------------------------------------
            */

            $remotePath = $storage->buildPath(
                'database',
                null,
                'sql.gz',
                $this->runAt
                    ? Carbon::parse($this->runAt)
                    : now()
            );

            /*
            |--------------------------------------------------------------------------
            | آپلود به هاست دانلود
            |--------------------------------------------------------------------------
            */

            $uploaded = $storage->upload(
                $localPath,
                $remotePath,
                (bool) $settings->encrypt_files
            );

            $record->update([
                'disk' => $storage->diskName(),
                'folder' => $uploaded['folder'],
                'filename' => $uploaded['filename'],
                'path' => $uploaded['path'],
                'size' => $uploaded['size'],
                'checksum' => $uploaded['checksum'],
                'encrypted' => $uploaded['encrypted'],
                'status' => 'success',
                'finished_at' => now(),
            ]);
        } catch (Throwable $e) {
            $record->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now(),
            ]);

            throw $e;
        } finally {
            /*
            |--------------------------------------------------------------------------
            | حذف فایل موقت از سرور Laravel
            |--------------------------------------------------------------------------
            */

            if (
                $localPath &&
                is_file($localPath)
            ) {
                @unlink($localPath);
            }
        }
    }

    public function failed(Throwable $e): void
    {
        Backup::query()
            ->where('type', 'database')
            ->where('status', 'running')
            ->latest('id')
            ->first()
            ?->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now(),
            ]);
    }
}
