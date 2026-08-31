<?php

namespace App\Services\System;

use App\Services\System\Checkers\MikrotikIntegrityChecker;
use App\Services\System\Checkers\WireGuardIntegrityChecker;
use App\Models\SystemHealthIssue;
use Illuminate\Support\Facades\Log;

class SystemHealthFacade
{
    public function runFullCheck(): array
    {
        $results = [];

        $checkers = [
            'mikrotik' => MikrotikIntegrityChecker::class,
            'wireguard' => WireGuardIntegrityChecker::class,
        ];

        foreach ($checkers as $service => $class) {
            try {
                Log::info("شروع بررسی سرویس: {$service}");
                $checker = new $class();
                $check = $checker->run();
                $results[$service] = [
                    'check_id' => $check->id,
                    'status' => $check->status,
                    'issues_count' => $check->summary['total_issues'] ?? 0,
                ];
                Log::info("بررسی سرویس {$service} با موفقیت انجام شد. تعداد issues: " . ($check->summary['total_issues'] ?? 0));
            } catch (\Exception $e) {
                Log::error("خطا در بررسی سرویس {$service}: " . $e->getMessage());
                $results[$service] = [
                    'status' => 'failed',
                    'issues_count' => 0,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    public function getLatestIssues(int $limit = 50): \Illuminate\Support\Collection
    {
        return SystemHealthIssue::with('user')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
