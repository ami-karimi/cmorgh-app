<?php

namespace App\Livewire\Admin;

use App\Models\Accounts;
use App\Models\Nas;
use App\Models\WireGuardUsers;
use App\Services\MikrotikApiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('بررسی سیستم | پنل مدیریت')]
#[Layout('layouts.admin')]
class SystemCheck extends Component
{
    public string $mode = 'mikrotik';
    public ?int $serverId = null;
    public string $username = '';
    public string $publicKey = '';
    public string $privateKey = '';
    public ?array $result = null;
    public ?string $error = null;
    public bool $checking = false;

    public function updatedMode(): void
    {
        $this->reset(['result', 'error', 'serverId', 'username', 'publicKey', 'privateKey']);
    }

    public function check(): void
    {
        $this->reset(['result', 'error']);
        $this->validate($this->mode === 'mikrotik'
            ? ['serverId' => 'required|integer', 'username' => 'required|string|max:128']
            : ['serverId' => 'required|integer', 'publicKey' => 'nullable|string|max:128', 'privateKey' => 'nullable|string|max:128'],
            [],
            ['serverId' => 'سرور', 'username' => 'نام کاربری', 'publicKey' => 'کلید عمومی', 'privateKey' => 'کلید خصوصی']
        );

        if ($this->mode === 'wireguard' && trim($this->publicKey) === '' && trim($this->privateKey) === '') {
            $this->addError('publicKey', 'حداقل یکی از کلید عمومی یا خصوصی را وارد کنید.');
            return;
        }

        $server = Nas::whereKey($this->serverId)->where('is_enabled', true)->first();
        if (!$server) {
            $this->error = 'سرور انتخاب‌شده فعال نیست یا وجود ندارد.';
            return;
        }

        $this->checking = true;
        try {
            $this->result = $this->mode === 'mikrotik'
                ? $this->checkMikrotik($server)
                : $this->checkWireguard($server);
        } catch (\Throwable $e) {
            report($e);
            $this->error = 'بررسی انجام نشد. اتصال و اطلاعات دسترسی سرور را بررسی کنید.';
        } finally {
            $this->checking = false;
        }
    }

    private function checkMikrotik(Nas $server): array
    {
        $api = new MikrotikApiService($server);
        $connection = $api->connect();
        if (!($connection['ok'] ?? false)) {
            return ['ok' => false, 'message' => 'اتصال به MikroTik برقرار نشد. ' . ($connection['error'] ?? '')];
        }

        $active = $api->getActiveUsers();
        $secret = $api->getAllUsers();

        if (!($active['ok'] ?? false) && !($secret['ok'] ?? false)) {
            return ['ok' => false, 'message' => 'دریافت اطلاعات کاربران MikroTik ناموفق بود: ' . ($active['error'] ?? $secret['error'] ?? 'پاسخ نامعتبر از RouterOS')];
        }

        $needle = mb_strtolower(trim($this->username));
        $matches = fn ($user) => mb_strtolower((string) ($user['name'] ?? '')) === $needle;

        $activeUser = collect($active['data'] ?? [])->first($matches);
        $secretUser = collect($secret['data'] ?? [])->first($matches);
        $isOnline = (bool) $activeUser;

        if (!$activeUser && !$secretUser) {
            return ['ok' => true, 'found' => false, 'server' => $server->name, 'message' => 'این نام کاربری نه در کاربران فعال و نه در کاربران تعریف‌شده MikroTik پیدا نشد.'];
        }

        $account = Accounts::where('username', $this->username)->first();
        $remote = $this->safeMikrotikData($activeUser ?: $secretUser);
        $remote['وضعیت اتصال'] = $isOnline ? 'آنلاین (در /ppp/active)' : 'آفلاین (فقط در /ppp/secret)';
        if ($isOnline && $secretUser) {
            $remote['تعریف‌شده در Secret'] = 'بله';
        }

        return ['ok' => true, 'found' => true, 'online' => $isOnline, 'server' => $server->name, 'type' => 'mikrotik', 'remote' => $remote, 'account' => $account ? $this->safeAccount($account) : null];
    }

    private function checkWireguard(Nas $server): array
    {
        $publicKey = trim($this->publicKey);
        if ($publicKey === '' && trim($this->privateKey) !== '') {
            $publicKey = $this->publicKeyFromPrivate($this->privateKey);
            if (!$publicKey) {
                return ['ok' => false, 'message' => 'فرمت کلید خصوصی WireGuard معتبر نیست.'];
            }
        }

        if (!preg_match('/^[A-Za-z0-9+\/]{42}[AEIMQUYcgkosw048]=$/', $publicKey)) {
            return ['ok' => false, 'message' => 'فرمت کلید عمومی WireGuard معتبر نیست.'];
        }

        $api = new MikrotikApiService($server);
        $connection = $api->connect();
        if (!($connection['ok'] ?? false)) {
            return ['ok' => false, 'message' => 'اتصال به سرور WireGuard/MikroTik برقرار نشد.'];
        }

        $response = $api->bs_mkt_rest_api_get('/interface/wireguard/peers');
        $peer = collect($response['data'] ?? [])->first(fn ($item) => hash_equals((string) ($item['public-key'] ?? ''), $publicKey));
        if (!$peer) {
            return ['ok' => true, 'found' => false, 'server' => $server->name, 'public_key' => $publicKey, 'message' => 'این Peer در سرور WireGuard پیدا نشد.'];
        }

        $local = WireGuardUsers::with(['account'])->where('server_id', $server->id)->where('public_key', $publicKey)->first();
        return ['ok' => true, 'found' => true, 'server' => $server->name, 'type' => 'wireguard', 'public_key' => $publicKey, 'remote' => $this->safeWireguardData($peer), 'local' => $local ? ['profile' => $local->profile_name ?? $local->profilename ?? null, 'username' => $local->profile_name ?? null, 'account' => $local->account ? $this->safeAccount($local->account) : null] : null];
    }

    private function publicKeyFromPrivate(string $privateKey): ?string
    {
        $decoded = base64_decode(trim($privateKey), true);
        if ($decoded === false || strlen($decoded) !== SODIUM_CRYPTO_BOX_SECRETKEYBYTES) return null;
        return base64_encode(sodium_crypto_scalarmult_base($decoded));
    }

    private function safeMikrotikData(array $data): array
    {
        unset($data['password'], $data['secret']);
        return $data;
    }

    private function safeWireguardData(array $data): array
    {
        return collect($data)->only(['.id', 'interface', 'allowed-address', 'public-key', 'disabled', 'current-endpoint-address', 'current-endpoint-port', 'last-handshake', 'rx', 'tx'])->all();
    }

    private function safeAccount(Accounts $account): array
    {
        return ['id' => $account->id, 'username' => $account->username, 'name' => $account->name ?? null, 'is_enabled' => (bool) ($account->is_enabled ?? false), 'expires_at' => $account->expires_at ?? null];
    }

    public function render()
    {
        $servers = Nas::where('is_enabled', true)->orderBy('name')->get();
        return view('livewire.admin.system-check', compact('servers'));
    }
}
