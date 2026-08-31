<?php
// app/Services/System/Checkers/MikrotikIntegrityChecker.php

namespace App\Services\System\Checkers;

use App\Services\System\SystemHealthChecker;
use App\Models\Accounts;
use App\Models\Nas;
use App\Services\MikrotikApiService;
use Illuminate\Support\Facades\Log;

class MikrotikIntegrityChecker extends SystemHealthChecker
{
    protected $servers;

    public function __construct()
    {
        parent::__construct('mikrotik');
        $this->servers = Nas::where('is_enabled', 1)
            ->supportsProtocol('wireguard')
            ->get();
    }

    protected function performCheck(): array
    {
        $issues = [];

        // دریافت لیست کاربران دیتابیس (همه کاربران با پروتکل‌های radius مانند l2tp, openvpn)
        // اگر بخواهیم فقط کاربران radius را بررسی کنیم، می‌توانیم فیلتر اضافه کنیم
        $dbUsers = Accounts::whereIn('service_group', ['l2tp', 'l2tp_cisco', 'openvpn'])
            ->pluck('username')
            ->toArray();

        foreach ($this->servers as $server) {
            $remoteUsers = $this->getRemoteUsers($server);

            // پیدا کردن کاربران اضافی در سرور (orphan)
            $orphans = array_diff($remoteUsers, $dbUsers);
            foreach ($orphans as $username) {
                $issues[] = [
                    'username' => $username,
                    'server_id' => $server->id,
                    'issue_type' => 'orphan',
                    'severity' => 'warning',
                    'details' => "کاربر {$username} در MikroTik وجود دارد اما در دیتابیس یافت نشد.",
                    'action' => 'delete_orphan'
                ];
            }

            // پیدا کردن کاربران گم‌شده در سرور (missing)
            $missing = array_diff($dbUsers, $remoteUsers);
            $dbActiveUsers = Accounts::whereIn('username', $missing)
                ->where('is_enabled', 1)
                ->pluck('username')
                ->toArray();

            foreach ($dbActiveUsers as $username) {
                $issues[] = [
                    'username' => $username,
                    'server_id' => $server->id,
                    'issue_type' => 'missing',
                    'severity' => 'critical',
                    'details' => "کاربر {$username} در دیتابیس فعال است اما در سرور MikroTik وجود ندارد.",
                    'action' => 'recreate_user'
                ];
            }
        }

        return $issues;
    }

    /**
     * دریافت لیست کاربران از سرور MikroTik
     */
    protected function getRemoteUsers(Nas $server): array
    {
        try {
            $mikrotikService = new MikrotikApiService($server);
            $users = $mikrotikService->getAllUsers();

            // استخراج نام کاربری از آرایه برگشتی
            return array_column($users, 'name');

        } catch (\Exception $e) {
            Log::error("خطا در دریافت کاربران MikroTik از سرور {$server->name}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * حذف یک کاربر Orphan از سرور MikroTik
     */
    public function deleteOrphanUser(int $serverId, string $username): array
    {
        $server = Nas::find($serverId);
        if (!$server) {
            return ['status' => false, 'message' => "سرور با ID {$serverId} یافت نشد."];
        }

        try {
            $mikrotikService = new MikrotikService($server);
            $result = $mikrotikService->deleteUser($username);

            if ($result['status']) {
                Log::info("کاربر Orphan {$username} از سرور {$server->name} حذف شد.");
                return ['status' => true, 'message' => "کاربر با موفقیت حذف شد."];
            } else {
                return ['status' => false, 'message' => "خطا در حذف کاربر: " . ($result['message'] ?? 'نامشخص')];
            }
        } catch (\Exception $e) {
            Log::error("خطا در حذف کاربر Orphan {$username}: " . $e->getMessage());
            return ['status' => false, 'message' => "خطا: " . $e->getMessage()];
        }
    }

    /**
     * حذف تمام Orphan Users از یک سرور خاص
     */
    public function deleteAllOrphans(int $serverId): array
    {
        $server = Nas::find($serverId);
        if (!$server) {
            return ['status' => false, 'message' => "سرور با ID {$serverId} یافت نشد."];
        }

        $issues = $this->getOpenIssues()
            ->where('server_id', $serverId)
            ->where('issue_type', 'orphan')
            ->where('status', 'open');

        $deleted = 0;
        $failed = 0;
        $errors = [];

        foreach ($issues as $issue) {
            $result = $this->deleteOrphanUser($serverId, $issue->username);
            if ($result['status']) {
                $deleted++;
                $issue->update(['status' => 'resolved', 'resolved_at' => now()]);
            } else {
                $failed++;
                $errors[] = $issue->username . ': ' . $result['message'];
            }
        }

        return [
            'status' => true,
            'message' => "{$deleted} کاربر حذف شد، {$failed} کاربر با خطا مواجه شد.",
            'errors' => $errors,
            'deleted' => $deleted,
            'failed' => $failed
        ];
    }

    /**
     * حذف تمام Orphan Users از تمام سرورها
     */
    public function deleteAllOrphansAllServers(): array
    {
        $totalDeleted = 0;
        $totalFailed = 0;
        $allErrors = [];

        foreach ($this->servers as $server) {
            $result = $this->deleteAllOrphans($server->id);
            $totalDeleted += $result['deleted'] ?? 0;
            $totalFailed += $result['failed'] ?? 0;
            if (!empty($result['errors'])) {
                $allErrors = array_merge($allErrors, $result['errors']);
            }
        }

        return [
            'status' => true,
            'message' => "{$totalDeleted} کاربر از تمام سرورها حذف شد، {$totalFailed} کاربر با خطا مواجه شد.",
            'errors' => $allErrors
        ];
    }
}
