<?php

namespace App\Utility;

use App\Models\Accounts;
use App\Models\User;
use App\Models\Group;
use App\Models\Financial;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Morilog\Jalali\Jalalian;

class Helper
{
    // ==========================================
    // ۱. تولید شناسه‌های یکتا
    // ==========================================


    public static function generateUsername(string $prefix): string
    {
        do {
            $username = $prefix . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (Accounts::where('username', $username)->exists());

        return $username;
    }

    // ==========================================
    // ۲. پردازش و آماده‌سازی اطلاعات اکانت (تولید کانفیگ اولیه)
    // ==========================================

    public static function AccountConfig($service_group = null, $user_list = [], $group_id = null, $name = null, $phonenumber = null, $creator = false, $data = [])
    {
        // الف) ولیدیشن اولیه پارامترهای اختصاصی پروتکل‌ها
        if ($service_group == 'v2ray' && empty($data['protocol_v2ray'])) {
            return ['status' => false, 'result' => 'تنظیمات پروتکل V2Ray کامل نیست.'];
        }
        if ($service_group == 'wireguard' && empty($data['wg_server_id'])) {
            return ['status' => false, 'result' => 'سرور هدف وایرگارد انتخاب نشده است.'];
        }

        $findGroup = Group::find($group_id);
        if (!$findGroup) {
            return ['status' => false, 'result' => 'گروه کاربری نامعتبر است.'];
        }

        $result_data = [];
        $GB_IN_BYTES = 1073741824; // ثابت تبدیل گیگابایت به بایت

        // ب) محاسبه زمان انقضا و حجم مجاز بر اساس گروه
        $exp_val_minute = 0;
        $max_usage = 0;
        $expire_value = (int) $findGroup->expire_value;

        switch ($findGroup->expire_type) {
            case 'minutes':
                $exp_val_minute = $expire_value;
                break;
            case 'hours':
                $exp_val_minute = $expire_value * 60;
                $max_usage = 400000000 * $expire_value * $findGroup->multi_login;
                break;
            case 'days':
                $exp_val_minute = $expire_value * 1440;
                $max_usage = 2000000000 * $expire_value * $findGroup->multi_login;
                break;
            case 'month':
                $exp_val_minute = $expire_value * 43200; // 30 روز
                $max_usage = ($expire_value * 100 * $GB_IN_BYTES) * $findGroup->multi_login;
                break;
            case 'year':
                $exp_val_minute = $expire_value * 525600; // 365 روز
                $max_usage = 90000000000 * $expire_value * $findGroup->multi_login;
                break;
        }

        // محاسبه حجم در صورتی که گروه حجمی باشد
        if ($findGroup->group_type == 'volume') {
            $max_usage = $findGroup->group_volume * $GB_IN_BYTES;
        }

        // پ) آماده‌سازی آرایه نهایی برای ذخیره در دیتابیس
        // چون فقط یک کاربر می‌سازیم، اولین آیتم آرایه $user_list را می‌گیریم
        $user = $user_list[0];

        $req_all = [
            'username'       => $user['username'],
            'password'       => $user['password'],
            'name'           => $name,
            'phonenumber'    => $phonenumber,
            'group_id'       => $group_id,
            'service_group'  => $service_group,
            'exp_val_minute' => $exp_val_minute,
            'max_usage'      => $max_usage,
            'expire_value'   => $expire_value,
            'expire_type'    => $findGroup->expire_type,
            'expire_set'     => 0, // 0 یعنی انقضا هنوز شروع نشده (تا اولین اتصال)
            'multi_login'    => $findGroup->multi_login,
            'creator'        => $creator,
        ];

        // اگر گروه از نوع زمانی است و گزینه first_login غیرفعال است (یعنی زمان از همین لحظه استارت بخورد)
        if ($findGroup->group_type == 'expire' && $findGroup->first_login == 0) {
            $req_all['expire_date'] = Carbon::now()->addMinutes($exp_val_minute);
            $req_all['first_login'] = Carbon::now();
            $req_all['expire_set']  = 1;
        }

        // اضافه کردن پارامترهای اختصاصی پروتکل‌ها
        if ($service_group == 'v2ray') {
            $req_all['volume_v2ray']   = $findGroup->group_volume;
            $req_all['protocol_v2ray'] = $data['protocol_v2ray'];
            $req_all['v2ray_location'] = $data['v2ray_location'] ?? null;
        } elseif ($service_group == 'wireguard') {
            $req_all['wg_server_id']   = $data['wg_server_id'];
        }

        // حذف مقادیر null از آرایه تا دیتابیس خطا نگیرد
        $result_data = array_filter($req_all, fn($value) => !is_null($value));

        return [
            'status' => true,
            'result' => $result_data
        ];
    }




    // ==========================================
    // ۴. دریافت تنظیمات سراسری پنل
    // ==========================================


    public static function s($key)
    {
        $settings = self::GetSettings();
        return $settings[$key] ?? false;
    }

    public static function check_expired($time)
    {
        if (Carbon::now()->greaterThanOrEqualTo($time)) {
            return false;
        }
        return Jalalian::forge($time)->ago();
    }
}
