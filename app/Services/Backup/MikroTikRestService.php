<?php

namespace App\Services\Backup;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MikroTikRestService
{
    public function baseUrl(object $device): string
    {
        $hostColumn = config(
            'backup.mikrotik.host_column',
            'mikrotik_domain'
        );

        $fallbackColumn = config(
            'backup.mikrotik.fallback_host_column',
            'ipaddress'
        );

        $host = $device->mikrotik_domain;

        if (! $host) {
            throw new RuntimeException(
                'آدرس MikroTik در رکورد روتر پیدا نشد.'
            );
        }

        $host = trim(
            preg_replace(
                '#^https?://#i',
                '',
                trim((string) $host)
            ),
            '/'
        );

        $portColumn = config(
            'backup.mikrotik.port_column',
            'mikrotik_port'
        );

        $port = (int) (
        $device->mikrotik_port ?? 443
        );

        $scheme = config(
            'backup.mikrotik.scheme',
            'https'
        );

        $basePath = trim(
            config('backup.mikrotik.base_path', '/rest'),
            '/'
        );

        return "{$scheme}://{$host}:{$port}/{$basePath}";
    }

    public function request(object $device): PendingRequest
    {
        $usernameColumn = config(
            'backup.mikrotik.username_column',
            'mikrotik_username'
        );

        $passwordColumn = config(
            'backup.mikrotik.password_column',
            'mikrotik_password'
        );

        $request = Http::withBasicAuth(
            (string) $device->mikrotik_username,
            (string) $device->mikrotik_password
        )
            ->acceptJson()
            ->asJson()
            ->connectTimeout(15)
            ->timeout((int) config(
                'backup.mikrotik.timeout',
                55
            ));

        if (! config('backup.mikrotik.verify_ssl', false)) {
            $request->withoutVerifying();
        }

        return $request;
    }

    public function testConnection(object $device): array
    {
        try {
            $response = $this->request($device)
                ->get($this->baseUrl($device) . '/system/identity');

            if ($response->failed()) {
                return [
                    'ok' => false,
                    'message' => $this->errorMessage($response),
                ];
            }

            $data = $response->json();

            return [
                'ok' => true,
                'message' => 'اتصال REST API با موفقیت برقرار شد.',
                'identity' => $this->firstRow($data),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function get(
        object $device,
        string $path,
        array $query = []
    ): array {
        $response = $this->request($device)->get(
            $this->baseUrl($device) . '/' . ltrim($path, '/'),
            $query
        );

        $this->throwIfFailed($response);

        return $response->json() ?? [];
    }

    public function post(
        object $device,
        string $path,
        array $data = []
    ): array {
        $response = $this->request($device)->post(
            $this->baseUrl($device) . '/' . ltrim($path, '/'),
            $data
        );

        $this->throwIfFailed($response);

        return $response->json() ?? [];
    }

    public function execute(
        object $device,
        string $script
    ): array {
        return $this->post($device, '/execute', [
            'script' => $script,
        ]);
    }


    public function createBinaryBackup(
        object $device,
        string $name
    ): array {
        return $this->post($device, '/system/backup/save', [
            'name' => $name,
        ]);
    }

    public function exportText(object $device): string
    {
        $result = $this->execute($device, ':export');

        foreach (['ret', 'output', 'result', 'message'] as $key) {
            if (! array_key_exists($key, $result)) {
                continue;
            }

            $value = $result[$key];

            if (is_array($value)) {
                return json_encode(
                    $value,
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES |
                    JSON_PRETTY_PRINT
                ) ?: '';
            }

            return (string) $value;
        }

        return json_encode(
            $result,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_PRETTY_PRINT
        ) ?: '';
    }


    public function files(object $device): array
    {
        return $this->get($device, '/file');
    }

    public function findFile(
        object $device,
        string $filename
    ): ?array {
        foreach ($this->files($device) as $file) {
            if (($file['name'] ?? null) === $filename) {
                return $file;
            }
        }

        return null;
    }

    public function deleteFile(
        object $device,
        string $id
    ): void {
        $response = $this->request($device)->delete(
            $this->baseUrl($device) . '/file/' . rawurlencode($id)
        );

        $this->throwIfFailed($response);
    }

    protected function throwIfFailed(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        throw new RuntimeException(
            sprintf(
                'REST API خطا داد. HTTP %d: %s',
                $response->status(),
                $this->errorMessage($response)
            )
        );
    }

    protected function errorMessage(Response $response): string
    {
        $json = $response->json();

        if (is_array($json)) {
            return json_encode(
                $json,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            ) ?: $response->body();
        }

        return $response->body();
    }

    protected function firstRow(mixed $data): ?array
    {
        if (! is_array($data)) {
            return null;
        }

        if (isset($data[0]) && is_array($data[0])) {
            return $data[0];
        }

        return $data;
    }
}
