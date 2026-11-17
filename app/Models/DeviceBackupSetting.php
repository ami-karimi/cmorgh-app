<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class DeviceBackupSetting extends Model
{
    protected $fillable = [
        'device_id',
        'enabled',
        'frequency',
        'run_at',
        'weekday',
        'day_of_month',
        'retention_days',
        'encrypt',
        'next_run_at',
        'last_run_at',
        'last_status',
        'last_error',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'encrypt' => 'boolean',
        'weekday' => 'integer',
        'day_of_month' => 'integer',
        'retention_days' => 'integer',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
    ];

    public const FREQUENCIES = [
        'every_6_hours' => 'هر ۶ ساعت',
        'every_12_hours' => 'هر ۱۲ ساعت',
        'daily' => 'روزانه',
        'weekly' => 'هفتگی',
        'monthly' => 'ماهانه',
    ];

    public const WEEKDAYS = [
        6 => 'شنبه',
        0 => 'یکشنبه',
        1 => 'دوشنبه',
        2 => 'سه‌شنبه',
        3 => 'چهارشنبه',
        4 => 'پنجشنبه',
        5 => 'جمعه',
    ];

    public static function for(int $deviceId): self
    {
        $setting = static::query()->firstOrCreate(
            ['device_id' => $deviceId],
            [
                'enabled' => true,
                'frequency' => 'daily',
                'run_at' => '03:00',
            ]
        );

        if (! $setting->next_run_at && $setting->enabled) {
            $setting->update([
                'next_run_at' => $setting->calculateNextRun(),
            ]);

            $setting->refresh();
        }

        return $setting;
    }

    public function calculateNextRun(?CarbonInterface $from = null): Carbon
    {
        $from = $from ? Carbon::parse($from) : now();

        [$hour, $minute] = array_pad(
            array_map('intval', explode(':', $this->run_at ?: '03:00')),
            2,
            0
        );

        return match ($this->frequency) {
            'every_6_hours' => $from->copy()->addHours(6)->startOfHour(),

            'every_12_hours' => $from->copy()->addHours(12)->startOfHour(),

            'weekly' => $this->nextWeekly(
                $from,
                (int) ($this->weekday ?? 6),
                $hour,
                $minute
            ),

            'monthly' => $this->nextMonthly(
                $from,
                $hour,
                $minute
            ),

            default => $this->nextDaily(
                $from,
                $hour,
                $minute
            ),
        };
    }

    protected function nextDaily(
        Carbon $from,
        int $hour,
        int $minute
    ): Carbon {
        $candidate = $from->copy()->setTime($hour, $minute, 0);

        return $candidate->gt($from)
            ? $candidate
            : $candidate->addDay();
    }

    protected function nextWeekly(
        Carbon $from,
        int $weekday,
        int $hour,
        int $minute
    ): Carbon {
        $candidate = $from->copy()->setTime($hour, $minute, 0);

        for ($i = 0; $i < 14; $i++) {
            if (
                $candidate->dayOfWeek === $weekday &&
                $candidate->gt($from)
            ) {
                return $candidate;
            }

            $candidate->addDay();
        }

        return $candidate;
    }

    protected function nextMonthly(
        Carbon $from,
        int $hour,
        int $minute
    ): Carbon {
        $day = max(1, min(28, (int) ($this->day_of_month ?? 1)));

        $candidate = $from
            ->copy()
            ->setTime($hour, $minute, 0)
            ->day($day);

        if ($candidate->lte($from)) {
            $candidate = $from
                ->copy()
                ->addMonthNoOverflow()
                ->setTime($hour, $minute, 0)
                ->day($day);
        }

        return $candidate;
    }

    public function scheduleSummary(): string
    {
        return match ($this->frequency) {
            'every_6_hours' => 'هر ۶ ساعت',
            'every_12_hours' => 'هر ۱۲ ساعت',

            'weekly' => sprintf(
                'هفتگی · %s · %s',
                self::WEEKDAYS[$this->weekday ?? 6] ?? 'شنبه',
                $this->run_at
            ),

            'monthly' => sprintf(
                'ماهانه · روز %s · %s',
                $this->day_of_month ?? 1,
                $this->run_at
            ),

            default => 'روزانه · ' . ($this->run_at ?: '03:00'),
        };
    }
}
