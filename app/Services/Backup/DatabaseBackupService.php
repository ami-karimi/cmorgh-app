<?php

namespace App\Services\Backup;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class DatabaseBackupService
{
    /**
     * ساخت بکاپ فشرده دیتابیس در مسیر موقت لوکال
     *
     * خروجی:
     * storage/app/private/backup-tmp/database-2026-11-17_15-30-00.sql.gz
     */
    public function create(): string
    {
        $directory = storage_path(
            'app/private/backup-tmp'
        );

        File::ensureDirectoryExists($directory, 0700);

        $filename = 'database-' .
            now()->format('Y-m-d_H-i-s') .
            '.sql.gz';

        $localPath = $directory . DIRECTORY_SEPARATOR . $filename;

        $connectionName = config('database.default');

        $connection = config(
            "database.connections.{$connectionName}"
        );

        if (! is_array($connection)) {
            throw new RuntimeException(
                "تنظیمات اتصال دیتابیس «{$connectionName}» پیدا نشد."
            );
        }

        $driver = $connection['driver'] ?? null;

        if (! $driver) {
            throw new RuntimeException(
                'درایور دیتابیس مشخص نشده است.'
            );
        }

        $command = match ($driver) {
            'mysql', 'mariadb' => $this->mysqlCommand(
                $connection,
                $localPath
            ),

            'pgsql' => $this->pgsqlCommand(
                $connection,
                $localPath
            ),

            default => throw new RuntimeException(
                "درایور دیتابیس «{$driver}» برای بکاپ پشتیبانی نمی‌شود."
            ),
        };

        $environment = $this->environment(
            $driver,
            $connection
        );

        $result = Process::env($environment)
            ->timeout(1800)
            ->run($command);

        if (
            $result->failed() ||
            ! is_file($localPath) ||
            filesize($localPath) === 0
        ) {
            $error = trim(
                $result->errorOutput()
                    ?: $result->output()
                    ?: 'خطای نامشخص در اجرای بکاپ دیتابیس'
            );

            throw new RuntimeException(
                "بکاپ دیتابیس ناموفق بود: {$error}"
            );
        }

        return $localPath;
    }

    protected function mysqlCommand(
        array $connection,
        string $localPath
    ): string {
        $host = $connection['host'] ?? '127.0.0.1';
        $port = (int) ($connection['port'] ?? 3306);
        $username = $connection['username'] ?? 'root';
        $database = $connection['database'] ?? '';

        if ($database === '') {
            throw new RuntimeException(
                'نام دیتابیس در تنظیمات Laravel مشخص نشده است.'
            );
        }

        $ignoreTables = [
            'radpostauth',
            'radacct',
        ];

        $ignoreOptions = '';

        foreach ($ignoreTables as $table) {
            $ignoreOptions .= sprintf(
                ' --ignore-table=%s.%s',
                escapeshellarg($database),
                escapeshellarg($table)
            );
        }

        return sprintf(
            'mysqldump --single-transaction --quick --routines --triggers --host=%s --port=%d --user=%s %s%s | gzip -9 > %s',
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            escapeshellarg($database),
            $ignoreOptions,
            escapeshellarg($localPath)
        );
    }

    protected function pgsqlCommand(
        array $connection,
        string $localPath
    ): string {
        $host = $connection['host'] ?? '127.0.0.1';
        $port = (int) ($connection['port'] ?? 5432);
        $username = $connection['username'] ?? 'postgres';
        $database = $connection['database'] ?? '';

        if ($database === '') {
            throw new RuntimeException(
                'نام دیتابیس در تنظیمات Laravel مشخص نشده است.'
            );
        }

        return sprintf(
            'pg_dump --no-owner --no-acl --host=%s --port=%d --username=%s %s | gzip -9 > %s',
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($localPath)
        );
    }

    /**
     * رمز عبور از طریق Environment به mysqldump داده می‌شود
     * تا داخل command line نمایش داده نشود.
     */
    protected function environment(
        string $driver,
        array $connection
    ): array {
        $password = (string) ($connection['password'] ?? '');

        return match ($driver) {
            'pgsql' => [
                'PGPASSWORD' => $password,
            ],

            default => [
                'MYSQL_PWD' => $password,
            ],
        };
    }
}
