<?php

namespace App\Console\Commands;

use App\Services\ActivityLogger;
use Illuminate\Console\Command;
use App\Models\Nas;
use App\Models\WireGuardUsers;
use App\Services\WireguardService;
use Illuminate\Support\Facades\Log;
use App\Models\Accounts;

class SyncWireguardUsage extends Command
{
    protected $signature = 'vpn:sync-wg-usage';
    protected $description = 'Synchronizing WireGuard user traffic through MikroTik queues (Queues)';

    public function handle()
    {
        $this->info('Starting the WireGuard traffic synchronization process...');

        // دریافت تمام سرورهای فعال
        $servers = Nas::where('is_enabled', 1)->supportsProtocol('wireguard')->get();

        foreach ($servers as $server) {
            $this->info("Communicating with the server: {$server->nasname} ({$server->l2tp_address})");

            try {
                $wgService = new WireguardService($server);

                // استخراج کاربرانی که روی این سرور ساخته شده و فعال هستند
                $users = WireGuardUsers::where('server_id', $server->id)
                    ->where('is_enabled', 1)
                    ->get();

                if ($users->isEmpty()) {
                    $this->warn(" There are no active users on this server.");
                    continue;
                }

                $syncedCount = 0;

                foreach ($users as $user) {
                    $usageResult = $wgService->getClientUsage($user->user_ip);

                    if ($usageResult['status']) {
                        $currentRx = $usageResult['data']['upload'];
                        $currentTx = $usageResult['data']['download'];
                        $deltaRx = $currentRx - $user->last_rx;
                        $deltaTx = $currentTx - $user->last_tx;

                        if ($deltaRx < 0) {
                            $deltaRx = $currentRx;
                        }
                        if ($deltaTx < 0) {
                            $deltaTx = $currentTx;
                        }

                        $user->rx += $deltaRx;
                        $user->tx += $deltaTx;

                        $user->last_rx = $currentRx;
                        $user->last_tx = $currentTx;

                        $user->save();

                        $account = Accounts::where('id', $user->user_id)->first();

                        if ($account) {
                            $account->upload_usage += $deltaRx;
                            $account->download_usage += $deltaTx;
                            $account->usage = $account->upload_usage + $account->download_usage;

                            if ($account->max_usage > 0 && $account->usage >= $account->max_usage) {
                                \App\Services\VpnManagerService::toggleAccount($account, false);
                            }

                            $account->save();
                        }

                        $syncedCount++;

                    } else {
                        Log::warning("Queue for IP {$user->user_ip} not found on server {$server->nasname}.");
                    }
                }

                $this->info("✔ Traffic {$syncedCount} User on the server  {$server->name} Updated.");

            } catch (\Exception $e) {
                Log::error("Server synchronization error  {$server->nasname}: " . $e->getMessage());
                $this->error("❌ Server sync failure {$server->nasname}");
            }
        }

        $this->info('The traffic sink operation has ended.');
    }
}
