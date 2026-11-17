<?php

namespace App\Services\Backup;

use App\Models\BackupSetting;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class BackupStorageService
{
    public function diskName(): string
    {
        return 'download-host';
    }

    protected static ?Filesystem $diskCache = null;

    protected static ?string $diskHash = null;

    public function reset(): void
    {
        static::$diskCache = null;
        static::$diskHash = null;
    }

    public function settings(): BackupSetting
    {
        return BackupSetting::current();
    }

    public function disk(): Filesystem
    {
        $config = $this->diskConfig();
        $hash = md5(serialize($config));

        if (
            static::$diskCache &&
            static::$diskHash === $hash
        ) {
            return static::$diskCache;
        }

        static::$diskCache = Storage::build($config);
        static::$diskHash = $hash;

        return static::$diskCache;
    }

    public function diskConfig(): array
    {
        $settings = $this->settings();

        $driver = $settings->storage_driver ?: 'sftp';

        $config = [
            'driver' => $driver,
            'host' => $settings->storage_host,
            'username' => $settings->storage_username,
            'password' => $settings->storage_password,
            'port' => (int) ($settings->storage_port ?: (
            $driver === 'sftp' ? 22 : 21
            )),
            'root' => $settings->storage_root ?: '/',
            'timeout' => (int) ($settings->storage_timeout ?: 120),
        ];

        if ($driver === 'sftp') {
            $config['privateKey'] = $settings->storage_private_key;
        }

        if ($driver === 'ftp') {
            $config['passive'] = true;
            $config['ssl'] = false;
            $config['ignorePassiveAddress'] = true;
        }

        return array_filter(
            $config,
            fn ($value) => $value !== null && $value !== ''
        );
    }

    public function testConnection(array $config): array
    {
        try {
            $disk = Storage::build(array_filter(
                $config,
                fn ($value) => $value !== null && $value !== ''
            ));

            $probe = '.cmorgh-test-' . Str::random(12) . '.tmp';

            $writable = false;

            if ($disk->put($probe, 'cmorgh-connection-test')) {
                $writable = true;
                $disk->delete($probe);
            }

            return [
                'ok' => true,
                'writable' => $writable,
                'message' => $writable
                    ? 'اتصال برقرار است و هاست قابل نوشتن است.'
                    : 'اتصال برقرار است، اما هاست قابل نوشتن نیست.',
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'writable' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function rootFolder(): string
    {
        return trim(
            (string) (
            $this->settings()->storage_folder_root
                ?: config('backup.folder_root', 'cmorgh-backups')
            ),
            '/'
        );
    }

    public function buildPath(
        string $category,
        ?string $deviceName,
        string $extension,
        ?CarbonInterface $at = null
    ): string {
        $at ??= now();

        $segments = [
            $this->rootFolder(),
            $category,
        ];

        if ($deviceName) {
            $segments[] = Str::slug($deviceName);
        }

        $segments[] = $at->format('Y-m-d');
        $segments[] = $at->format('H-i-s');

        $prefix = $deviceName
            ? Str::slug($deviceName)
            : $category;

        $filename = sprintf(
            '%s-%s.%s',
            $prefix,
            $at->format('Y-m-d_H-i-s'),
            ltrim($extension, '.')
        );

        return implode('/', $segments) . '/' . $filename;
    }

    public function folderOf(string $path): string
    {
        return ltrim(dirname($path), '.');
    }

    public function upload(
        string $localPath,
        string $remotePath,
        bool $encrypt = true
    ): array {
        if (! is_file($localPath)) {
            throw new RuntimeException(
                "فایل بکاپ پیدا نشد: {$localPath}"
            );
        }

        $contents = file_get_contents($localPath);

        if ($contents === false) {
            throw new RuntimeException(
                "خواندن فایل بکاپ ناموفق بود: {$localPath}"
            );
        }

        $encrypted = false;

        if (
            $encrypt &&
            (bool) config('backup.encrypt', true)
        ) {
            $contents = app(BackupEncryptionService::class)
                ->encrypt($contents);

            $remotePath .= '.enc';
            $encrypted = true;
        }

        $this->disk()->makeDirectory(dirname($remotePath));

        if (! $this->disk()->put($remotePath, $contents)) {
            throw new RuntimeException(
                "آپلود فایل روی هاست دانلود ناموفق بود: {$remotePath}"
            );
        }

        return [
            'path' => $remotePath,
            'folder' => $this->folderOf($remotePath),
            'filename' => basename($remotePath),
            'size' => strlen($contents),
            'checksum' => hash('sha256', $contents),
            'encrypted' => $encrypted,
        ];
    }

    public function delete(?string $path): void
    {
        if ($path) {
            $this->disk()->delete($path);
        }
    }

    public function get(string $path): ?string
    {
        try {
            return $this->disk()->get($path);
        } catch (\Throwable) {
            return null;
        }
    }

    public function exists(string $path): bool
    {
        return $this->disk()->exists($path);
    }
}
