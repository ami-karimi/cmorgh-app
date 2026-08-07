<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('vpn:sync-wg-usage')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/wireguard_sync.log'));


Schedule::command('vpn:sync-radius-usage')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/radius_sync.log'));

Schedule::command('vpn:clear-radius')
    ->everySixHours()
    ->withoutOverlapping();


Schedule::command('vpn:disable-expired')->dailyAt('00:00');
