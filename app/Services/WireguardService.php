<?php

namespace App\Services;

use App\Models\Nas;
use App\Models\WireGuardUsers;
use Illuminate\Support\Facades\Log;

class WireguardService
{
    protected $server;
    protected $api;

    /**
     * تزریق مدل سرور (Nas) و راه‌اندازی API میکروتیک
     */
    public function __construct(Nas $server)
    {
        $this->server = $server;
        $this->api = new MikrotikApiService($server);
    }

    /**
     * متد بررسی اتصال لایو به میکروتیک (ورودی امنیتی سرویس)
     */
    private function connect(): bool
    {
        if ($this->api->connected) {
            return true;
        }

        try {
            $connection = $this->api->connect();
            return (bool) ($connection['ok'] ?? false);
        } catch (\Exception $e) {

            Log::error("خطای اتصال به میکروتیک سرور {$this->server->name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * تولید و ثبت یک اکانت کامل وایرگارد روی سرور
     */
    /**
     * تولید و ثبت یک اکانت کامل وایرگارد روی سرور با سرعت داینامیک
     */
    public function createClient(string $username, ?string $speedLimit = null): array
    {
        // ۱. بررسی لایو بودن و صحت اتصال سرور میکروتیک
        if (!$this->connect()) {
            return ['status' => false, 'message' => 'امکان برقراری ارتباط با سرور میکروتیک وجود ندارد. لطفاً وضعیت سرور را بررسی کنید.'];
        }

        // ۲. دریافت مشخصات اینترفیس وایرگارد
        $interface = $this->getInterfaceInfo();
        if (!$interface['status']) {
            return $interface;
        }

        // ۳. پیدا کردن آی‌پی آزاد در ساب‌نت
        $ipAddress = $this->findAvailableIp();
        if (!$ipAddress) {
            return ['status' => false, 'message' => 'هیچ آی‌پی آزادی در رنج سرور یافت نشد.'];
        }

        // ۴. تولید کلیدهای رمزنگاری Sodium
        $keys = $this->generateKeys();
        $configFile = preg_replace('/\s+/', '', $username) . rand(10, 99);

        // ۵. ثبت Peer در میکروتیک
        $peerResponse = $this->api->bs_mkt_rest_api_add('/interface/wireguard/peers', [
            'interface' => 'ROS_WG_USERS',
            'allowed-address' => "{$ipAddress}/32",
            'public-key' => $keys['public'],
        ]);

        if (!$peerResponse['ok']) {
            return ['status' => false, 'message' => 'خطا در ثبت کاربر در میکروتیک: ' . $peerResponse['error']];
        }

        // 🟢 تعیین سرعت Queue از روی پارامتر ورودی (اگر فرستاده نشد، پیش‌فرض ۸۰ مگابیت قرار می‌گیرد)
        $limit = $speedLimit ?: "80M/10M";

        // ۶. ثبت محدودیت سرعت (Queue) به صورت داینامیک
        $this->api->bs_mkt_rest_api_add('/queue/simple', [
            'name' => $configFile,
            'target' => "{$ipAddress}/32",
            'max-limit' => $limit, // مقدار داینامیک شد!
        ]);

        // ۷. تولید فایل کانفیگ و QR Code
        $this->generateConfigFile($keys['private'], $interface['public_key'], $ipAddress, $interface['listen_port'], $configFile);

        return [
            'status' => true,
            'message' => 'کاربر وایرگارد با موفقیت ساخته شد.',
            'data' => [
                'config_file' => $configFile,
                'client_private_key' => $keys['private'],
                'client_public_key' => $keys['public'],
                'server_pub_key' => $interface['public_key'],
                'ip_address' => $ipAddress,
                'server_name' => $this->server->name,
            ]
        ];
    }

    /**
     * تغییر دستی لیمیت سرعت (Queue) در میکروتیک
     */
    public function updateClientSpeed(string $ipAddress, string $profileName, string $newSpeed): array
    {
        // بررسی وضعیت لایو بودن سرور
        if (!$this->connect()) {
            return ['status' => false, 'message' => 'سرور میکروتیک در دسترس نیست.'];
        }

        // پیدا کردن Queue در میکروتیک بر اساس آی‌پی کلاینت
        $queues = $this->api->bs_mkt_rest_api_get("/queue/simple?target={$ipAddress}/32");

        if ($queues['ok'] && !empty($queues['data'])) {
            $queueId = $queues['data'][0]['.id'];

            // آپدیت صف موجود
            $updateResponse = $this->api->bs_mkt_rest_api_upd("/queue/simple/{$queueId}", [
                'max-limit' => $newSpeed
            ]);

            if (!$updateResponse['ok']) {
                return ['status' => false, 'message' => 'خطا در آپدیت روتر: ' . $updateResponse['error']];
            }
        } else {
            // اگر صف به هر دلیلی وجود نداشت، آن را می‌سازیم
            $addResponse = $this->api->bs_mkt_rest_api_add('/queue/simple', [
                'name' => $profileName,
                'target' => "{$ipAddress}/32",
                'max-limit' => $newSpeed,
            ]);

            if (!$addResponse['ok']) {
                return ['status' => false, 'message' => 'خطا در ساخت صف جدید: ' . $addResponse['error']];
            }
        }

        return ['status' => true, 'message' => 'محدودیت سرعت مستقیماً در روتر اعمال شد.'];
    }
    /**
     * تغییر وضعیت فعال / غیرفعال بودن کاربر (Toggle)
     */
    public function toggleClientStatus(string $publicKey, bool $enable): array
    {
        // بررسی لایو بودن و صحت اتصال سرور میکروتیک
        if (!$this->connect()) {
            return ['status' => false, 'message' => 'سرور میکروتیک در دسترس نیست.'];
        }

        $peer = $this->api->bs_mkt_rest_api_get("/interface/wireguard/peers?interface=ROS_WG_USERS&public-key={$publicKey}");

        if (empty($peer['data'])) {
            return ['status' => false, 'message' => 'کاربر در سرور یافت نشد.'];
        }

        $peerId = $peer['data'][0]['.id'];
        $response = $this->api->bs_mkt_rest_api_upd("/interface/wireguard/peers/{$peerId}", [
            'disabled' => $enable ? 'no' : 'yes'
        ]);

        return [
            'status' => $response['ok'],
            'message' => $response['ok'] ? 'وضعیت با موفقیت تغییر کرد.' : $response['error']
        ];
    }


    public function cleanupOrphanQueuesByIssues(array $orphanUsernames): array
    {
        if (!$this->connect()) {
            return ['status' => false, 'message' => 'سرور میکروتیک در دسترس نیست.'];
        }

        if (empty($orphanUsernames)) {
            return ['status' => true, 'message' => 'هیچ اورفانی برای پاکسازی وجود ندارد.', 'deleted' => 0];
        }

        $deleted = 0;
        $errors = [];

        // ۱. دریافت همه Queueهای Simple (یک بار درخواست)
        $queues = $this->api->bs_mkt_rest_api_get("/queue/simple");
        if (!$queues['ok'] || empty($queues['data'])) {
            return ['status' => true, 'message' => 'هیچ Queueای روی سرور یافت نشد.', 'deleted' => 0];
        }

        // ۲. برای هر Queue، اگر نام آن در لیست اورفان‌ها باشد، حذف کن
        foreach ($queues['data'] as $queue) {
            $queueName = $queue['name'] ?? '';
            if (empty($queueName)) {
                continue;
            }

            // بررسی اینکه آیا این نام در لیست اورفان‌ها وجود دارد؟
            if (in_array($queueName, $orphanUsernames)) {
                $result = $this->api->bs_mkt_rest_api_del("/queue/simple/{$queue['.id']}");
                if ($result['ok']) {
                    $deleted++;
                    \Illuminate\Support\Facades\Log::info("Queue اورفان {$queueName} از سرور حذف شد.");
                } else {
                    $errorMsg = $result['error'] ?? 'نامشخص';
                    $errors[] = "خطا در حذف Queue {$queueName}: {$errorMsg}";
                    \Illuminate\Support\Facades\Log::error("خطا در حذف Queue {$queueName}: {$errorMsg}");
                }
            }
        }

        return [
            'status' => true,
            'message' => "{$deleted} Queue اورفان از سرور حذف شد.",
            'deleted' => $deleted,
            'errors' => $errors,
        ];
    }


    /**
     * حذف کامل کاربر و Queue از سرور
     */
    public function removeClient(string $publicKey): array
    {
        // بررسی لایو بودن و صحت اتصال سرور میکروتیک
        if (!$this->connect()) {
            return ['status' => false, 'message' => 'سرور میکروتیک در دسترس نیست. امکان حذف Peer وجود ندارد.'];
        }

        $peer = $this->api->bs_mkt_rest_api_get("/interface/wireguard/peers?interface=ROS_WG_USERS&public-key={$publicKey}");

        if ($peer['ok'] && !empty($peer['data'])) {
            $peerData = $peer['data'][0];

            // ۱. حذف Peer
            $this->api->bs_mkt_rest_api_del("/interface/wireguard/peers/{$peerData['.id']}");

            // ۲. حذف Queue متصل به این کاربر
            $queues = $this->api->bs_mkt_rest_api_get("/queue/simple?target={$peerData['allowed-address']}");
            if ($queues['ok'] && !empty($queues['data'])) {
                foreach ($queues['data'] as $queue) {
                    $this->api->bs_mkt_rest_api_del("/queue/simple/{$queue['.id']}");
                }
            }

            return ['status' => true, 'message' => 'کاربر و محدودیت سرعت با موفقیت حذف شدند.'];
        }

        return ['status' => true, 'message' => 'کاربر در سرور وجود نداشت (احتمالاً قبلاً پاک شده است).'];
    }

    /**
     * دریافت میزان آپلود و دانلود کاربر از طریق Queue میکروتیک
     */
    public function getClientUsage(string $ipAddress): array
    {
        // بررسی لایو بودن و صحت اتصال سرور میکروتیک
        if (!$this->connect()) {
            return ['status' => false, 'message' => 'سرور میکروتیک جهت خواندن حجم مصرفی در دسترس نیست.'];
        }

        // جستجوی Queue بر اساس آی‌پی تارگت
        $response = $this->api->bs_mkt_rest_api_get("/queue/simple?target={$ipAddress}/32");

        if ($response['ok'] && !empty($response['data'])) {
            $queue = $response['data'][0];

            // میکروتیک بایت‌ها را به صورت "آپلود/دانلود" می‌دهد
            $bytes = $queue['bytes'] ?? '0/0';
            $parts = explode('/', $bytes);

            $uploadBytes = isset($parts[0]) ? (int)$parts[0] : 0;
            $downloadBytes = isset($parts[1]) ? (int)$parts[1] : 0;

            return [
                'status' => true,
                'data' => [
                    'upload' => $uploadBytes,
                    'download' => $downloadBytes,
                    'total' => $uploadBytes + $downloadBytes
                ]
            ];
        }

        return [
            'status' => false,
            'message' => 'محدودیت سرعت (Queue) این کاربر در سرور یافت نشد.'
        ];
    }


    public function getAllPeers(): array
    {
        if (!$this->connect()) {
            return [];
        }

        $response = $this->api->bs_mkt_rest_api_get("/interface/wireguard/peers");
        if ($response['ok'] && !empty($response['data'])) {
            return $response['data'];
        }

        return [];
    }


    public function cleanupOrphanQueues(): array
    {
        if (!$this->connect()) {
            return ['status' => false, 'message' => 'سرور میکروتیک در دسترس نیست.'];
        }

        $deleted = 0;
        $errors = [];

        // ۱. دریافت همه Queueها
        $queues = $this->api->bs_mkt_rest_api_get("/queue/simple");
        if (!$queues['ok'] || empty($queues['data'])) {
            return ['status' => true, 'message' => 'هیچ Queueای یافت نشد.', 'deleted' => 0];
        }

        // ۲. دریافت همه Peerهای فعال (برای مقایسه)
        $peers = $this->api->bs_mkt_rest_api_get("/interface/wireguard/peers");
        $activeIps = [];
        $activeNames = [];
        if ($peers['ok'] && !empty($peers['data'])) {
            foreach ($peers['data'] as $peer) {
                if (isset($peer['allowed-address'])) {
                    $activeIps[] = $peer['allowed-address'];
                }
                if (isset($peer['comment']) && !empty($peer['comment'])) {
                    $activeNames[] = $peer['comment'];
                }
            }
        }

        // ۳. بررسی هر Queue
        foreach ($queues['data'] as $queue) {
            $target = $queue['target'] ?? '';
            $name = $queue['name'] ?? '';
            $isOrphan = true;

            // بررسی بر اساس IP (target)
            if ($target && in_array($target, $activeIps)) {
                $isOrphan = false;
            }

            // بررسی بر اساس نام (اگر Queue با نام کاربری ساخته شده باشد)
            if (!$isOrphan && $name && in_array($name, $activeNames)) {
                $isOrphan = false;
            }

            if ($isOrphan) {
                $result = $this->api->bs_mkt_rest_api_del("/queue/simple/{$queue['.id']}");
                if ($result['ok']) {
                    $deleted++;
                } else {
                    $errors[] = "خطا در حذف Queue {$name}: " . ($result['error'] ?? 'نامشخص');
                }
            }
        }

        return [
            'status' => true,
            'message' => "{$deleted} Queue اورفان حذف شد.",
            'deleted' => $deleted,
            'errors' => $errors,
        ];
    }

    // ==========================================
    // متدهای کمکی (Private Helpers)
    // ==========================================

    private function getInterfaceInfo(): array
    {
        // متد GetInterfaceInfo به این دلیل که به صورت داخلی فقط داخل createClient صدا زده می‌شود،
        // نیازی به چک کردن مجدد متد connect() ندارد چون یک بار در متد والد بررسی شده است.
        $interface = $this->api->bs_mkt_rest_api_get('/interface/wireguard?name=ROS_WG_USERS');

        if ($interface['ok'] && !empty($interface['data'])) {
            $data = $interface['data'][0];
            return [
                'status' => true,
                'public_key' => $data['public-key'],
                'listen_port' => $data['listen-port'],
            ];
        }

        return ['status' => false, 'message' => 'اینترفیس ROS_WG_USERS در سرور میکروتیک یافت نشد.'];
    }

    private function findAvailableIp(): string|false
    {
        for ($i = 2; $i <= 254; $i++) {
            $ip = "12.11.10.{$i}";
            $exists = WireGuardUsers::where('server_id', $this->server->id)->where('user_ip', $ip)->exists();
            if (!$exists) return $ip;
        }
        return false;
    }

    private function generateKeys(): array
    {
        $keypair = sodium_crypto_kx_keypair();
        return [
            'private' => base64_encode(sodium_crypto_kx_secretkey($keypair)),
            'public' => base64_encode(sodium_crypto_kx_publickey($keypair)),
        ];
    }

    private function generateConfigFile($clientPrivKey, $serverPubKey, $ipAddress, $serverPort, $fileName): void
    {
        $dir = public_path("configs");
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $confPath = "{$dir}/{$fileName}.conf";
        $pngPath = "{$dir}/{$fileName}.png";
        $serverIp = $this->server->l2tp_address;

        $config = <<<CONF
[Interface]
PrivateKey = {$clientPrivKey}
Address = {$ipAddress}/32
DNS = 8.8.8.8

[Peer]
PublicKey = {$serverPubKey}
AllowedIPs = 0.0.0.0/0
Endpoint = {$serverIp}:{$serverPort}
PersistentKeepalive = 10
CONF;

        file_put_contents($confPath, $config);
        exec("qrencode -t png -o {$pngPath} -r {$confPath}");
    }
}
