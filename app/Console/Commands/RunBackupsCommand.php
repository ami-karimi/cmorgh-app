<?php

namespace App\Console\Commands;

use App\Jobs\BackupDatabaseJob;
use App\Jobs\BackupMikroTikJob;
use App\Models\BackupSetting;
use App\Services\Backup\BackupDeviceResolver;
use Illuminate\Console\Command;

class RunBackupsCommand extends Command
{
    protected $signature = 'backups:run
                            {--database : فقط بکاپ دیتابیس}
                            {--mikrotik : فقط بکاپ روترها}
                            {--force : حتی اگر بکاپ خودکار غیرفعال باشد}';

    protected $description = 'اجرای بکاپ خودکار از روترهای MikroTik و دیتابیس';

    public function handle(BackupDeviceResolver $resolver): int
    {
        $settings = BackupSetting::current();

        if (! $settings->enabled && ! $this->option('force')) {
            $this->warn('بکاپ خودکار غیرفعال است. با --force می‌توانی اجرا کنی.');

            return self::SUCCESS;
        }

        $onlyDatabase = $this->option('database');
        $onlyMikrotik = $this->option('mikrotik');
        $runBoth = ! $onlyDatabase && ! $onlyMikrotik;

        if (($runBoth || $onlyDatabase) && $settings->database_enabled) {
            BackupDatabaseJob::dispatch(now()->toDateTimeString())->onQueue('backups');
            $this->info('بکاپ دیتابیس در صف قرار گرفت.');
        }

        if (($runBoth || $onlyMikrotik) && $settings->mikrotik_enabled) {
            foreach ($resolver->all() as $device) {
                BackupMikroTikJob::dispatch($device->id, now()->toDateTimeString())
                    ->onQueue('backups');

                $this->info("بکاپ روتر «{$resolver->nameOf($device)}» در صف قرار گرفت.");
            }
        }

        return self::SUCCESS;
    }
}
