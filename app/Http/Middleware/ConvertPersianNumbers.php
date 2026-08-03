<?php
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class ConvertPersianNumbers extends TransformsRequest
{
    /**
     * تبدیل اعداد فارسی و عربی به انگلیسی در تمام ورودی‌ها
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (is_string($value)) {
            $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            $arabic  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

            $value = str_replace($persian, $english, $value);
            $value = str_replace($arabic, $english, $value);
        }

        return $value;
    }
}
