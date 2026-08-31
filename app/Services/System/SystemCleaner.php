<?php
// app/Services/System/SystemCleaner.php

namespace App\Services\System;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Accounts;
use Carbon\Carbon;

class SystemCleaner
{
    /**
     * حجم فایل‌های لاگ در storage/logs
     */
    public function getLogsSize(): array
    {
        $logPath = storage_path('logs');
        $totalSize = 0;
        $fileCount = 0;

        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log');
            $fileCount = count($files);
            foreach ($files as $file) {
                $totalSize += filesize($file);
            }
        }

        return [
            'size' => $totalSize,
            'size_human' => $this->formatBytes($totalSize),
            'file_count' => $fileCount,
            'path' => $logPath,
        ];
    }

    public function cleanLogs(): array
    {
        $logPath = storage_path('logs');
        $deletedCount = 0;
        $deletedSize = 0;

        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log');
            foreach ($files as $file) {
                // فقط فایل‌هایی که قدیمی‌تر از 7 روز هستند را پاک کن
                if (filemtime($file) < now()->subDays(7)->timestamp) {
                    $deletedSize += filesize($file);
                    unlink($file);
                    $deletedCount++;
                }
            }
        }

        return [
            'success' => true,
            'deleted_count' => $deletedCount,
            'deleted_size' => $deletedSize,
            'deleted_size_human' => $this->formatBytes($deletedSize),
        ];
    }

    /**
     * یافتن کاربران قابل پاکسازی (منقضی یا تمام‌شده)
     */
    public function findExpiredUsers(int $daysOld = 15): array
    {
        $threshold = now()->subDays($daysOld);

        $expiredAccounts = Accounts::where(function ($q) use ($threshold) {
            $q->whereNotNull('expire_date')
                ->where('expire_date', '<', $threshold)
                ->orWhere(function ($sub) use ($threshold) {
                    $sub->whereNotNull('max_usage')
                        ->where('max_usage', '>', 0)
                        ->whereRaw('download_usage >= max_usage')
                        ->where('updated_at', '<', $threshold);
                });
        })->with('group')->get();

        $result = [];
        foreach ($expiredAccounts as $acc) {
            $expireDate = $acc->expire_date ? Carbon::parse($acc->expire_date) : null;
            $daysSinceExpire = $expireDate ? $expireDate->diffInDays(now()) : 0;
            $reason = [];
            if ($expireDate && $expireDate->isPast()) {
                $reason[] = 'منقضی شده ('.$daysSinceExpire.' روز)';
            }
            if ($acc->max_usage > 0 && $acc->download_usage >= $acc->max_usage) {
                $reason[] = 'حجم تمام شده';
            }
            $result[] = [
                'id' => $acc->id,
                'username' => $acc->username,
                'service_group' => $acc->service_group,
                'expire_date' => $expireDate ? $expireDate->toDateString() : null,
                'days_since_expire' => $daysSinceExpire,
                'usage' => $acc->download_usage,
                'max_usage' => $acc->max_usage,
                'reason' => implode(' - ', $reason),
                'can_delete' => true,
            ];
        }

        return $result;
    }

    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
