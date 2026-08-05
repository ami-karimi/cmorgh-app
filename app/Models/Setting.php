<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['key', 'group', 'value', 'type'];

    public $timestamps = false;
    public static function get($key, $default = null) {
        $cacheKey = 'site_setting_' . $key;

        // اطلاعات را برای همیشه (تا زمانی که تغییر نکرده) در کش نگه می‌دارد
        return Cache::rememberForever($cacheKey, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set($key, $value, $group = 'general', $type = 'public') {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type]
        );

        Cache::forget('site_setting_' . $key);
    }
}
