<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Accounts;
use App\Models\WireGuardUsers;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PublicAccountView extends Component
{
    public $account;
    public $groupName = 'نامشخص';
    public $isOnline = false;
    public $wgConfigs = [];

    // 🟢 متغیرهای جدید برندینگ نماینده
    public $brandName = 'سامانه VPN';
    public $brandLogo = null;
    public $supportId = null;

    public function mount($hash)
    {
        // پیدا کردن اکانت بر اساس هش اختصاصی
        $this->account = Accounts::all()->first(function ($acc) use ($hash) {
            return $acc->public_hash ===$hash;
        });

        if (!$this->account) {
            abort(404, 'صفحه مورد نظر یا اکانت یافت نشد.');
        }

        // 🟢 استخراج برندینگ و لوگوی نماینده سازنده اکانت
        $this->loadAgentBranding();

        // دریافت نام گروه کاربری (تعرفه)
        if (!empty($this->account->group_id)) {
            $group = Group::find($this->account->group_id);
            $this->groupName =$group ? $group->name : ($this->account->service_group ?? 'عمومی');
        } else {
            $this->groupName =$this->account->service_group ?? 'عمومی';
        }

        // محاسبه وضعیت آنلاین/آفلاین
        $this->isOnline =$this->account->is_online
            ?? (isset($this->account->online) &&$this->account->online == 1)
            ?? false;

        // اگر وایرگارد است، کانفیگ‌های آن را لود کن
        if ($this->account->service_group === 'wireguard') {
            $this->wgConfigs = WireGuardUsers::where('user_id',$this->account->id)->get();
        }
    }

    /**
     * 🟢 بررسی و دریافت اطلاعات برندینگ نماینده بالادستی
     */
    private function loadAgentBranding()
    {
        if (!empty($this->account->creator)) {
            $agent = User::find($this->account->creator);

            if ($agent) {
                // دریافت اطلاعات فروشگاه نماینده از جدول agent_stores
                $agentStore = DB::table('agent_stores')
                    ->where('user_id', $agent->id)
                    ->first();

                // اولویت نام برند: عنوان فروشگاه -> نام برند -> نام نماینده -> نام سیستم
                $this->brandName =$agentStore->title
                    ?? $agent->brand_name
                    ?? $agent->name
                    ?? config('app.name', 'سامانه VPN');

                // پشتیبانی تلگرام
                $this->supportId =$agentStore->support_id ?? null;

                // لوگوی نماینده (در صورت وجود)
                $this->brandLogo =$agentStore->logo_url
                    ?? $agent->logo
                    ?? null;
            }
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
        $expireDateFormatted =$this->account->expire_date
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
