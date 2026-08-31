<?php
// app/Services/System/Checkers/WireGuardIntegrityChecker.php

namespace App\Services\System\Checkers;

use App\Services\System\SystemHealthChecker;
use App\Models\Accounts;
use App\Models\WireGuardUsers;
use Illuminate\Support\Facades\Storage;

class WireGuardIntegrityChecker extends SystemHealthChecker
{
    public function __construct()
    {
        parent::__construct('wireguard');
    }

    protected function performCheck(): array
    {
        $issues = [];

        // کاربران دیتابیس که WireGuard هستند
        $wgAccounts = Accounts::where('service_group', 'wireguard')
            ->where('is_enabled', 1)
            ->pluck('id')
            ->toArray();

        // تعداد کانفیگ‌های موجود در دیتابیس
        $wgConfigs = WireGuardUsers::whereIn('user_id', $wgAccounts)->get();

        // بررسی فایل‌های موجود در سرور (مسیر فرضی)
        $serverConfigs = $this->getServerConfigFiles();

        // برای هر کانفیگ دیتابیس، بررسی کن فایل وجود دارد؟
        foreach ($wgConfigs as $config) {
            $filename = $config->profile_name . '.conf';
            if (!in_array($filename, $serverConfigs)) {
                $issues[] = [
                    'username' => $config->profile_name,
                    'issue_type' => 'missing',
                    'severity' => 'critical',
                    'details' => "فایل کانفیگ WireGuard برای کاربر {$config->profile_name} در سرور یافت نشد.",
                ];
            }
        }

        // بررسی فایل‌های اضافی (orphan)
        $dbFilenames = $wgConfigs->pluck('profile_name')->map(function ($name) {
            return $name . '.conf';
        })->toArray();

        $orphanFiles = array_diff($serverConfigs, $dbFilenames);
        foreach ($orphanFiles as $file) {
            $username = str_replace('.conf', '', $file);
            $issues[] = [
                'username' => $username,
                'issue_type' => 'orphan',
                'severity' => 'warning',
                'details' => "فایل کانفیگ WireGuard برای کاربر {$username} در سرور وجود دارد اما در دیتابیس یافت نشد.",
            ];
        }

        return $issues;
    }

    protected function getServerConfigFiles(): array
    {
        // مسیر واقعی فایل‌های WireGuard
        $path = '/etc/wireguard/';
        if (!is_dir($path)) {
            return [];
        }
        $files = scandir($path);
        return array_filter($files, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'conf';
        });
    }
}
