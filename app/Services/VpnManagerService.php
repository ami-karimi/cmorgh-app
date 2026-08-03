<?php

namespace App\Services;

use App\Models\Accounts;
use App\Models\WireguardUsers;
use App\Models\Nas;
use App\Models\Charge;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Financial;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VpnManagerService
{
    /**
     * فعال یا غیرفعال کردن اکانت در سرور و دیتابیس همراه با ثبت لاگ
     *
     * @param Accounts $account
     * @param bool|null $status وضعیت درخواستی (null برای معکوس کردن وضعیت فعلی)
     * @return bool
     */
    public static function toggleAccount(Accounts $account, ?bool $status = null)
    {
        try {
            $newStatus = is_null($status) ? !$account->is_enabled : $status;

            $serverSuccess = match ($account->service_group) {
                'wireguard'         => self::toggleWireguard($account, $newStatus),
                'v2ray'             => self::toggleV2Ray($account, $newStatus),
                'l2tp', 'openvpn'   => self::toggleRadius($account, $newStatus),
                default             => true,
            };

            if (!$serverSuccess) {
                Log::warning("تغییر وضعیت روی سرور برای اکانت {$account->username} با شکست مواجه شد.");
            }

            $account->is_enabled = $newStatus ? 1 : 0;
            $account->save();

            $statusText = $newStatus ? 'فعال' : 'غیرفعال';
            $operatorName = auth()->check() ? auth()->user()->name ?? auth()->user()->username : 'سیستم/کرون‌جاب';

            $logContent = "اکانت {$account->username} توسط {$operatorName} {$statusText} شد.";

            ActivityLogger::log(
                $account->id,
                $logContent,
                1,
                1
            );

            return true;

        } catch (\Exception $e) {
            Log::error("خطا در پروسه تغییر وضعیت اکانت {$account->username}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * متدهای اختصاصی ارتباط با API سرورها
     */
    private static function toggleWireguard(Accounts $account, bool $status): bool
    {
        try {
            // ۱. پیدا کردن کاربر وایرگارد مرتبط با اکانت
            $wgUser = WireGuardUsers::where('user_id', $account->id)->first();

            if (!$wgUser) {
                Log::warning("کاربر وایرگارد با نام کاربری {$account->username} در جدول wire_guard_users یافت نشد.");
                return false;
            }

            // ۲. پیدا کردن سرور مربوطه
            $server = Nas::find($wgUser->server_id);

            if ($server) {
                $wgService = new WireguardService($server);
                $res = $wgService->toggleClientStatus($wgUser->public_key, $status);

                if (!($res['status'] ?? false)) {
                    Log::error("خطا در تغییر وضعیت پیر وایرگارد در میکروتیک: " . ($res['message'] ?? 'ناشناخته'));
                }
            } else {
                Log::warning("سرور متعلق به کاربر وایرگارد #{$wgUser->id} یافت نشد.");
            }

            $wgUser->is_enabled = $status ? 1 : 0;
            $account->is_enabled = $status ? 1 : 0;
            $wgUser->save();

            return true;

        } catch (\Exception $e) {
            Log::error("خطا در متد toggleWireguard برای اکانت {$account->username}: " . $e->getMessage());
            return false;
        }
    }

    private static function toggleV2Ray(Accounts $account, bool $status): bool
    {
        // کدهای اتصال به پنل سنایی / X-UI برای enable/disable کردن client
        return true;
    }

    private static function toggleRadius(Accounts $account, bool $status): bool
    {
        // کدهای مربوط به تغییر وضعیت در دیتابیس Radius (مثلاً radcheck)
        return true;
    }


    private static function applyRechargeToServer(Accounts $account, Group $group)
    {
        match ($account->service_group) {
            'wireguard'         => self::rechargeWireguard($account, $group),
            'v2ray'             => self::rechargeV2Ray($account, $group),
            'l2tp', 'l2tp_cisco', 'openvpn' => self::rechargeRadius($account, $group),
            default             => null,
        };
    }

    public static function rechargeAccount(Accounts $account, Group $group, bool $payFromAgentWallet = false, ?int $executedByUserId = null,$payFromUserWallet = true)
    {
        try {
            DB::beginTransaction();

            $executorId = $executedByUserId ?? auth()->id();

            if ($payFromAgentWallet && $account->creator > 0) {
                $paymentResult = self::processCascadePayment($account, $group, $executorId,$payFromUserWallet);
                if (!$paymentResult['status']) {
                    DB::rollBack();
                    return $paymentResult;
                }
            }


            $remainingDays = 0;
            if ($account->expire_date && Carbon::parse($account->expire_date)->isFuture()) {
                $remainingDays = floor(now()->diffInDays(Carbon::parse($account->expire_date)));
            }

            $rolloverDays = min(3, $remainingDays);
            $rolloverMinutes = $rolloverDays * 24 * 60;


            $baseMinutes = $group->exp_val_minute ?? 0;
            if ($baseMinutes == 0 && isset($group->expire_type) && isset($group->expire_value)) {
                $baseMinutes = match ($group->expire_type) {
                    'minutes' => $group->expire_value,
                    'hours'   => $group->expire_value * 60,
                    'days'    => $group->expire_value * 24 * 60,
                    'month'   => $group->expire_value * 30 * 24 * 60,
                    'year'    => $group->expire_value * 365 * 24 * 60,
                    default   => 0,
                };
            }


            $account->group_id = $group->id;
            $account->upload_usage = 0;
            $account->download_usage = 0;
            $account->usage = 0;
            $account->max_usage = ($group->group_volume > 0) ? ($group->group_volume * 1024 * 1024 * 1024) : 0;

            $account->exp_val_minute = $baseMinutes + $rolloverMinutes;

            if (isset($group->expire_type)) {
                $account->expire_type = $group->expire_type;
                $account->expire_value = $group->expire_value;
            }

            if ($group->first_login == 0) {
                $account->expire_date = ($account->exp_val_minute > 0) ? now()->addMinutes($account->exp_val_minute) : null;
            } else {
                $account->expire_date = null;
            }

            $account->first_login = null;
            $account->is_enabled = 1;
            $account->save();

            self::applyRechargeToServer($account, $group);
            self::toggleAccount($account, true);

            // ثبت نهایی تغییرات
            DB::commit();

            // -------------------------------------------------------------
            // ۶. ثبت لاگ سیستم
            // -------------------------------------------------------------
            $logMsg = "اکانت با بسته {$group->name} تمدید/ایجاد شد.";
            if ($rolloverDays > 0) {
                $logMsg .= " ({$rolloverDays} روز اعتبار قبلی منتقل شد).";
            }
            ActivityLogger::log($account->id, $logMsg, 1, 1);

            return ['status' => true, 'message' => $logMsg];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("خطا در تمدید/ایجاد اکانت {$account->username}: " . $e->getMessage());
            return ['status' => false, 'message' => 'خطای سیستمی: ' . $e->getMessage()];
        }
    }

    public static function processCascadePayment(
        Accounts $account,
        Group $group,
        int $executorId,
        bool $payFromUserWallet = true
    ): array {

        // -------------------------------------------------------------
        // ۰. بررسی کیف پول مشتری/کاربر (در صورتی که تیک زده شده باشد)
        // -------------------------------------------------------------
        $customerToDeduct = null;
        if ($payFromUserWallet) {
            $customer = $account->customer;

            if (!$customer) {
                return [
                    'status' => false,
                    'message' => 'این اکانت به هیچ مشتری متصل نیست. لطفا ابتدا یک مشتری برای آن انتخاب کنید یا تیک کسر از کاربر را بردارید.'
                ];
            }

            $creatorUser =  User::find($customer->creator);
            $customerPrice = $group->price; // قیمت پیش‌فرض سیستم (اگر سازنده ادمین باشد)

            // اگر سازنده اکانت یک نماینده/مدیر فروش است (ادمین کل نیست)
            if ($creatorUser && !in_array($creatorUser->role, ['admin', 'manager'])) {


                $agentSellPrice = DB::table('agent_plan_prices')
                ->where('agent_id', $creatorUser->id)
                    ->where('group_id', $group->id)
                    ->value('selling_price');

                if (!is_null($agentSellPrice)) {
                    $customerPrice = $agentSellPrice;
                }
            }

            if ($customer->balance < $customerPrice) {
                return [
                    'status' => false,
                    'message' => "موجودی کیف پول مشتری ({$customer->name}) کافی نیست. (موجودی: " . number_format($customer->balance) . " تومان | نیاز: " . number_format($customerPrice) . " تومان)"
                ];
            }

            $customerToDeduct = [
                'user'  => $customer,
                'price' => $customerPrice
            ];
        }

        // -------------------------------------------------------------
        // ۱. بررسی و جمع‌آوری نمایندگان بالادستی (کسر قیمت عمده از نماینده)
        // -------------------------------------------------------------
        $agentsChain = [];
        if ($account->creator > 0) {
            $currentAgent = User::find($account->creator);

            while ($currentAgent) {
                if (!in_array($currentAgent->role, ['customer', 'user'])) {
                    // دریافت قیمت خرید (عمده) برای این نماینده
                    $price = $group->getFinalPriceFor($currentAgent);

                    $agentsChain[] = [
                        'agent'   => $currentAgent,
                        'price'   => $price,
                        'balance' => $currentAgent->balance
                    ];
                }
                $currentAgent = $currentAgent->parentAgent;
            }

            // بررسی موجودی تمام نمایندگان
            foreach ($agentsChain as $item) {
                $agent = $item['agent'];
                $price = $item['price'];
                $balance = $item['balance'];

                if ($balance < $price) {
                    return [
                        'status' => false,
                        'message' => "موجودی کیف پول نماینده ({$agent->name}) برای تمدید/ایجاد این بسته کافی نیست. (موجودی: " . number_format($balance) . " تومان | نیاز: " . number_format($price) . " تومان)"
                    ];
                }
            }
        }

        // =============================================================
        // انجام کسر موجودی‌ها و ثبت رکوردهای مالی در دیتابیس
        // =============================================================

        // ۲. کسر موجودی از مشتری نهایی (با قیمت فروش نماینده یا قیمت سیستم)
        if ($customerToDeduct) {
            Financial::create([
                'creator'     => $executorId,
                'for'         => $customerToDeduct['user']->id,
                'type'        => 'minus',
                'price'       => $customerToDeduct['price'], // قیمت داینامیک مشتری
                'approved'    => 1,
                'description' => "تمدید/ایجاد اکانت {$account->username} با بسته {$group->name}",
                'created_at'  => now(),
                'updated_at'  => now()
            ]);
        }

        // ۳. کسر موجودی پلکانی از نمایندگان (با قیمت عمده/خرید)
        foreach ($agentsChain as $index => $item) {
            $agent = $item['agent'];
            $price = $item['price'];

            $description = ($index === 0)
                ? "کسر هزینه عمده تمدید/ایجاد اکانت {$account->username} با سرویس {$group->name}"
                : "کسر پلکانی هزینه تمدید/ایجاد اکانت {$account->username} (مربوط به زیرمجموعه)";

            Financial::create([
                'creator'     => $executorId,
                'for'         => $agent->id,
                'type'        => 'minus',
                'price'       => $price,
                'approved'    => 1,
                'description' => $description,
                'created_at'  => now(),
                'updated_at'  => now()
            ]);
        }

        return ['status' => true];
    }

    /**
     * روتر مرکزی: تفکیک منطق تمدید بر اساس نوع پروتکل
     */


    /**
     * تمدید وایرگارد: ریست مصرف لوکال + آپدیت لیمیت سرعت در میکروتیک
     */
    private static function rechargeWireguard(Accounts $account, Group $group)
    {
        $wgUsers = WireGuardUsers::where('user_id', $account->id)->get();

        // استخراج سرعت گروه جدید
        $mikrotikSpeed = '10M/10M';
        if ($group->mikrotik_speed) {
            $mikrotikSpeed = $group->mikrotik_speed;
        }

        foreach ($wgUsers as $wg) {
            $wg->rx = 0;
            $wg->tx = 0;
            $wg->config_limit = $mikrotikSpeed;
            $wg->save();

            $server = Nas::find($wg->server_id);
            if ($server) {
                $wgService = new WireguardService($server);
                $wgService->updateClientSpeed($wg->user_ip, $wg->profile_name, $mikrotikSpeed);
            }
        }
    }

    /**
     * تمدید رادیوس (L2TP/OpenVPN): تغییر گروه در رادیوس
     */
    private static function rechargeRadius(Accounts $account, Group $group)
    {

    }

    /**
     * تمدید V2Ray: اتصال به API پنل سنایی / X-UI
     */
    private static function rechargeV2Ray(Accounts $account, Group $group)
    {
        // در این بخش باید متدهای ارتباط با V2Ray را پیاده کنید. مثلاً:
        // ۱. دریافت کانکشن V2Ray
        // ۲. ارسال درخواست POST به /xui/API/inbounds/updateClient
        // ۳. ست کردن up:0 و down:0
        // ۴. تبدیل گیگابایت به بایت برای totalGB
        // ۵. تبدیل تاریخ انقضا به timestamp (میلی‌ثانیه) برای expiryTime
    }

}
