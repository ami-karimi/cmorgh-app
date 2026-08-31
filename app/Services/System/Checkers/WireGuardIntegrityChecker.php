<?php

namespace App\Services\System\Checkers;

use App\Services\System\SystemHealthChecker;
use App\Models\Accounts;
use App\Models\WireGuardUsers;
use App\Models\Nas;
use App\Services\WireguardService;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

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

    public function syncSpeed(int $serverId, string $username): array
    {
        $server = Nas::find($serverId);
        if (!$server) {
            return ['status' => false, 'message' => "سرور با ID {$serverId} یافت نشد."];
        }

        try {
            $wgUser = WireGuardUsers::where('profile_name', $username)->first();
            if (!$wgUser) {
                return ['status' => false, 'message' => "کانفیگ برای {$username} یافت نشد."];
            }

            $account = $wgUser->account;
            if (!$account) {
                return ['status' => false, 'message' => "اکانت مرتبط یافت نشد."];
            }

            $expectedSpeed = $account->group->mikrotik_speed ?? '80M/10M';

            // به‌روزرسانی در دیتابیس
            $wgUser->config_limit = $expectedSpeed;
            $wgUser->save();

            // به‌روزرسانی در سرور
            $wgService = new WireguardService($server);
            $result = $wgService->updateClientSpeed($wgUser->user_ip, $username, $expectedSpeed);

            if (!$result['status']) {
                return ['status' => false, 'message' => "خطا در به‌روزرسانی سرعت در سرور: " . ($result['message'] ?? 'نامشخص')];
            }

            Log::info("سرعت Peer {$username} به {$expectedSpeed} همگام‌سازی شد.");
            $this->closeOpenIssues($username, auth()->id());

            return ['status' => true, 'message' => "سرعت با موفقیت همگام‌سازی شد."];
        } catch (\Exception $e) {
            Log::error("خطا در همگام‌سازی سرعت {$username}: " . $e->getMessage());
            return ['status' => false, 'message' => "خطا: " . $e->getMessage()];
        }
    }

    public function createAccountFromPeer(int $serverId, string $username): array
    {
        $server = Nas::find($serverId);
        if (!$server) {
            return ['status' => false, 'message' => "سرور با ID {$serverId} یافت نشد."];
        }

        try {
            // دریافت اطلاعات Peer از سرور
            $wgService = new WireguardService($server);
            $peers = $wgService->getAllPeers();

            $peerData = null;
            foreach ($peers as $peer) {
                if (($peer['comment'] ?? '') === $username) {
                    $peerData = $peer;
                    break;
                }
            }

            if (!$peerData) {
                return ['status' => false, 'message' => "Peer با نام {$username} در سرور یافت نشد."];
            }

            // ایجاد اکانت جدید
            $account = Accounts::create([
                'username' => $username,
                'password' => bin2hex(random_bytes(8)),
                'service_group' => 'wireguard',
                'creator' => auth()->id() ?? 1,
                'is_enabled' => 1,
                'expire_date' => now()->addDays(30),
            ]);

            // ایجاد رکورد WireGuardUsers
            WireGuardUsers::create([
                'user_id' => $account->id,
                'server_id' => $serverId,
                'profile_name' => $username,
                'user_ip' => $peerData['allowed-address'] ?? '',
                'public_key' => $peerData['public-key'] ?? '',
                'config_limit' => $peerData['comment'] ?? '80M/10M',
                'is_enabled' => 1,
            ]);

            Log::info("اکانت جدید از روی Peer {$username} ایجاد شد. Account ID: {$account->id}");
            $this->closeOpenIssues($username, auth()->id());

            return ['status' => true, 'message' => "اکانت با موفقیت ایجاد و به Peer متصل شد."];
        } catch (\Exception $e) {
            Log::error("خطا در ایجاد اکانت از روی Peer {$username}: " . $e->getMessage());
            return ['status' => false, 'message' => "خطا: " . $e->getMessage()];
        }
    }

