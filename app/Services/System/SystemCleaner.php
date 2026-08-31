<?php

namespace App\Services\System;

use Illuminate\Support\Facades\Storage;
use App\Models\Accounts;
use Carbon\Carbon;

class SystemCleaner
{
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
            'deleted_size_human' => $this->formzatBytes($deletedSize),
        ];
    }

    public function findExpiredUsersPaginated(int $daysOld = 15, int $perPage = 20)
    {
        $threshold = now()->subDays($daysOld);

        $query = Accounts::where(function ($q) use ($threshold) {
            $q->whereNotNull('expire_date')
                ->where('expire_date', '<', $threshold)
                ->orWhere(function ($sub) use ($threshold) {
                    $sub->whereNotNull('max_usage')
                        ->where('max_usage', '>', 0)
                        ->whereRaw('download_usage >= max_usage')
                        ->where('updated_at', '<', $threshold);
                });
        })->with('group');

        $paginated = $query->paginate($perPage);

        $result = $paginated->map(function ($acc) {
            $expireDate = $acc->expire_date ? Carbon::parse($acc->expire_date) : null;
            $daysSinceExpire = $expireDate ? $expireDate->diffInDays(now()) : 0;
            $reason = [];
            if ($expireDate && $expireDate->isPast()) {
                $reason[] = 'منقضی شده ('.$daysSinceExpire.' روز)';
            }
            if ($acc->max_usage > 0 && $acc->download_usage >= $acc->max_usage) {
                $reason[] = 'حجم تمام شده';
            }
            return [
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
        });

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $result,
            $paginated->total(),
            $paginated->perPage(),
            $paginated->currentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }

    public function getExpiredUsersQuery()
    {
        $threshold = now()->subDays(15);

        return Accounts::where(function ($q) use ($threshold) {
            $q->whereNotNull('expire_date')
                ->where('expire_date', '<', $threshold)
                ->orWhere(function ($sub) use ($threshold) {
                    $sub->whereNotNull('max_usage')
                        ->where('max_usage', '>', 0)
                        ->whereRaw('download_usage >= max_usage')
                        ->where('updated_at', '<', $threshold);
                });
        })->with('group');
    }

    public function getExpiredStats(): array
    {
        $threshold = now()->subDays(15);
        return [
            'total' => Accounts::where(function ($q) use ($threshold) {
                $q->whereNotNull('expire_date')->where('expire_date', '<', $threshold)
                    ->orWhere(function ($sub) use ($threshold) {
                        $sub->whereNotNull('max_usage')->where('max_usage', '>', 0)
                            ->whereRaw('download_usage >= max_usage')
                            ->where('updated_at', '<', $threshold);
                    });
            })->count(),
            'expired' => Accounts::whereNotNull('expire_date')->where('expire_date', '<', now())->count(),
            'volume_finished' => Accounts::whereNotNull('max_usage')->where('max_usage', '>', 0)
                ->whereRaw('download_usage >= max_usage')->count(),
        ];
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
