<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Accounts;
use App\Models\WireGuardUsers;
use App\Models\Nas;

class PublicAccountView extends Component
{
    public $account;
    public $wgConfigs = [];

    public function mount($hash)
    {
        // پیدا کردن اکانت بر اساس هش
        // تمام اکانت‌ها چک می‌شوند تا اکانتی که هش آن برابر با hash ورودی است پیدا شود
        $this->account = Accounts::all()->first(function ($acc) use ($hash) {
            return $acc->public_hash === $hash;
        });

        if (!$this->account) {
            abort(404, 'صفحه مورد نظر یا اکانت یافت نشد.');
        }

        // اگر اکانت وایرگارد است، کانفیگ‌های آن را لود کن
        if ($this->account->service_group === 'wireguard') {
            $this->wgConfigs = WireGuardUsers::where('user_id', $this->account->id)->get();
        }
    }

    public function render()
    {
        // محاسبه وضعیت انقضا
        $isExpired = $this->account->expire_date && \Carbon\Carbon::parse($this->account->expire_date)->isPast();
        $isFirstLogin = is_null($this->account->expire_date);

        // محاسبه روزهای باقیمانده
        $daysRemaining = 0;
        if ($this->account->expire_date && !$isExpired) {
            $daysRemaining = floor(now()->diffInDays(\Carbon\Carbon::parse($this->account->expire_date)));
        }

        // تاریخ شمسی
        $expireDateFormatted = $this->account->expire_date
            ? \Morilog\Jalali\Jalalian::forge($this->account->expire_date)->format('Y/m/d')
            : 'بدون انقضا';

        return view('livewire.public-account-view', [
            'isExpired'           => $isExpired,
            'isFirstLogin'        => $isFirstLogin,
            'daysRemaining'       => $daysRemaining,
            'expireDateFormatted' => $expireDateFormatted,
        ])->layout('layouts.app');
    }
}
