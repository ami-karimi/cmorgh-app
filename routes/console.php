<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\BackupDatabaseJob;
use App\Jobs\BackupMikroTikJob;
use App\Jobs\CleanupOldBackupsJob;
use App\Models\BackupSetting;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\ProcessBankMessages;

/*
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
*/

Schedule::command('vpn:sync-wg-usage')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/wireguard_sync.log'));

Schedule::command('orders:fulfill-paid')->everyMinute()->withoutOverlapping();

/*
Schedule::command('bank:sync-payment')
    ->everyMinute()
    ->withoutOverlapping();
*/


Schedule::command('vpn:sync-radius-usage')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/radius_sync.log'));

Schedule::command('vpn:clear-radius')
    ->everySixHours()
    ->withoutOverlapping();


Schedule::call(function () {
    $settings = BackupSetting::current();

    if (! $settings->enabled) {
        return;
    }

    if ($settings->database_enabled) {
        BackupDatabaseJob::dispatch(now()->toDateTimeString())->onQueue('backups');
    }

    if ($settings->mikrotik_enabled) {
        $modelClass = config('backup.device_model');
        $activeColumn = config('backup.device_active_column');

        $modelClass::query()
            ->when($activeColumn, fn ($q) => $q->where($activeColumn, true))
            ->pluck('id')
            ->each(fn ($id) => BackupMikroTikJob::dispatch($id, now()->toDateTimeString())
                ->onQueue('backups'));
    }
})
    ->dailyAt('03:00')
    ->name('daily-backups')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(new CleanupOldBackupsJob)
    ->dailyAt('04:30')
    ->name('cleanup-old-backups')
    ->withoutOverlapping()
    ->onOneServer();


Schedule::command('vpn:disable-expired')->dailyAt('00:00');
