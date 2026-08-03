<?php

namespace App\Livewire\Admin;

use App\Models\Nas;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Group;
use App\Models\User;
use App\Utility\Helper;
use App\Services\AccountProvisioningService;

#[Title('صدور اکانت جدید | همراه سیمرغ')]
#[Layout('layouts.admin')]
class AccountCreate extends Component
{
    public $currentStep = 1;

    // متغیرهای فرم
    public $username,$password, $name,$phonenumber;
    public $group_id,$creator;
    public $service_group = 'l2tp_cisco';
    public $wg_server_id;
    public $protocol_v2ray = 'vmess';

    public function mount()
    {
        // پیش‌فرض قرار دادن نماینده روی کاربر لاگین شده فعلی
        $this->creator = auth()->id();
    }

    // اعتبارسنجی زنده (Live Validation) حین تایپ
    public function updatedUsername($value)
    {
        $this->validateOnly('username', ['username' => 'required|string|max:20|unique:accounts,username']);
    }

    public function updatedPassword($value)
    {
        $this->validateOnly('password', ['password' => 'required|string|min:6']);
    }

    public function autoGenerateUsername()
    {
        $this->username = Helper::generateUsername('cs');
        $this->password = (string) rand(100000, 999999);$this->validateOnly('username', ['username' => 'unique:accounts,username']);
    }

    public function nextStep()
    {
        if ($this->currentStep == 1) {$this->validate([
            'username' => 'required|string|max:20|unique:accounts,username',
            'password' => 'required|string|min:6',
        ]);
        } elseif ($this->currentStep == 2) {$this->validate([
            'service_group' => 'required|string|in:l2tp_cisco,openvpn,wireguard,v2ray',
            'group_id'      => 'required|integer|exists:groups,id',
            'creator'       => 'required|integer|exists:users,id', // صحت‌سنجی نماینده انتخاب‌شده
        ]);
        } elseif ($this->currentStep == 3) {
            if ($this->service_group === 'wireguard') {$this->validate(['wg_server_id' => 'required|integer|exists:nas,id']);
            } elseif ($this->service_group === 'v2ray') {$this->validate(['protocol_v2ray' => 'required|string']);
            }
        }
        $this->currentStep++;
    }

    public function prevStep()
    {
        $this->currentStep--;
    }

    public function save(AccountProvisioningService $provisioningService)
    {
        $configResult = Helper::AccountConfig(
            $this->service_group,
            [['username' => $this->username, 'password' => $this->password]],
            $this->group_id,
            $this->name,
            $this->phonenumber,
            $this->creator,
            [
                'wg_server_id' => $this->wg_server_id,
                'protocol_v2ray' => $this->protocol_v2ray
            ]
        );

        if (!$configResult['status']) {
            session()->flash('error', $configResult['result']);
            return;
        }

        $group = Group::find($this->group_id);
        $mikrotikSpeed = '10M/10M';
        $charge = \App\Models\Charge::find($group->charge_id);
        if ($charge && str_contains($charge->name, '-')) {
            $parts = explode('-', $charge->name);

            $speedValue = trim(end($parts));
            $mikrotikSpeed = "{$speedValue}/{$speedValue}";
        }

        $userData = [
            'username' => $this->username,
            'password' => $this->password,
            'name'     => $this->name,
        ];

        try {
            // فرستادن سرعت به آرایه کانفیگ برای استفاده در سرویس دیتابیس
            $configData = array_merge($configResult['result'], [
                'speed_limit' => $mikrotikSpeed
            ]);

            $provisioningService->createFullAccount($userData, $configData);

            session()->flash('message', 'اکانت جدید و دسترسی لاگین نماینده با موفقیت ایجاد شد.');
            return redirect()->route('admin.accounts.list');
        } catch (\Exception $e) {
            session()->flash('error', 'خطا: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // فیلتر کردن هوشمند گروه‌ها بر اساس پروتکل انتخابی
        $groupsQuery = Group::where('is_enabled', 1)->orderBy('sort_order', 'asc');

        if ($this->service_group === 'wireguard') {$groupsQuery->where('name', 'LIKE', '%وایرگارد%');
        } elseif ($this->service_group === 'v2ray') {$groupsQuery->where('name', 'LIKE', '%v2ray%')->orWhere('name', 'LIKE', '%V2ray%');
        } else {
            $groupsQuery->where('name', 'NOT LIKE', '%وایرگارد%')
                ->where('name', 'NOT LIKE', '%v2ray%')
                ->where('name', 'NOT LIKE', '%V2ray%');
        }

        // دریافت لیست تمام مدیران و نمایندگان برای تخصیص اکانت
        $creators = User::where('is_active', 1)->get();

        return view('livewire.admin.account-create', [
            'groups' => $groupsQuery->get(),
            'creators' => $creators,
            'allWgServers' =>  Nas::where('is_enabled',1)->supportsProtocol('wireguard')->get(),
        ]);
    }
}
