<?php

namespace App\Support;

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

final class JalaliDateTime
{
    public static function normalize(string $value): string
    {
        return strtr(trim($value), array_combine(
            preg_split('//u', '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩', -1, PREG_SPLIT_NO_EMPTY), str_split('01234567890123456789')
        ));
    }

    public static function parse(?string $value): ?string
    {
        if ($value === null || trim($value) === '') { return null; }
        $value = self::normalize($value);
        if (!preg_match('/^(13[0-9]{2}|14[0-9]{2})\/([0-9]{2})\/([0-9]{2}) ([0-9]{2}):([0-9]{2})$/', $value, $parts)) {
            throw new \InvalidArgumentException('تاریخ و ساعت شمسی را با قالب ۱۴۰۵/۰۸/۲۱ ۱۴:۳۰ وارد کنید.');
        }
        if ((int)$parts[2] < 1 || (int)$parts[2] > 12 || (int)$parts[3] < 1 || (int)$parts[3] > 31 || (int)$parts[4] > 23 || (int)$parts[5] > 59) {
            throw new \InvalidArgumentException('تاریخ یا ساعت شمسی معتبر نیست.');
        }
        try {
            $date = Jalalian::fromFormat('Y/m/d H:i', $value)->toCarbon();
            if (Jalalian::fromCarbon($date)->format('Y/m/d H:i') !== $value) { throw new \RuntimeException(); }
            return $date->toDateTimeString();
        } catch (\Throwable) { throw new \InvalidArgumentException('تاریخ یا ساعت شمسی معتبر نیست.'); }
    }

    public static function input($date): string
    {
        return $date ? Jalalian::fromCarbon(Carbon::parse($date))->format('Y/m/d H:i') : '';
    }

    public static function display($date, string $format = 'Y/m/d H:i'): string
    {
        if (!$date) { return '—'; }
        return strtr(Jalalian::fromCarbon(Carbon::parse($date))->format($format), array_combine(
            str_split('0123456789'), preg_split('//u','۰۱۲۳۴۵۶۷۸۹',-1,PREG_SPLIT_NO_EMPTY)
        ));
    }
}
