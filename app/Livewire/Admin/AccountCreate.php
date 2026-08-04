<?php

namespace App\Livewire\Admin;

use App\Models\Nas;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Group;
use App\Models\User;
use App\Models\Financial;
use App\Utility\Helper;
use App\Services\AccountProvisioningService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Accounts;
#[Title('صدور اکانت جدید | همراه سیمرغ')]
#[Layout('layouts.admin')]

class AccountCreate extends Component
{
    public $creationType = 'single';
    public $customer_id = 'new';
    public $newCustomerName, $newCustomerPhone,$newCustomerEmail;
    public $bulkCount = 10;
    public $prefix = '';

    public $payFromAgentWallet = true;
    public $payFromUserWallet = true;

    public $username,$password;
    public $group_id,$creator;
    public $service_group = 'l2tp_cisco';
    public $wg_server_id;
    public $protocol_v2ray = 'vmess';

    // === متغیرهای محاسبه قیمت زنده ===
    public $basePrice = 0;
    public $totalAgentPrice = 0;
    public $totalUserPrice = 0;
    public $agentDiscount = 0;

    public function mount()
    {
        $this->creator = auth()->id();$this->calculatePrices();
    }

    // هر تغییری در فرم اتفاق بیفتد این تابع اجرا می‌شود تا قیمت‌ها آپدیت شوند
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['group_id', 'creator', 'customer_id', 'creationType', 'bulkCount'])) {$this->calculatePrices();
        }

        if ($propertyName === 'username' && $this->creationType === 'single') {$this->validateOnly('username', ['username' => 'required|string|max:30|unique:accounts,username']);
        }

        if ($propertyName === 'password' && $this->creationType === 'single') {$this->validateOnly('password', ['password' => 'required|string|min:4']);
        }
    }

    // 🔴 منطق محاسبه آنلاین قیمت بر اساس نماینده و تعداد اکانت
    public function calculatePrices()
    {
        if (!$this->group_id) {
            $this->basePrice = 0; $this->totalAgentPrice = 0; $this->totalUserPrice = 0; $this->agentDiscount = 0;
            return;
        }

        $group = Group::find($this->group_id);
        $agent = User::find($this->creator);

        if (!$group || !$agent) return;

        $count = $this->creationType === 'bulk' ? (int)$this->bulkCount : 1;

        $this->basePrice = $group->price;
        $this->agentDiscount =$agent->discount_percent ?? 0;

        // قیمت پرداختی کاربر (بدون تخفیف * تعداد)
        $this->totalUserPrice = $group->getSellingPriceFor($agent) * $count;

        $this->totalAgentPrice = $group->getFinalPriceFor($agent)* $count;
    }

    public function autoGenerateUsername()
    {
        $this->username = Helper::generateUsername('cs');
        $this->password = (string) rand(100000, 999999);$this->validateOnly('username', ['username' => 'unique:accounts,username']);
    }

    public function save(AccountProvisioningService $provisioningService)
    {
        // ۱. اعتبارسنجی فرم
        $rules = [
            'group_id'      => 'required|exists:groups,id',
            'service_group' => 'required|in:wireguard,l2tp_cisco,openvpn,v2ray',
            'creator'       => 'required|exists:users,id',
        ];

        if ($this->creationType === 'single') {
            if ($this->service_group === 'wireguard' && empty($this->username)) {
                $this->autoGenerateUsername();
            }

            $rules['username'] = 'required|string|unique:accounts,username';
            $rules['password'] = 'required|string|min:4';
            $rules['customer_id'] = 'required';

            if ($this->customer_id === 'new') {
                $rules['newCustomerName']  = 'required|string|max:255';
                $rules['newCustomerPhone'] = 'nullable|string|max:20';
                $rules['newCustomerEmail'] = 'nullable|email|max:255';
            }
        } else {
            $rules['bulkCount'] = 'required|numeric|min:2|max:100';
            $rules['prefix']    = 'nullable|string|max:10';
        }

        $this->validate($rules);

        $agent = User::find($this->creator);
        $group = Group::find($this->group_id);

        $targetUser = null;
        $existingUserId = null;

        // ۲. آماده‌سازی کاربر هدف (Target User)
        if ($this->creationType === 'single') {
            if ($this->customer_id === 'me') {
                $targetUser = User::firstOrCreate(
                    ['creator' => $agent->id, 'role' => 'customer', 'email' => 'archive_' . $agent->id . '@local.system'],
                    ['name' => '🗂️ آرشیو اکانت‌های من', 'username' => 'archive_agent_' . $agent->id, 'password' => Hash::make(Str::random(16)), 'is_active' => 1]
                );
                $existingUserId = $targetUser->id;
            } elseif (is_numeric($this->customer_id)) {
                $targetUser = User::find($this->customer_id);
                $existingUserId = $targetUser->id;
            } elseif ($this->customer_id === 'new') {
                // ساخت مدل مجازی در رم (در سرویس دیتابیس می‌شود)
                $targetUser = new User([
                    'name'     => $this->newCustomerName,
                    'username' => $this->newCustomerPhone ?? Str::random(10),
                    'email'    => $this->newCustomerEmail,
                    'phone'    => $this->newCustomerPhone,
                    'role'     => 'customer', // یکپارچه شده با نقش مشتریان
                    'creator'  => $agent->id,
                ]);
            }
        } else {
            // اکانت‌های گروهی پیش‌فرض به آرشیو نماینده می‌روند
            $targetUser = User::firstOrCreate(
                ['creator' => $agent->id, 'role' => 'customer', 'email' => 'archive_' . $agent->id . '@local.system'],
                ['name' => '🗂️ آرشیو اکانت‌های من', 'username' => 'archive_agent_' . $agent->id, 'password' => Hash::make(Str::random(16)), 'is_active' => 1]
            );
            $existingUserId = $targetUser->id;
        }

        DB::beginTransaction();
        try {
            // ۳. ایجاد اکانت‌ها
            if ($this->creationType === 'single') {

                $overrides = [
                    'username'      => strtolower($this->username),
                    'password'      => $this->password,
                    'service_group' => $this->service_group,
                ];

                if ($this->service_group === 'wireguard' && $this->wg_server_id !== 'auto') {
                    $overrides['wg_server_id'] = $this->wg_server_id;
                } elseif ($this->service_group === 'v2ray') {
                    $overrides['protocol_v2ray'] = $this->protocol_v2ray;
                }

                $preparedData = $provisioningService->prepareAccountData($group, $targetUser, $this->newCustomerPhone ?? null, $overrides);

                $result = $provisioningService->createFullAccount(
                    $preparedData['userData'],
                    $preparedData['configData'],
                    $existingUserId,
                    $this->payFromAgentWallet,
                    $this->payFromUserWallet
                );

                if (is_array($result) && isset($result['status']) && !$result['status']) {
                    throw new \Exception($result['message']);
                }

                if (!$existingUserId && isset($result['user_id'])) {
                    $existingUserId = $result['user_id'];
                    $targetUser = User::find($existingUserId);
                }

            } else {

                $defaultWgServerId = null;
                if ($this->service_group === 'wireguard') {
                    $defaultWgServerId = ($this->wg_server_id !== 'auto') ? $this->wg_server_id : Nas::where('type', 'wireguard')->value('id');
                }

                for ($i = 0; $i < $this->bulkCount; $i++) {
                    do {
                        $randUser = strtolower($this->prefix . Str::random(5) . rand(10, 99));
                    } while (Accounts::where('username', $randUser)->exists());

                    $randPass = (string) rand(100000, 999999);

                    $overrides = [
                        'username'      => $randUser,
                        'password'      => $randPass,
                        'service_group' => $this->service_group,
                    ];

                    if ($this->service_group === 'wireguard' && $defaultWgServerId) {
                        $overrides['wg_server_id'] = $defaultWgServerId;
                    } elseif ($this->service_group === 'v2ray') {
                        $overrides['protocol_v2ray'] = $this->protocol_v2ray;
                    }

                    $preparedData = $provisioningService->prepareAccountData($group, $targetUser, null, $overrides);

                    $result = $provisioningService->createFullAccount(
                        $preparedData['userData'],
                        $preparedData['configData'],
                        $existingUserId,
                        $this->payFromAgentWallet,
                        $this->payFromUserWallet
                    );

                    if (is_array($result) && isset($result['status']) && !$result['status']) {
                        throw new \Exception("خطا در ساخت اکانت گروهی: " . $result['message']);
                    }
                }
            }

            // ۴. عملیات مالی
            $count = $this->creationType === 'bulk' ? (int) $this->bulkCount : 1;



            DB::commit();

            session()->flash('success_msg', "تعداد $count اکانت با موفقیت صادر و ثبت گردید.");
            return redirect()->route('admin.accounts.list');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $groupsQuery = Group::where('is_enabled', 1)->orderBy('sort_order', 'asc');

        if ($this->service_group === 'wireguard') {$groupsQuery->where('name', 'LIKE', '%وایرگارد%');
        } elseif ($this->service_group === 'v2ray') {$groupsQuery->where('name', 'LIKE', '%v2ray%')->orWhere('name', 'LIKE', '%V2ray%');
        } else {
            $groupsQuery->where('name', 'NOT LIKE', '%وایرگارد%')->where('name', 'NOT LIKE', '%v2ray%')->where('name', 'NOT LIKE', '%V2ray%');
        }

        // دریافت لیست تمام نمایندگان و مدیران
        $creators = User::where('is_active', 1)->whereIn('role', ['admin','manager', 'agent', 'sub_agent'])->get();

        // دریافت لیست تمام مشتریان (کاربران نهایی)
        $customers = User::with('parentAgent')->where('is_active', 1)->where('role', 'customer')->orderBy('id', 'desc')->get();

        return view('livewire.admin.account-create', [
            'groups'       => $groupsQuery->get(),
            'creators'     => $creators,
            'customers'    => $customers, // اضافه شدن مشتریان به ویو
            'allWgServers' => Nas::where('is_enabled', 1)->supportsProtocol('wireguard')->get(),
        ]);
    }
}
