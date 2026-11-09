<?php

namespace App\Http\Resources\Pc;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class AccountsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // محاسبه حجم باقی‌مانده
        $trafficRemaining = null;
        if (!is_null($this->max_usage) && $this->max_usage > 0) {
            $trafficRemaining = max(0, $this->max_usage - ($this->usage ?? 0));
        }

        // تشخیص نوع سرویس
        $serviceType = $this->service_group ?? 'unknown';
        $isWireguard = ($serviceType === 'wireguard' ? true : false);

        // تاریخ‌ها
        $expirationDate = $this->formatDateToJalali($this->expire_date);
        $daysRemaining = $this->calculateDaysRemaining($this->expire_date);

        return [
            'id' => $this->id,
            'user_id' => $this->creator ?? $this->user_id,
            'username' => $this->username,
            'password' => $this->password,

            'type' => $this->normalizeServiceType($serviceType),

            'is_active' => (bool) $this->is_enabled,

            // تاریخ‌های شمسی
            'expiration_date' => $expirationDate,          // مثال: ۱۴۰۴/۰۶/۱۲
            'expiration_date_time' => $expirationDate . ' ' . $this->formatTime($this->expire_date), // ۱۴۰۴/۰۶/۱۲ ۲۳:۵۹
            'days_remaining' => $daysRemaining,             // مثال: ۲۴
            'is_expired' => $daysRemaining < 0 || $this->expired == 1,

            'traffic_remaining' => $trafficRemaining,
            'traffic_used' => $this->usage ?? 0,
            'traffic_limit' => $this->max_usage ?? 0,
            'traffic_percent' => $this->calculateTrafficPercent(),

            'service_group' => $this->service_group,
            'multi_login' => $this->multi_login,
            'is_online' => (bool) $this->is_online,
            'wireguard_config' => $isWireguard ? $this->getWireguardConfig() : false,

            'pivot' => $this->when($this->pivot, function () {
                return [
                    'created_at' => $this->formatDateToJalali($this->pivot->created_at),
                ];
            }),
        ];
    }

    private function getWireguardConfig()
    {
        $wgUser = $this->wireguardUser;

        if (!$wgUser) {
            return false;
        }

        if ($wgUser->profile_name) {
            return $this->readConfigFromFile($wgUser->profile_name);
        }

        return null;
    }



    private function readConfigFromFile(string $profilename)
    {
        $path = public_path("configs/$profilename.conf");

        if (!file_exists($path)) {
            return null;
        }

        return file_get_contents($path);
    }



    /**
     * تبدیل تاریخ میلادی به شمسی
     */
    private function formatDateToJalali($date): ?string
    {
        if (is_null($date)) {
            return null;
        }

        try {
            if ($date instanceof Carbon) {
                return Jalalian::fromCarbon($date)->format('Y/m/d');
            }

            if (is_string($date)) {
                return Jalalian::fromCarbon(Carbon::parse($date))->format('Y/m/d');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * فرمت ساعت (در صورت نیاز)
     */
    private function formatTime($date): ?string
    {
        if (is_null($date)) {
            return null;
        }

        try {
            if ($date instanceof Carbon) {
                return $date->format('H:i');
            }

            if (is_string($date)) {
                return Carbon::parse($date)->format('H:i');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * محاسبه باقیمانده روز تا انقضا
     */
    private function calculateDaysRemaining($date): int
    {
        if (is_null($date)) {
            return -1; // نامحدود
        }

        try {
            $expireDate = $date instanceof Carbon ? $date : Carbon::parse($date);
            $now = Carbon::now();
            $days = $now->diffInDays($expireDate, false);

            return (int) $days;
        } catch (\Exception $e) {
            return -1;
        }
    }

    /**
     * نرمال‌سازی نوع سرویس
     */
    private function normalizeServiceType(string $serviceGroup): string
    {
        $map = [
            'l2tp_cisco' => 'l2tp',
            'openvpn' => 'openvpn',
            'wireguard' => 'wireguard',
            'v2ray' => 'v2ray',
        ];

        foreach ($map as $key => $value) {
            if (str_contains(strtolower($serviceGroup), $key)) {
                return $value;
            }
        }

        return $serviceGroup;
    }

    /**
     * محاسبه درصد مصرف ترافیک
     */
    private function calculateTrafficPercent(): int
    {
        if (!$this->max_usage || $this->max_usage <= 0) {
            return 0;
        }
        $percent = ($this->usage / $this->max_usage) * 100;
        return (int) round(min($percent, 100));
    }
}
