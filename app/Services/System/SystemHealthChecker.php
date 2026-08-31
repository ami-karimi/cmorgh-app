<?php

namespace App\Services\System;

use App\Models\SystemHealthCheck;
use App\Models\SystemHealthIssue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

abstract class SystemHealthChecker
{
    protected string $service;
    protected ?SystemHealthCheck $check = null;

    public function __construct(string $service)
    {
        $this->service = $service;
    }

    public function run(): SystemHealthCheck
    {
        $this->check = SystemHealthCheck::create([
            'service' => $this->service,
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            $issues = $this->performCheck();
            $this->saveIssues($issues);

            $this->check->update([
                'status' => 'completed',
                'summary' => [
                    'total_issues' => count($issues),
                    'critical' => collect($issues)->where('severity', 'critical')->count(),
                    'warning' => collect($issues)->where('severity', 'warning')->count(),
                    'info' => collect($issues)->where('severity', 'info')->count(),
                ],
                'completed_at' => now(),
            ]);

            Log::info("Health check completed for {$this->service}", ['check_id' => $this->check->id]);

        } catch (\Exception $e) {
            $this->check->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);
            Log::error("Health check failed for {$this->service}: " . $e->getMessage());
        }

        return $this->check;
    }

    abstract protected function performCheck(): array;

    protected function createIssue(array $data): SystemHealthIssue
    {
        return SystemHealthIssue::create(array_merge($data, [
            'check_id' => $this->check->id,
            'service' => $this->service,
            'detected_at' => now(),
        ]));
    }

    protected function saveIssues(array $issues): void
    {
        foreach ($issues as $issue) {
            $this->createIssue($issue);
        }
    }

    protected function getOpenIssues(?string $username = null): \Illuminate\Support\Collection
    {
        $query = SystemHealthIssue::where('service', $this->service)
            ->where('status', 'open');

        if ($username) {
            $query->where('username', $username);
        }

        return $query->get();
    }

    protected function closeOpenIssues(string $username, ?int $resolvedBy = null): void
    {
        SystemHealthIssue::where('service', $this->service)
            ->where('username', $username)
            ->where('status', 'open')
            ->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'resolved_by' => $resolvedBy,
            ]);
    }

    public function ignoreIssue(int $issueId, ?int $adminId = null): bool
    {
        $issue = SystemHealthIssue::where('service', $this->service)
            ->where('id', $issueId)
            ->where('status', 'open')
            ->first();

        if (!$issue) return false;

        $issue->update([
            'status' => 'ignored',
            'resolved_at' => now(),
            'resolved_by' => $adminId,
        ]);

        return true;
    }
}
