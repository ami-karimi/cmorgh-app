<?php
namespace App\Utility;
use Illuminate\Support\Str;


class Functions {
    public static function otp(int $length = 5): string
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= random_int(0, 9);
        }

        return $code;
    }

    /**
     * تولید یک رشته تصادفی ترکیبی (حروف و عدد) با طول دلخواه
     * مناسب برای تولید پسوردهای رندوم یا نام کاربری اختصاصی
     * مثال خروجی: "x7T9pLm2Qv"
     */
    public static function randomString(int $length = 8): string
    {
        return Str::random($length);
    }
}
