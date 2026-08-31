<?php
// app/Services/System/SystemHealthFacade.php

namespace App\Services\System;

use App\Services\System\Checkers\MikrotikIntegrityChecker;
use App\Services\System\Checkers\WireGuardIntegrityChecker;
use App\Services\System\Checkers\OpenVPNIntegrityChecker;
use App\Services\System\Checkers\V2RayIntegrityChecker;
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
            $checker = new $class();
            $check = $checker->run();
            $results[$service] = [
                'check_id' => $check->id,
                'status' => $check->status,
                'issues_count' => $check->summary['total_issues'] ?? 0,
            ];
        }

        return $results;
    }

    public function getLatestIssues(int $limit = 50): \Illuminate\Support\Collection
    {
        return \App\Models\SystemHealthIssue::with('user')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
