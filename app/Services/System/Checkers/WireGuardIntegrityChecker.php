<?php

namespace App\Services\System\Checkers;

use App\Services\System\SystemHealthChecker;
use App\Models\Accounts;
use App\Models\WireGuardUsers;
use App\Models\Nas;
use App\Services\WireguardService;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class WireGuardIntegrityChecker extends SystemHealthChecker
{
    protected Collection $servers;

    public function __construct()
    {
        parent::__construct('wireguard');
        $this->servers = Nas::where('is_enabled', 1)
            ->supportsProtocol('wireguard')
            ->get();
    }

    protected function performCheck(): array
    {
        $issues = [];

        // دریافت همه کاربران WireGuard با اکانت‌های مرتبط
        $wgUsers = WireGuardUsers::with(['server', 'account'])
            ->whereHas('server', function($q) {
                $q->where('is_enabled', 1)->supportsProtocol('wireguard');
            })
            ->get();

        $dbUsersByServer = $wgUsers->groupBy('server_id');

        foreach ($this->servers as $server) {
            $serverId = $server->id;
            $serverName = $server->name;

            $remotePeers = $this->getRemotePeers($server);

            $dbProfileNames = $dbUsersByServer->has($serverId)
                ? $dbUsersByServer[$serverId]->pluck('profile_name')->toArray()
                : [];

            // ۱. بررسی Orphan (Peer در سرور، اما در دیتابیس نیست)
            $orphanPeers = array_diff($remotePeers, $dbProfileNames);
            foreach ($orphanPeers as $profileName) {
                $issues[] = [
                    'username' => $profileName,
                    'server_id' => $serverId,
                    'issue_type' => 'orphan',
                    'severity' => 'warning',
                    'details' => "Peer {$profileName} در سرور {$serverName} وجود دارد اما در دیتابیس یافت نشد.",
                    'action' => 'delete_orphan'
                ];
            }

            // ۲. بررسی Missing و وضعیت اکانت‌ها برای هر Peer در دیتابیس
            $serverWgUsers = $dbUsersByServer->get($serverId, collect());

            foreach ($serverWgUsers as $wgUser) {
                $profileName = $wgUser->profile_name;
                $account = $wgUser->account;

                // اگر Peer در سرور وجود نداشته باشد
                if (!in_array($profileName, $remotePeers)) {
                    // اگر اکانت فعال باشد => critical missing
                    if ($account && $account->is_enabled) {
                        $issues[] = [
                            'username' => $profileName,
                            'server_id' => $serverId,
                            'issue_type' => 'missing',
                            'severity' => 'critical',
                            'details' => "اکانت {$profileName} فعال است اما Peer در سرور {$serverName} یافت نشد.",
                            'action' => 'recreate_peer'
                        ];
                    } else {
                        // اگر اکانت غیرفعال یا منقضی باشد => info
                        $status = $account ? ($account->is_enabled ? 'فعال' : 'غیرفعال') : 'ناموجود';
                        $issues[] = [
                            'username' => $profileName,
                            'server_id' => $serverId,
                            'issue_type' => 'missing',
                            'severity' => 'info',
                            'details' => "Peer {$profileName} در سرور {$serverName} یافت نشد. وضعیت اکانت: {$status}.",
                            'action' => 'recreate_peer'
                        ];
                    }
                    continue;
                }

                // ۳. Peer وجود دارد، بررسی وضعیت اکانت
                if (!$account) {
                    // این حالت نباید رخ دهد چون WireGuardUsers به account متصل است، اما احتیاطاً
                    $issues[] = [
                        'username' => $profileName,
                        'server_id' => $serverId,
                        'issue_type' => 'orphan',
                        'severity' => 'warning',
                        'details' => "Peer {$profileName} در دیتابیس وجود دارد اما اکانت مرتبط یافت نشد.",
                        'action' => 'delete_orphan'
                    ];
                    continue;
                }

                // ۳-۱. بررسی تطابق وضعیت فعال/غیرفعال
                $isAccountEnabled = (bool) $account->is_enabled;
                $isPeerEnabled = $this->getPeerStatus($server, $profileName);

                if ($isPeerEnabled !== null && $isPeerEnabled !== $isAccountEnabled) {
                    $issues[] = [
                        'username' => $profileName,
                        'server_id' => $serverId,
                        'issue_type' => 'status_mismatch',
                        'severity' => $isAccountEnabled ? 'critical' : 'warning',
                        'details' => "وضعیت Peer با اکانت مطابقت ندارد. اکانت: " . ($isAccountEnabled ? 'فعال' : 'غیرفعال') . "، Peer: " . ($isPeerEnabled ? 'فعال' : 'غیرفعال'),
                        'action' => 'sync_status'
                    ];
                }

                // ۳-۲. بررسی انقضا (اگر اکانت منقضی شده باشد)
                if ($account->expire_date && \Carbon\Carbon::parse($account->expire_date)->isPast()) {
                    $days = \Carbon\Carbon::parse($account->expire_date)->diffInDays(now());
                    $issues[] = [
                        'username' => $profileName,
                        'server_id' => $serverId,
                        'issue_type' => 'expired',
                        'severity' => 'warning',
                        'details' => "اکانت {$profileName} از {$days} روز پیش منقضی شده است اما Peer در سرور باقی مانده است.",
                        'action' => 'disable_peer'
                    ];
                }

                // ۳-۳. بررسی تطابق محدودیت سرعت (config_limit)
                $expectedSpeed = $account->group->mikrotik_speed ?? '80M/10M';
                $actualSpeed = $wgUser->config_limit ?? '';

                if ($actualSpeed !== $expectedSpeed) {
                    $issues[] = [
                        'username' => $profileName,
                        'server_id' => $serverId,
                        'issue_type' => 'speed_mismatch',
                        'severity' => 'info',
                        'details' => "محدودیت سرعت با گروه مطابقت ندارد. انتظار: {$expectedSpeed}، فعلی: {$actualSpeed}",
                        'action' => 'sync_speed'
                    ];
                }
            }
        }

        return $issues;
    }

    /**
     * دریافت لیست profile_name های موجود در سرور
     */
    protected function getRemotePeers(Nas $server): array
    {
        try {
            $wgService = new WireguardService($server);
            $peers = $wgService->getAllPeers();

            $profileNames = [];
            foreach ($peers as $peer) {
                if (isset($peer['comment']) && !empty($peer['comment'])) {
                    $profileNames[] = $peer['comment'];
                } elseif (isset($peer['name']) && !empty($peer['name'])) {
                    $profileNames[] = $peer['name'];
                }
            }

            return $profileNames;

        } catch (\Exception $e) {
            Log::error("خطا در دریافت Peerهای سرور {$server->name}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * دریافت وضعیت فعال/غیرفعال یک Peer از سرور
     */
    protected function getPeerStatus(Nas $server, string $profileName): ?bool
    {
        try {
            $wgService = new WireguardService($server);
            $peers = $wgService->getAllPeers();

            foreach ($peers as $peer) {
                $comment = $peer['comment'] ?? '';
                if ($comment === $profileName) {
                    // بررسی وضعیت disabled
                    if (isset($peer['disabled']) && $peer['disabled'] === 'true') {
                        return false;
                    }
                    return true;
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error("خطا در دریافت وضعیت Peer {$profileName}: " . $e->getMessage());
            return null;
        }
    }

    // =====================================================
    // متدهای عملیاتی برای رفع مشکلات
    // =====================================================

    public function deleteOrphanPeer(int $serverId, string $profileName): array
    {
        $server = Nas::find($serverId);
        if (!$server) {
            return ['status' => false, 'message' => "سرور با ID {$serverId} یافت نشد."];
        }

        try {
            $wgService = new WireguardService($server);
            $peers = $wgService->getAllPeers();
            $publicKey = null;
            foreach ($peers as $peer) {
                $comment = $peer['comment'] ?? '';
                if ($comment === $profileName) {
                    $publicKey = $peer['public-key'] ?? null;
                    break;
                }
            }

            if (!$publicKey) {
                return ['status' => false, 'message' => "Peer با نام {$profileName} در سرور یافت نشد."];
            }

            $result = $wgService->removeClient($publicKey);

            if ($result['status']) {
                Log::info("Peer Orphan {$profileName} از سرور {$server->name} حذف شد.");
                return ['status' => true, 'message' => "Peer با موفقیت حذف شد."];
            } else {
                return ['status' => false, 'message' => "خطا در حذف Peer: " . ($result['message'] ?? 'نامشخص')];
            }
        } catch (\Exception $e) {
            Log::error("خطا در حذف Peer Orphan {$profileName}: " . $e->getMessage());
            return ['status' => false, 'message' => "خطا: " . $e->getMessage()];
        }
    }

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
            $result = $this->deleteOrphanPeer($serverId, $issue->username);
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
            'message' => "{$deleted} Peer حذف شد، {$failed} Peer با خطا مواجه شد.",
            'errors' => $errors,
            'deleted' => $deleted,
            'failed' => $failed
        ];
    }

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
            'message' => "{$totalDeleted} Peer از تمام سرورها حذف شد، {$totalFailed} Peer با خطا مواجه شد.",
            'errors' => $allErrors
        ];
    }
}
