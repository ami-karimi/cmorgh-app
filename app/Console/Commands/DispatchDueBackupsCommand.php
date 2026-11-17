<?php

namespace App\Console\Commands;

use App\Jobs\BackupMikroTikJob;
use App\Models\Backup;
use App\Models\BackupSetting;
use App\Models\DeviceBackupSetting;
use App\Services\Backup\BackupDeviceResolver;
use Illuminate\Console\Command;

class DispatchDueBackupsCommand extends Command
{
    protected $signature = 'backups:dispatch-due {--dry-run : فقط نمایش بده، در صف نگذار}';

    protected $description = 'بررسی روترهایی که به زمان بکاپ رسیده‌اند و ارسال به صف';

    public function handle(BackupDeviceResolver $resolver): int
    {
        $global = BackupSetting::current();

        if (! $global->mikrotik_enabled) {
            $this->warn('بکاپ روترها در تنظیمات سراسری غیرفعال است.');

            return self::SUCCESS;
        }

        $deviceIds = $resolver->query()->pluck('id')->all();

        $due = DeviceBackupSetting::query()
            ->whereIn('device_id', $deviceIds)
            ->where('enabled', true)
            ->whereNotNull('next_run_at')
            ->where('next_run_at', '<=', now())
            ->get();

        if ($due->isEmpty()) {
            $this->info('روتری برای بکاپ در این زمان نیست.');

            return self::SUCCESS;
        }

        foreach ($due as $setting) {
            $hasRunning = Backup::query()
                ->where('device_id', $setting->device_id)
                ->whereIn('status', ['queued', 'running'])
                ->exists();

            if ($hasRunning) {
                $setting->update(['next_run_at' => now()->addMinutes(15)]);
                $this->line("روتر {$setting->device_id}: بکاپ در حال اجراست، ۱۵ دقیقه بعد دوباره بررسی می‌شود.");

                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("روتر {$setting->device_id}: آماده ارسال به صف.");
            } else {
                BackupMikroTikJob::dispatch($setting->device_id)->onQueue('backups');
                $this->info("روتر {$setting->device_id}: در صف قرار گرفت.");
            }

            $setting->update([
                'next_run_at' => $setting->calculateNextRun(),
            ]);
        }

        return self::SUCCESS;
    }
}
