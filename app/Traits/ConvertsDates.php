<?php

namespace App\Traits;

use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

trait ConvertsDates
{
    /**
     * تبدیل تاریخ میلادی به شمسی با فرمت دلخواه
     */
    public function toJalali($date, $format = 'Y/m/d H:i')
    {
        if (!$date) return '-';
        if ($date instanceof Carbon) {
            return Jalalian::fromCarbon($date)->format($format);
        }
        try {
            return Jalalian::fromCarbon(Carbon::parse($date))->format($format);
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function toJalaliDate($date, $format = 'Y/m/d')
    {
        return $this->toJalali($date, $format);
    }

    public function toJalaliDateTime($date, $format = 'Y/m/d H:i')
    {
        return $this->toJalali($date, $format);
    }

    /**
     * تبدیل تاریخ شمسی (رشته) به میلادی (رشته)
     */
    public function jalaliToGregorian($jalaliDate)
    {
        if (!$jalaliDate) return null;

        // پشتیبانی از فرمت‌های مختلف
        $jalaliDate = trim($jalaliDate);

        // اگر شامل خط فاصله یا اسلش بود
        $formats = ['Y/m/d', 'Y-m-d', 'Y/m/d H:i', 'Y-m-d H:i'];

        foreach ($formats as $format) {
            try {
                $carbon = Jalalian::fromFormat($format, $jalaliDate)->toCarbon();
                return $carbon->toDateString();
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * تبدیل بازه تاریخ شمسی به میلادی
     */
    public function jalaliRangeToGregorian($from, $to)
    {
        return [
            'from' => $this->jalaliToGregorian($from),
            'to' => $this->jalaliToGregorian($to),
        ];
    }

    /**
     * دریافت امروز شمسی
     */
    public function todayJalali($format = 'Y/m/d')
    {
        return Jalalian::now()->format($format);
    }

    /**
     * دریافت زمان حال شمسی
     */
    public function nowJalali($format = 'Y/m/d H:i')
    {
        return Jalalian::now()->format($format);
    }
}
