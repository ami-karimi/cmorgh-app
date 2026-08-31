<?php
// app/Services/System/Checkers/MikrotikIntegrityChecker.php

namespace App\Services\System\Checkers;

use App\Services\System\SystemHealthChecker;
use App\Models\Accounts;
use App\Models\Nas;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MikrotikIntegrityChecker extends SystemHealthChecker
{
    protected array $servers;

    public function __construct()
    {
        parent::__construct('mikrotik');
        $this->servers = Nas::where('is_enabled', 1)
            ->where('type', 'mikrotik')
            ->get()
            ->toArray();
    }

    protected function performCheck(): array
    {
        $issues = [];

        // دریافت لیست کاربران دیتابیس
        $dbUsers = Accounts::pluck('username')->toArray();

        // برای هر سرور MikroTik
        foreach ($this->servers as $server) {
            $remoteUsers = $this->getRemoteUsers($server);

            // پیدا کردن کاربران اضافی در سرور (orphan)
            $orphans = array_diff($remoteUsers, $dbUsers);
            foreach ($orphans as $username) {
                $issues[] = [
                    'username' => $username,
                    'server_id' => $server['id'],
                    'issue_type' => 'orphan',
                    'severity' => 'warning',
                    'details' => "کاربر در MikroTik وجود دارد اما در دیتابیس یافت نشد.",
                ];
            }

            // پیدا کردن کاربران گم‌شده در سرور (missing)
            $missing = array_diff($dbUsers, $remoteUsers);
            // محدود کنید: فقط کاربرانی که active هستند و در سرور مربوطه باید باشند
            $dbActiveUsers = Accounts::whereIn('username', $missing)
                ->where('is_enabled', 1)
                ->pluck('username')
                ->toArray();

            foreach ($dbActiveUsers as $username) {
                $issues[] = [
                    'username' => $username,
                    'server_id' => $server['id'],
                    'issue_type' => 'missing',
                    'severity' => 'critical',
                    'details' => "کاربر در دیتابیس فعال است اما در سرور MikroTik وجود ندارد.",
                ];
            }
        }

        return $issues;
    }

    protected function getRemoteUsers(array $server): array
    {
        try {
            // اینجا باید ارتباط با MikroTik API پیاده‌سازی شود
            // برای نمونه، یک پاسخ ساختگی برمی‌گردانیم
            // در پروژه واقعی، از کتابخانه RouterOS استفاده کنید
            return ['testuser1', 'testuser2']; // نمونه
        } catch (\Exception $e) {
            Log::error("Error fetching MikroTik users from server {$server['id']}: " . $e->getMessage());
            return [];
        }
    }
}
