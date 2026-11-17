<?php

namespace App\Services\Backup;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use phpseclib3\Net\SFTP;
use RuntimeException;

class MikroTikBackupService
{
    public function __construct(
        protected MikroTikRestService $rest
    ) {}

    public function backup(
        object $device,
        CarbonInterface $at
    ): array {
        $workDir = storage_path(
            'app/private/backup-tmp/' . Str::uuid()
        );

        File::ensureDirectoryExists($workDir, 0700);

        $test = $this->rest->testConnection($device);

        if (! $test['ok']) {
            throw new RuntimeException(
                'اتصال REST برقرار نشد: ' . $test['message']
            );
        }

        $stamp = $at->format('Ymd-His');
        $remoteName = 'cmorgh-' . $stamp;

        $files = [];

        /*
        |--------------------------------------------------------------------------
        | Export متنی
        |--------------------------------------------------------------------------
        */

        $export = $this->rest->exportText($device);

        if (trim($export) === '') {
            throw new RuntimeException(
                'خروجی export از MikroTik خالی است.'
            );
        }

        $exportPath = "{$workDir}/{$remoteName}.rsc";

        file_put_contents($exportPath, $export);

        $files[] = [
            'type' => 'mikrotik_export',
            'local_path' => $exportPath,
            'extension' => 'rsc',
        ];

        /*
        |--------------------------------------------------------------------------
        | Backup باینری
        |--------------------------------------------------------------------------
        */

        if (config('backup.ftp.enabled', true)) {
            $this->rest->createBinaryBackup(
                $device,
                $remoteName
            );

            $binaryPath = $this->downloadByFtp(
                $device,
                "{$remoteName}.backup",
                $workDir
            );

            $files[] = [
                'type' => 'mikrotik_binary',
                'local_path' => $binaryPath,
                'extension' => 'backup',
            ];

            $this->deleteRemoteFile(
                $device,
                "{$remoteName}.backup"
            );
        }

        return $files;
    }

    protected function downloadBySftp(
        object $device,
        string $remoteFilename,
        string $localDir
    ): string {
        $hostColumn = config(
            'backup.mikrotik.host_column',
            'host'
        );

        $fallbackHostColumn = config(
            'backup.mikrotik.fallback_host_column',
            'ip'
        );

        $host = $device->{$hostColumn}
            ?? $device->{$fallbackHostColumn};

        $sftpPort = (int) config(
            'backup.sftp.port',
            6044
        );

        $sftp = new SFTP($host, $sftpPort, 30);

        $usernameColumn = config(
            'backup.sftp.username_column',
            'ssh_username'
        );

        $fallbackUsernameColumn = config(
            'backup.sftp.fallback_username_column',
            'username'
        );

        $passwordColumn = config(
            'backup.sftp.password_column',
            'ssh_password'
        );

        $fallbackPasswordColumn = config(
            'backup.sftp.fallback_password_column',
            'password'
        );

        $username = $device->{$usernameColumn}
            ?: $device->{$fallbackUsernameColumn};

        $password = $device->{$passwordColumn}
            ?: $device->{$fallbackPasswordColumn};

        if (! $sftp->login($username, $password)) {
            throw new RuntimeException(
                'ورود SFTP به MikroTik ناموفق بود.'
            );
        }

        $localPath = "{$localDir}/{$remoteFilename}";

        for ($attempt = 1; $attempt <= 20; $attempt++) {
            if (
                $sftp->file_exists($remoteFilename) &&
                $sftp->get($remoteFilename, $localPath) &&
                is_file($localPath) &&
                filesize($localPath) > 0
            ) {
                return $localPath;
            }

            sleep(1);
        }

        throw new RuntimeException(
            "فایل {$remoteFilename} روی MikroTik ساخته نشد."
        );
    }

    protected function deleteRemoteFile(
        object $device,
        string $filename
    ): void {
        try {
            $file = $this->rest->findFile($device, $filename);

            if (! $file || empty($file['.id'])) {
                return;
            }

            $this->rest->deleteFile(
                $device,
                $file['.id']
            );
        } catch (\Throwable) {
            // پاک‌سازی نباید باعث شکست بکاپ شود
        }
    }

    protected function downloadByFtp(
        object $device,
        string $remoteFilename,
        string $localDir
    ): string {
        if (! function_exists('ftp_connect')) {
            throw new RuntimeException(
                'افزونه FTP در PHP فعال نیست. تابع ftp_connect پیدا نشد.'
            );
        }

        $hostColumn = config(
            'backup.ftp.host_column',
            config('backup.mikrotik.host_column', 'host')
        );

        $fallbackHostColumn = config(
            'backup.ftp.fallback_host_column',
            config('backup.mikrotik.fallback_host_column', 'ip')
        );

        $host = $device->mikrotik_domain;

        if (! $host) {
            throw new RuntimeException(
                'آدرس MikroTik برای اتصال FTP مشخص نشده است.'
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

        $port = (int) config('backup.ftp.port', 21);
        $timeout = (int) config('backup.ftp.timeout', 30);

        $connection = @ftp_connect($host, $port, $timeout);

        if ($connection === false) {
            throw new RuntimeException(
                "اتصال FTP به MikroTik برقرار نشد: {$host}:{$port}"
            );
        }

        try {
            $usernameColumn = config(
                'backup.ftp.username_column',
                'username'
            );

            $fallbackUsernameColumn = config(
                'backup.ftp.fallback_username_column',
                'ssh_username'
            );

            $passwordColumn = config(
                'backup.ftp.password_column',
                'password'
            );

            $fallbackPasswordColumn = config(
                'backup.ftp.fallback_password_column',
                'ssh_password'
            );

            $username = $device->mikrotik_username;

            $password = $device->mikrotik_password;

            if (! $username) {
                throw new RuntimeException(
                    'نام کاربری FTP MikroTik مشخص نشده است.'
                );
            }

            if ($password === null) {
                throw new RuntimeException(
                    'رمز عبور FTP MikroTik مشخص نشده است.'
                );
            }

            if (! @ftp_login(
                $connection,
                (string) $username,
                (string) $password
            )) {
                throw new RuntimeException(
                    'ورود به FTP MikroTik ناموفق بود. نام کاربری و رمز عبور را بررسی کن.'
                );
            }

            ftp_pasv(
                $connection,
                (bool) config('backup.ftp.passive', true)
            );

            /*
            |--------------------------------------------------------------------------
            | صبر برای آماده شدن فایل روی MikroTik
            |--------------------------------------------------------------------------
            */

            $localPath = rtrim($localDir, '/') .
                '/' .
                basename($remoteFilename);

            for ($attempt = 1; $attempt <= 20; $attempt++) {
                $files = @ftp_nlist($connection, '.');

                $remoteExists = false;

                if (is_array($files)) {
                    foreach ($files as $file) {
                        if (
                            basename((string) $file) ===
                            basename($remoteFilename)
                        ) {
                            $remoteExists = true;
                            break;
                        }
                    }
                }

                if ($remoteExists) {
                    $downloaded = @ftp_get(
                        $connection,
                        $localPath,
                        $remoteFilename,
                        FTP_BINARY
                    );

                    if (
                        $downloaded &&
                        is_file($localPath) &&
                        filesize($localPath) > 0
                    ) {
                        return $localPath;
                    }
                }

                sleep(1);
            }

            throw new RuntimeException(
                "فایل {$remoteFilename} روی MikroTik پیدا نشد یا دانلود آن ناموفق بود."
            );
        } finally {
            ftp_close($connection);
        }
    }



}
