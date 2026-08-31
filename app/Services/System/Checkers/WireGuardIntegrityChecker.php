<?php

namespace App\Services\System\Checkers;

use App\Services\System\SystemHealthChecker;
use App\Models\Accounts;
use App\Models\WireGuardUsers;
use App\Models\Nas;
use App\Services\WireguardService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WireGuardIntegrityChecker extends SystemHealthChecker
{
    protected  $servers;

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

        // ۱. دریافت لیست کاربران دیتابیس (همراه اطلاعات سرور)
        $dbUsers = WireGuardUsers::with('server')
            ->whereHas('server', function($q) {
                $q->where('is_enabled', 1) ->supportsProtocol('wireguard');
            })
            ->get();

        // گروه‌بندی بر اساس server_id
        $dbUsersByServer = $dbUsers->groupBy('server_id');

        foreach ($this->servers as $server) {
            $serverId = $server['id'];
            $serverName = $server['name'];

            // دریافت Peerهای موجود در سرور
            $remotePeers = $this->getRemotePeers($server);

            // دریافت نام‌های profile_name از دیتابیس برای این سرور
            $dbProfileNames = $dbUsersByServer->has($serverId)
                ? $dbUsersByServer[$serverId]->pluck('profile_name')->toArray()
                : [];

            // پیدا کردن Orphan Peers (در سرور وجود دارند، در دیتابیس نیستند)
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

            // پیدا کردن Missing Peers (در دیتابیس هستند، در سرور نیستند)
            $missingPeers = array_diff($dbProfileNames, $remotePeers);
            foreach ($missingPeers as $profileName) {
                $issues[] = [
                    'username' => $profileName,
                    'server_id' => $serverId,
                    'issue_type' => 'missing',
                    'severity' => 'critical',
                    'details' => "کاربر {$profileName} در دیتابیس وجود دارد اما در سرور {$serverName} یافت نشد.",
                    'action' => 'recreate_peer'
                ];
            }
        }

        return $issues;
    }

    /**
     * دریافت لیست profile_name های موجود در سرور
     */
    protected function getRemotePeers( $server): array
    {
        try {
            $wgService = new WireguardService($server);
            $peers = $wgService->getAllPeers();

            $profileNames = [];
            foreach ($peers as $peer) {
                // در MikroTik، profile_name معمولاً در comment ذخیره می‌شود
                if (isset($peer['comment']) && !empty($peer['comment'])) {
                    $profileNames[] = $peer['comment'];
                } elseif (isset($peer['name']) && !empty($peer['name'])) {
                    $profileNames[] = $peer['name'];
                }
            }

            return $profileNames;

        } catch (\Exception $e) {
            Log::error("خطا در دریافت Peerهای سرور {$server['name']}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * حذف یک Peer Orphan از سرور با profile_name
     */
    public function deleteOrphanPeer(int $serverId, string $profileName): array
    {
        $server = Nas::find($serverId);
        if (!$server) {
            return ['status' => false, 'message' => "سرور با ID {$serverId} یافت نشد."];
        }

        try {
            $wgService = new WireguardService($server);

            // ابتدا public_key را با profile_name پیدا می‌کنیم
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

            // حذف با public_key
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

    /**
     * حذف تمام Orphan Peers از یک سرور خاص
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
            'errors' => $errors
        ];
    }

    /**
     * حذف تمام Orphan Peers از تمام سرورها
     */
    public function deleteAllOrphansAllServers(): array
    {
        $totalDeleted = 0;
        $totalFailed = 0;
        $allErrors = [];

        foreach ($this->servers as $server) {
            $result = $this->deleteAllOrphans($server['id']);
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