// در WireGuardIntegrityChecker.php

    /**
     * دریافت لیست اورفان‌ها (نام‌های کاربری) به تفکیک هر سرور
     *
     * @return array ['server_id' => ['username1', 'username2', ...]]
     */
    public function getOrphansGroupedByServer(): array
    {
        $orphansByServer = [];

        // دریافت Issues باز از نوع اورفان
        $issues = $this->getOpenIssues()
            ->whereIn('issue_type', ['orphan_peer_only', 'orphan_full', 'orphan_peer_config', 'config_without_account'])
            ->where('status', 'open')
            ->get();

        foreach ($issues as $issue) {
            $serverId = $issue->server_id;
            if (!isset($orphansByServer[$serverId])) {
                $orphansByServer[$serverId] = [];
            }
            $orphansByServer[$serverId][] = $issue->username;
        }

        return $orphansByServer;
    }


    protected function performCheck(): array
    {
        $issues = [];
        $allUsers = collect();

        // =============================================
        // ۱. جمع‌آوری کاربران از Accounts (اکانت‌های WireGuard)
        // =============================================
        $dbAccounts = Accounts::where('service_group', 'wireguard')->get();
        foreach ($dbAccounts as $account) {
            $allUsers->put($account->username, [
                'user_id' => $account->id,
                'username' => $account->username,
                'has_account' => true,
                'account_data' => $account,
                'has_config' => false,
                'config_data' => null,
                'has_peer' => false,
                'peer_data' => null,
                'servers' => [],
                'peer_key' => null,
            ]);
        }

        // =============================================
        // ۲. جمع‌آوری کاربران از WireGuardUsers
        // =============================================
        $wgUsers = WireGuardUsers::with('server')->get();
        foreach ($wgUsers as $wg) {
            $username = ($wg->account ? $wg->account->username : $wg->profile_name);
            $publicKey = $wg->public_key;
            if ($allUsers->has($username)) {
                $user = $allUsers->get($username);
                $user['has_config'] = true;
                $user['config_data'] = $wg;
                $user['peer_key'] = $publicKey;
                $allUsers->put($username, $user);
            } else {
                $allUsers->put($username, [
                    'username' => $username,
                    'has_account' => false,
                    'account_data' => null,
                    'has_config' => true,
                    'config_data' => $wg,
                    'has_peer' => false,
                    'peer_data' => null,
                    'servers' => [],
                    'peer_key' => $publicKey,
                ]);
            }
        }

        // =============================================
        // ۳. جمع‌آوری Peerها از سرورها (با public_key)
        // =============================================
        foreach ($this->servers as $server) {
            $remotePeers = $this->getRemotePeers($server); // آرایه‌ای از اطلاعات کامل Peer

            foreach ($remotePeers as $peerInfo) {
                $comment = $peerInfo['comment'];
                $publicKey = $peerInfo['public_key'];

                // تلاش برای پیدا کردن کاربر با public_key (دقیق‌ترین روش)
                $foundUser = null;
                $foundUsername = null;

                // ابتدا با public_key جستجو کن (در wireguard_users)
                foreach ($allUsers as $username => $user) {
                    if (isset($user['peer_key']) && $user['peer_key'] === $publicKey) {
                        $foundUser = $user;
                        $foundUsername = $username;
                        break;
                    }
                }

                // اگر با public_key پیدا نشد، با comment (نام) جستجو کن
                if (!$foundUser && !empty($comment)) {
                    if ($allUsers->has($comment)) {
                        $foundUser = $allUsers->get($comment);
                        $foundUsername = $comment;
                    }
                }

                if ($foundUser) {
                    $foundUser['has_peer'] = true;
                    $foundUser['peer_data'] = [
                        'server_id' => $server->id,
                        'server_name' => $server->name,
                        'public_key' => $publicKey,
                        'comment' => $comment,
                    ];
                    $foundUser['servers'][] = $server->id;
                    $allUsers->put($foundUsername, $foundUser);
                } else {
                    // Peer جدیدی است که در دیتابیس وجود ندارد
                    $newUsername = !empty($comment) ? $comment : 'unknown_' . substr($publicKey, 0, 8);
                    $allUsers->put($newUsername, [
                        'username' => $newUsername,
                        'has_account' => false,
                        'account_data' => null,
                        'has_config' => false,
                        'config_data' => null,
                        'has_peer' => true,
                        'peer_data' => [
                            'server_id' => $server->id,
                            'server_name' => $server->name,
                            'public_key' => $publicKey,
                            'comment' => $comment,
                        ],
                        'servers' => [$server->id],
                        'peer_key' => $publicKey,
                    ]);
                }
            }
        }

        // =============================================
        // ۴. تحلیل وضعیت هر کاربر و تولید Issue
        // =============================================
        foreach ($allUsers as $user) {
            $username = $user['username'];
            $hasAccount = $user['has_account'];
            $hasConfig = $user['has_config'];
            $hasPeer = $user['has_peer'];
            $account = $user['account_data'];
            $wgUser = $user['config_data'];
            $serverId = $user['peer_data']['server_id'] ?? ($wgUser->server_id ?? null);

            // **منطق جدید تشخیص Issue**
            $issueType = $this->determineIssueTypeV2($hasAccount, $hasConfig, $hasPeer);
            if (!$issueType) {
                // همه چیز سالم است
                continue;
            }

            $details = $this->buildDetails($user);
            $severity = $this->getSeverity($issueType);
            $action = $this->getAction($issueType);

            $issues[] = [
                'username' => $username,
                'server_id' => $serverId ?? 0,
                'issue_type' => $issueType,
                'severity' => $severity,
                'details' => $details,
                'action' => $action,
                'has_account' => $hasAccount,
                'has_config' => $hasConfig,
                'has_peer' => $hasPeer,
                'account_status' => $account ? ($account->is_enabled ? 'فعال' : 'غیرفعال') : 'ندارد',
                'is_expired' => $account && $account->expire_date && Carbon::parse($account->expire_date)->isPast(),
            ];
        }

        return $this->deduplicateIssues($issues);
    }

    // در WireGuardIntegrityChecker.php

    /**
     * حذف Issues تکراری و انتخاب مهم‌ترین Issue برای هر کاربر
     *
     * @param array $issues لیست Issues تولید شده
     * @return array لیست Issues پالایش‌شده
     */
    protected function deduplicateIssues(array $issues): array
    {
        if (empty($issues)) {
            return [];
        }

        // گروه‌بندی بر اساس username و server_id
        $grouped = [];
        foreach ($issues as $issue) {
            $key = $issue['username'] . '|' . $issue['server_id'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }
            $grouped[$key][] = $issue;
        }

        $result = [];

        foreach ($grouped as $group) {
            // اگر فقط یک Issue وجود دارد، همان را انتخاب کن
            if (count($group) === 1) {
                $result[] = $group[0];
                continue;
            }

            // انتخاب مهم‌ترین Issue بر اساس اولویت
            $best = $this->selectBestIssue($group);
            if ($best) {
                $result[] = $best;
            }
        }

        return $result;
    }

    /**
     * انتخاب مهم‌ترین Issue از بین یک گروه
     * اولویت: critical > warning > info
     * در صورت برابری شدت، issue_type مهم‌تر را انتخاب کن
     */
    protected function selectBestIssue(array $issues): ?array
    {
        if (empty($issues)) {
            return null;
        }

        // ترتیب اولویت severity
        $severityOrder = ['critical' => 4, 'warning' => 3, 'info' => 2];

        // ترتیب اولویت issue_type (مهم‌ترها اول)
        $typeOrder = [
            'missing_peer' => 10,
            'orphan_full' => 9,
            'status_mismatch' => 8,
            'orphan_peer_config' => 7,
            'orphan_peer_only' => 6,
            'account_without_service' => 5,
            'config_without_account' => 4,
            'expired_account' => 3,
            'speed_mismatch' => 2,
            'healthy' => 1,
        ];

        $best = null;
        $bestScore = -1;

        foreach ($issues as $issue) {
            $severityScore = $severityOrder[$issue['severity']] ?? 0;
            $typeScore = $typeOrder[$issue['issue_type']] ?? 0;
            $totalScore = ($severityScore * 100) + $typeScore;

            if ($totalScore > $bestScore) {
                $bestScore = $totalScore;
                $best = $issue;
            }
        }

        return $best;
    }


    protected function determineIssueTypeV2(bool $hasAccount, bool $hasConfig, bool $hasPeer): ?string
    {
        // حالت ۱: همه چیز دارد (سالم)
        if ($hasAccount && $hasConfig && $hasPeer) {
            return 'healthy';
        }

        // حالت ۲: اکانت دارد، کانفیگ دارد، Peer ندارد
        if ($hasAccount && $hasConfig && !$hasPeer) {
            return 'missing_peer';
        }

        // حالت ۳: اکانت دارد، کانفیگ ندارد، Peer دارد (مهم!)
        if ($hasAccount && !$hasConfig && $hasPeer) {
            return 'orphan_peer_config'; // باید کانفیگ ایجاد شود
        }

        // حالت ۴: اکانت دارد، کانفیگ ندارد، Peer ندارد
        if ($hasAccount && !$hasConfig && !$hasPeer) {
            return 'account_without_service'; // باید کانفیگ و Peer ایجاد شود
        }

        // حالت ۵: اکانت ندارد، کانفیگ دارد، Peer دارد
        if (!$hasAccount && $hasConfig && $hasPeer) {
            return 'orphan_full'; // اورفان کامل (حذف شود)
        }

        // حالت ۶: اکانت ندارد، کانفیگ دارد، Peer ندارد (نادر)
        if (!$hasAccount && $hasConfig && !$hasPeer) {
            return 'config_without_account'; // کانفیگ بدون اکانت
        }

        // حالت ۷: اکانت ندارد، کانفیگ ندارد، Peer دارد
        if (!$hasAccount && !$hasConfig && $hasPeer) {
            return 'orphan_peer_only'; // فقط Peer اضافی
        }

        // حالت ۸: هیچ چیزی ندارد (نادیده گرفته می‌شود)
        return null;
    }

    /**
     * تعیین نوع Issue بر اساس ترکیب سه وضعیت
     */
    protected function determineIssueType(bool $hasAccount, bool $hasConfig, bool $hasPeer): ?string
    {
        // حالت ۱: همه چیز دارد (سالم)
        if ($hasAccount && $hasConfig && $hasPeer) {
            return 'healthy'; // هیچ اقدامی نیاز نیست
        }

        // حالت ۲: اکانت دارد، کانفیگ دارد، Peer ندارد
        if ($hasAccount && $hasConfig && !$hasPeer) {
            return 'missing_peer';
        }

        // حالت ۳: اکانت دارد، کانفیگ ندارد، Peer دارد
        if ($hasAccount && !$hasConfig && $hasPeer) {
            return 'orphan_peer_config';
        }

        // حالت ۴: اکانت دارد، کانفیگ ندارد، Peer ندارد
        if ($hasAccount && !$hasConfig && !$hasPeer) {
            return 'account_without_service';
        }

        // حالت ۵: اکانت ندارد، کانفیگ دارد، Peer دارد
        if (!$hasAccount && $hasConfig && $hasPeer) {
            return 'orphan_full';
        }

        // حالت ۶: اکانت ندارد، کانفیگ دارد، Peer ندارد (نادر)
        if (!$hasAccount && $hasConfig && !$hasPeer) {
            return 'config_without_account';
        }

        // حالت ۷: اکانت ندارد، کانفیگ ندارد، Peer دارد
        if (!$hasAccount && !$hasConfig && $hasPeer) {
            return 'orphan_peer_only';
        }

        // حالت ۸: هیچ چیزی ندارد (نادیده گرفته می‌شود)
        return null;
    }

    /**
     * ساخت توضیحات برای Issue
     */
    protected function buildDetails(array $user): string
    {
        $parts = [];
        $parts[] = $user['has_account'] ? '✅ اکانت دارد' : '❌ اکانت ندارد';
        $parts[] = $user['has_config'] ? '✅ کانفیگ دارد' : '❌ کانفیگ ندارد';
        $parts[] = $user['has_peer'] ? '✅ Peer دارد' : '❌ Peer ندارد';

        if ($user['has_account'] && $user['account_data']) {
            $account = $user['account_data'];
            $status = $account->is_enabled ? 'فعال' : 'غیرفعال';
            $parts[] = "وضعیت اکانت: {$status}";
            if ($account->expire_date) {
                $expire = Carbon::parse($account->expire_date);
                $parts[] = $expire->isPast() ? '⛔ منقضی شده' : "📅 تا {$expire->toDateString()}";
            }
        }

        if ($user['has_peer'] && isset($user['peer_data']['server_name'])) {
            $parts[] = "سرور: {$user['peer_data']['server_name']}";
        }

        return implode(' | ', $parts);
    }

    protected function getSeverity(string $issueType): string
    {
        $map = [
            'missing_peer' => 'critical',
            'orphan_peer_config' => 'warning',
            'account_without_service' => 'warning',
            'orphan_full' => 'critical',
            'config_without_account' => 'warning',
            'orphan_peer_only' => 'warning',
        ];
        return $map[$issueType] ?? 'info';
    }

    protected function getAction(string $issueType): string
    {
        $map = [
            'missing_peer' => 'recreate_peer',
            'orphan_peer_config' => 'create_config',
            'account_without_service' => 'create_config_and_peer',
            'orphan_full' => 'create_account_or_delete',
            'config_without_account' => 'create_account_or_delete',
            'orphan_peer_only' => 'create_account_and_config',
        ];
        return $map[$issueType] ?? 'review';
    }

    // =====================================================
    // متدهای کمکی
    // =====================================================

    protected function getRemotePeers(Nas $server): array
    {
        try {
            $wgService = new WireguardService($server);
            $peers = $wgService->getAllPeers();

            $peerData = [];
            foreach ($peers as $peer) {
                $comment = $peer['comment'] ?? '';
                $publicKey = $peer['public-key'] ?? '';
                if (empty($comment) && empty($publicKey)) {
                    continue;
                }
                $peerData[] = [
                    'comment' => $comment,
                    'public_key' => $publicKey,
                    'allowed_address' => $peer['allowed-address'] ?? '',
                    'disabled' => isset($peer['disabled']) && $peer['disabled'] === 'true',
                ];
            }
            return $peerData;
        } catch (\Exception $e) {
            Log::error("خطا در دریافت Peerهای سرور {$server->name}: " . $e->getMessage());
            return [];
        }
    }

    // =====================================================
    // متدهای عملیاتی (۳ دکمه اصلی)
    // =====================================================

    /**
     * دکمه ۱: ایجاد کانفیگ و Peer برای اکانت (account_without_service)
     */
    public function createConfigAndPeer(int $serverId, string $username): array
    {
        $server = Nas::find($serverId);
        if (!$server) {
            return ['status' => false, 'message' => "سرور با ID {$serverId} یافت نشد."];
        }

        try {
            $account = Accounts::where('username', $username)->where('service_group', 'wireguard')->first();
            if (!$account) {
                return ['status' => false, 'message' => "اکانت {$username} یافت نشد."];
            }

            // ایجاد Peer در سرور
            $wgService = new WireguardService($server);
            $speed = $account->group->mikrotik_speed ?? '80M/10M';
            $result = $wgService->createClient($username, $speed);

            if (!$result['status']) {
                return ['status' => false, 'message' => "خطا در ایجاد Peer: " . ($result['message'] ?? 'نامشخص')];
            }

            $data = $result['data'];

            // ایجاد رکورد WireGuardUsers
            WireGuardUsers::create([
                'user_id' => $account->id,
                'server_id' => $serverId,
                'profile_name' => $username,
                'user_ip' => $data['ip_address'] ?? '',
                'public_key' => $data['client_public_key'] ?? '',
                'config_limit' => $speed,
                'is_enabled' => 1,
            ]);

            Log::info("کانفیگ و Peer برای اکانت {$username} ایجاد شد.");
            $this->closeOpenIssues($username, auth()->id());

            return ['status' => true, 'message' => "کانفیگ و Peer با موفقیت ایجاد شد."];
        } catch (\Exception $e) {
            Log::error("خطا در ایجاد کانفیگ برای {$username}: " . $e->getMessage());
            return ['status' => false, 'message' => "خطا: " . $e->getMessage()];
        }
    }

    /**
     * دکمه ۲: بازسازی Peer (missing_peer)
     */
    public function recreatePeer(int $serverId, string $username): array
    {
        $server = Nas::find($serverId);
        if (!$server) {
            return ['status' => false, 'message' => "سرور با ID {$serverId} یافت نشد."];
        }

        try {
            $wgUser = WireGuardUsers::where('profile_name', $username)->first();
            if (!$wgUser) {
                return ['status' => false, 'message' => "کانفیگ برای {$username} یافت نشد."];
            }

            $account = $wgUser->account;
            if (!$account) {
                return ['status' => false, 'message' => "اکانت مرتبط یافت نشد."];
            }

            $wgService = new WireguardService($server);

            // حذف Peer قدیمی
            $peers = $wgService->getAllPeers();
            foreach ($peers as $peer) {
                if (($peer['comment'] ?? '') === $username) {
                    $wgService->removeClient($peer['public-key'] ?? '');
                    break;
                }
            }

            // ایجاد Peer جدید
            $speed = $account->group->mikrotik_speed ?? '80M/10M';
            $result = $wgService->createClient($username, $speed);

            if (!$result['status']) {
                return ['status' => false, 'message' => "خطا در ایجاد Peer: " . ($result['message'] ?? 'نامشخص')];
            }

            $data = $result['data'];
            $wgUser->update([
                'user_ip' => $data['ip_address'] ?? '',
                'public_key' => $data['client_public_key'] ?? '',
                'is_enabled' => 1,
            ]);

            Log::info("Peer {$username} بازسازی شد.");
            $this->closeOpenIssues($username, auth()->id());

            return ['status' => true, 'message' => "Peer با موفقیت بازسازی شد."];
        } catch (\Exception $e) {
            Log::error("خطا در بازسازی Peer {$username}: " . $e->getMessage());
            return ['status' => false, 'message' => "خطا: " . $e->getMessage()];
        }
    }

    /**
     * دکمه ۳: حذف کامل (Orphan)
     */
    public function deleteOrphan(int $serverId, string $username): array
    {
        $server = Nas::find($serverId);
        if (!$server) {
            return ['status' => false, 'message' => "سرور با ID {$serverId} یافت نشد."];
        }

        try {
            // ۱. حذف از سرور
            $wgService = new WireguardService($server);
            $peers = $wgService->getAllPeers();
            foreach ($peers as $peer) {
                if (($peer['comment'] ?? '') === $username) {
                    $wgService->removeClient($peer['public-key'] ?? '');
                    break;
                }
            }

            // ۲. حذف از wireguard_users (اگر وجود داشته باشد)
            WireGuardUsers::where('profile_name', $username)->delete();

            // ۳. حذف از accounts (اگر وجود داشته باشد و شرط داشته باشد)
            // فقط در صورتی که اکانت منقضی شده باشد یا بدون سرویس باشد
            $account = Accounts::where('username', $username)->where('service_group', 'wireguard')->first();
            if ($account) {
                // اگر اکانت منقضی شده یا بدون سرویس باشد، حذف شود
                $isExpired = $account->expire_date && Carbon::parse($account->expire_date)->isPast();
                if ($isExpired || !WireGuardUsers::where('user_id', $account->id)->exists()) {
                    $account->delete();
                }
            }

            Log::info("Orphan {$username} حذف شد.");
            $this->closeOpenIssues($username, auth()->id());

            return ['status' => true, 'message' => "Orphan با موفقیت حذف شد."];
        } catch (\Exception $e) {
            Log::error("خطا در حذف Orphan {$username}: " . $e->getMessage());
            return ['status' => false, 'message' => "خطا: " . $e->getMessage()];
        }
    }

    /**
     * حذف همه Orphan‌ها
     */
    public function deleteAllOrphans(): array
    {
        $orphanIssues = $this->getOpenIssues()
            ->whereIn('issue_type', ['orphan_peer_only', 'orphan_full', 'orphan_peer_config'])
            ->where('status', 'open');

        $deleted = 0;
        $failed = 0;
        $errors = [];

        foreach ($orphanIssues as $issue) {
            $result = $this->deleteOrphan($issue->server_id, $issue->username);
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
            'message' => "{$deleted} Orphan حذف شد، {$failed} مورد با خطا مواجه شد.",
            'errors' => $errors,
        ];
    }
}
