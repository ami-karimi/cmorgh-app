<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use App\Models\Accounts;
use App\Models\WireGuardUsers;
use App\Models\Nas;
use App\Services\VpnManagerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Services\ActivityLogger;
use Illuminate\Support\Str;

class AccountProvisioningService
{


    public function prepareAccountData($group, $user, $phone = null, $overrides = [])
    {
        // اگر در overrides یوزر و پسورد پاس داده شده بود از آن استفاده کن، در غیر این صورت رندوم بساز
        $vpnUsername = $overrides['username'] ?? 'usr_' . strtolower(Str::random(6));
        $vpnPassword = $overrides['password'] ?? Str::random(8);

        // تشخیص نوع سرویس (امکان override کردن)
        $serviceGroup = $overrides['service_group'] ?? (str_contains(mb_strtolower($group->name), 'وایرگارد') ? 'wireguard' : 'l2tp_cisco');

        $finalWgServerId = null;
        if ($serviceGroup === 'wireguard') {
            // اگر نماینده سرور خاصی را انتخاب کرده بود
            if (isset($overrides['wg_server_id'])) {
                $finalWgServerId = $overrides['wg_server_id'];
            } else {
                // یافتن خلوت‌ترین سرور به صورت خودکار
                $finalWgServerId = \App\Models\Nas::where('is_enabled', 1)
                    ->supportsProtocol('wireguard')
                    ->addSelect(['users_count' => \App\Models\WireGuardUsers::selectRaw('count(*)')
                        ->whereColumn('server_id', 'nas.id')
                    ])
                    ->orderBy('users_count', 'asc')
                    ->value('id');

                if (!$finalWgServerId) {
                    throw new \Exception('هیچ سرور وایرگارد فعالی جهت تخصیص یافت نشد.');
                }
            }
        }

        // ۴. محاسبات حجم و تاریخ انقضا
        $maxUsageBytes = ($group->group_volume > 0) ? ($group->group_volume * 1024 * 1024 * 1024) : 0;
        $expType = $group->expire_type ?? 'days';
        $expVal  = (int) ($group->expire_value ?? 30);

        $expValMinute = $group->exp_val_minute ?? match($expType) {
                'minutes' => $expVal,
                'hours'   => $expVal * 60,
                'days'    => $expVal * 24 * 60,
                'month'   => $expVal * 30 * 24 * 60,
                'year'    => $expVal * 365 * 24 * 60,
                default   => 0,
            };

        $expireDate = null;
        if ($group->first_login == 0) {
            $expireDate = match($expType) {
                'minutes' => now()->addMinutes($expVal),
                'hours'   => now()->addHours($expVal),
                'days'    => now()->addDays($expVal),
                'month'   => now()->addMonths($expVal),
                'year'    => now()->addYears($expVal),
                default   => now()->addDays(30),
            };
        }

        // ۵. آماده‌سازی خروجی
        $configData = [
            'username'      => $vpnUsername,
            'password'      => $vpnPassword,
            'name'          => $user->name ?? $vpnUsername, // جلوگیری از خطا اگر نام خالی بود
            'phonenumber'   => $phone ?? $user->phone ?? null,
            'group_id'      => $group->id,
            'service_group' => $serviceGroup,
            'multi_login'   => $group->multi_login ?? 1,
            'expire_type'   => $group->expire_type ?? 'month',
            'expire_value'  => $group->expire_value ?? 1,
            'expire_date'   => $expireDate,
            'first_login'   => $group->first_login == 1 ? null : Carbon::now(),
            'exp_val_minute'=> $expValMinute,
            'max_usage'     => $maxUsageBytes,
            'speed_limit'   => $group->mikrotik_speed ?? null,
            'wg_server_id'  => $finalWgServerId,
        ];

        $userData = [
            'name'     => $user->name ?? $vpnUsername,
            'username' => $user->username ?? $vpnUsername,
            'email'    => $user->email ?? ($vpnUsername . '@cmorgh.com'),
            'phone'    => $user->phone ?? null,
            'password' => $vpnPassword,
        ];
        // برگرداندن هر دو آرایه
        return [
            'configData' => $configData,
            'userData'   => $userData,
        ];
    }

    /**
     * ایجاد یکپارچه یوزر لاگین و اکانت VPN
     */
    public function createFullAccount(array $userData, array $configData, $existingUserId = null,$payFromAgentWallet = true,$payFromUserWallet = true)
    {
        return DB::transaction(function () use ($userData, $configData, $existingUserId, $payFromAgentWallet,$payFromUserWallet) {


            if ($existingUserId) {
                $user = User::findOrFail($existingUserId);
            } else {
                $user = User::create([
                    'name'     => $userData['name'] ?: $userData['username'],
                    'username' => $userData['username'],
                    'email'    => $userData['email'] ?? ($userData['username'] . '@cmorgh.com'),
                    'phone'    => $userData['phone'] ?? null,
                    'password' => Hash::make($userData['password']),
                    'role'     => 'customer',
                    'creator'  => (isset($userData['custom_creator']) ? $userData['custom_creator'] : auth()->id()),

                ]);
                $existingUserId = $user->id;
            }


            if (isset($configData['user_id'])) {
                unset($configData['user_id']);
            }
            $configData['creator'] = $user->id;
            $account = Accounts::create($configData);

            DB::table('user_accounts')->insert([
                'user_id'    => $user->id,
                'account_id' => $account->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $find_group = Group::where('id',$configData['group_id'])->first();

            if ($payFromAgentWallet && $user->creator > 0) {
                $account_user = Accounts::where('id',$account->id)->first();
                $paymentResult = VpnManagerService::processCascadePayment($account_user, $find_group, $existingUserId,$payFromUserWallet);
                if (!$paymentResult['status']) {
                    DB::rollBack();
                    return $paymentResult;
                }
            }



            if ($configData['service_group'] === 'wireguard') {
                $this->provisionWireguard($account, $configData['wg_server_id'] ?? null, $configData['speed_limit'] ?? null);
            }

            ActivityLogger::log($account->id, "اکانت VPN و دسترسی ورود به پنل برای {$account->username} با موفقیت صادر شد.");

            return $account;
        });
    }

    /**
     * متد ایزوله برای ساخت کانفیگ وایرگارد
     */
    private function provisionWireguard(Accounts $account, $serverId, ?string $speedLimit = null)
    {
        $server = Nas::findOrFail($serverId);
        $wgService = new WireguardService($server);

        $response = $wgService->createClient($account->username, $speedLimit);

        if (!$response['status']) {
            throw new Exception('خطا در ساخت کانفیگ روی سرور میکروتیک: ' . $response['message']);
        }

        WireGuardUsers::create([
            'profile_name'       => $response['data']['config_file'],
            'user_id'            => $account->id,
            'server_id'          => $server->id,
            'public_key'         => $response['data']['client_public_key'],
            'client_private_key' => $response['data']['client_private_key'],
            'user_ip'            => $response['data']['ip_address'],
            'config_limit'       => $speedLimit,
            'rx'                 => 0,
            'tx'                 => 0,
            'is_enabled'         => 1
        ]);
    }
}
