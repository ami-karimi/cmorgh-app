<?php

namespace App\Services\Backup;

use RuntimeException;

class BackupEncryptionService
{
    private const CIPHER = 'aes-256-gcm';
    private const HEADER = 'CMORGH1';

    protected function key(): string
    {
        $key = config('backup.encryption_key') ?: config('app.key');

        if (
            is_string($key) &&
            str_starts_with($key, 'base64:')
        ) {
            $key = base64_decode(substr($key, 7));
        }

        return hash('sha256', (string) $key, true);
    }

    public function encrypt(string $contents): string
    {
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = random_bytes($ivLength);
        $tag = '';

        $encrypted = openssl_encrypt(
            $contents,
            self::CIPHER,
            $this->key(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($encrypted === false) {
            throw new RuntimeException('رمزنگاری فایل ناموفق بود.');
        }

        return self::HEADER . $iv . $tag . $encrypted;
    }

    public function decrypt(string $contents): string
    {
        if (! str_starts_with($contents, self::HEADER)) {
            throw new RuntimeException('فرمت فایل رمزنگاری‌شده معتبر نیست.');
        }

        $payload = substr($contents, strlen(self::HEADER));

        $ivLength = openssl_cipher_iv_length(self::CIPHER);

        $iv = substr($payload, 0, $ivLength);
        $tag = substr($payload, $ivLength, 16);
        $encrypted = substr($payload, $ivLength + 16);

        $decrypted = openssl_decrypt(
            $encrypted,
            self::CIPHER,
            $this->key(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($decrypted === false) {
            throw new RuntimeException(
                'رمزگشایی فایل ناموفق بود. کلید BACKUP_ENCRYPTION_KEY را بررسی کن.'
            );
        }

        return $decrypted;
    }
}
