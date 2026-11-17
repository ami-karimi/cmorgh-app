<?php

namespace App\Jobs;

use App\Models\Backup;
use App\Models\BackupSetting;
use App\Models\DeviceBackupSetting;
use App\Services\Backup\BackupDeviceResolver;
use App\Services\Backup\BackupStorageService;
use App\Services\Backup\MikroTikBackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Throwable;

class BackupMikroTikJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 900;

    public int $backoff = 60;

    public function __construct(
        public int $deviceId,
        public ?string $runAt = null,
        public bool $manual = false,
    ) {}

    public function handle(
        BackupDeviceResolver $resolver,
        MikroTikBackupService $mikrotik,
        BackupStorageService $storage,
    ): void {
        $device = $resolver->query()->findOrFail(
            $this->deviceId
        );

        $global = BackupSetting::current();

        $setting = DeviceBackupSetting::query()
            ->where('device_id', $device->id)
            ->first();

        if (
            ! $this->manual &&
            $setting &&
            ! $setting->enabled
        ) {
            return;
        }

        $encrypt = $setting?->encrypt
            ?? $global->encrypt_files;

        $at = $this->runAt
            ? Carbon::parse($this->runAt)
            : now();

        $files = [];

        try {
            $files = $mikrotik->backup($device, $at);

            foreach ($files as $file) {
                $record = Backup::create([
                    'type' => $file['type'],
                    'device_id' => $device->id,
                    'device_name' => $resolver->nameOf($device),
                    'disk' => 'dynamic',
                    'folder' => null,
                    'filename' => 'pending',
                    'path' => 'pending',
                    'status' => 'running',
                    'started_at' => now(),
                ]);

                try {
                    $remotePath = $storage->buildPath(
                        'mikrotik',
                        $resolver->nameOf($device),
                        $file['extension'],
                        $at
                    );

                    $uploaded = $storage->upload(
                        $file['local_path'],
                        $remotePath,
                        (bool) $encrypt
                    );

                    $record->update([
                        'disk' => 'download-host',
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
                    if (is_file($file['local_path'])) {
                        @unlink($file['local_path']);
                    }
                }
            }

            $setting?->update([
                'last_run_at' => now(),
                'last_status' => 'success',
                'last_error' => null,
            ]);
        } catch (Throwable $e) {
            $setting?->update([
                'last_run_at' => now(),
                'last_status' => 'failed',
                'last_error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        Backup::query()
            ->where('device_id', $this->deviceId)
            ->where('status', 'running')
            ->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now(),
            ]);

        DeviceBackupSetting::query()
            ->where('device_id', $this->deviceId)
            ->update([
                'last_run_at' => now(),
                'last_status' => 'failed',
                'last_error' => $e->getMessage(),
            ]);
    }
}
