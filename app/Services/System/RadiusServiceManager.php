<?php
// app/Services/System/RadiusServiceManager.php

namespace App\Services\System;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class RadiusServiceManager
{
    protected string $serviceName = 'radius_server.service';

    public function getStatus(): array
    {
        $result = Process::run("systemctl is-active {$this->serviceName}");
        $isActive = trim($result->output()) === 'active';

        $resultEnabled = Process::run("systemctl is-enabled {$this->serviceName}");
        $isEnabled = trim($resultEnabled->output()) === 'enabled';

        $resultStatus = Process::run("systemctl status {$this->serviceName} --no-pager");

        $output = $resultStatus->output();
        $pid = null;
        $uptime = null;
        $lastStarted = null;

        // استخراج PID
        if (preg_match('/Main PID:\s*(\d+)/', $output, $matches)) {
            $pid = $matches[1];
        }

        // استخراج Uptime
        if (preg_match('/Active:\s*active\s*\(running\)\s*since\s*(.+?)(?:\n|$)/', $output, $matches)) {
            $lastStarted = trim($matches[1]);
            // محاسبه uptime به ثانیه (ساده)
            $uptime = $this->calculateUptime($lastStarted);
        }

        // آخرین خطا (در صورت وجود)
        $lastError = null;
        if (preg_match('/ERROR:\s*(.+?)(?:\n|$)/', $output, $matches)) {
            $lastError = trim($matches[1]);
        }

        return [
            'status' => $isActive ? 'active' : 'inactive',
            'is_enabled' => $isEnabled,
            'pid' => $pid,
            'uptime' => $uptime,
            'last_started' => $lastStarted,
            'last_error' => $lastError,
            'raw_output' => $output,
        ];
    }

    protected function calculateUptime(?string $since): ?int
    {
        if (!$since) return null;
        try {
            $start = \Carbon\Carbon::parse($since);
            return now()->diffInSeconds($start);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function start(): array
    {
        return $this->execute("systemctl start {$this->serviceName}");
    }

    public function stop(): array
    {
        return $this->execute("systemctl stop {$this->serviceName}");
    }

    public function restart(): array
    {
        return $this->execute("systemctl restart {$this->serviceName}");
    }

    public function reload(): array
    {
        return $this->execute("systemctl reload {$this->serviceName}");
    }

    protected function execute(string $command): array
    {
        $result = Process::run($command);

        return [
            'success' => $result->successful(),
            'output' => $result->output(),
            'error' => $result->errorOutput(),
        ];
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }
}
