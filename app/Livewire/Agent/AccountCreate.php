<?php

namespace App\Livewire\Agent;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Accounts;
use App\Models\User;
use App\Models\Group;
use App\Models\Financial;

#[Title('صدور اکانت جدید | پنل نمایندگی')]
#[Layout('layouts.agent')]
class AccountCreate extends Component
{
    public $creationType = 'single';
    public $selectedWgServer = 'auto';

    public $isErrorModalOpen = false;
    public $errorMessage = '';

    // متغیرهای مشترک
    public $group_id = '';
    public $service_group = 'wireguard'; // پروتکل پیش‌فرض
    public $searchPlan = '';

    // متغیرهای اکانت تکی
    public $username = '';
    public $password = '';
    public $customer_id = 'new'; // پیش‌فرض روی مشتری جدید

    // متغیرهای ثبت مشتری جدید
    public $newCustomerName = '';
    public $newCustomerPhone = '';
    public $newCustomerEmail = '';

    // متغیرهای اکانت گروهی
    public $bulkCount = 5;
    public $prefix = '';

    public $isSuccessModalOpen = false;
    public $createdAccountsList = [];

    public function mount($customer_id = null)
    {
        if ($customer_id) {
            // اگر از صفحه پروفایل مشتری آمده باشد، آن مشتری را پیش‌فرض انتخاب می‌کنیم
            $this->customer_id = $customer_id;
            $this->creationType = 'single'; // مطمئن می‌شویم روی حالت تکی است
        } else {
            $this->customer_id = 'new'; // حالت پیش‌فرض عادی
        }
    }

    // تغییر حالت تکی/گروهی
    public function setCreationType($type)
    {
        $this->creationType =$type;
        $this->resetValidation();
    }

    // تغییر پروتکل و ریست کردن گروه انتخاب شده
    public function setServiceGroup($protocol)
    {
        $this->service_group =$protocol;
        $this->group_id = ''; // با تغییر پروتکل، تعرفه قبلی باطل می‌شود
        $this->resetValidation(['group_id', 'username', 'password']);
    }

    // تولید یوزر و پسورد تصادفی برای اکانت VPN
    public function generateRandomCredentials()
    {
        $this->username = strtolower(Str::random(6)) . rand(10, 99);$this->password = (string) rand(100000, 999999);
    }

    // تولید ایمیل تصادفی برای مشتری جدیدf
    public function generateRandomEmail()
    {
        $this->newCustomerEmail = 'user_' . Str::random(5) . rand(10, 99) . '@domain.com';
    }

    public function createAccounts()
    {
        $agent = Auth::user();

        // ==========================================
        // ۱. جمع‌آوری تمام قوانین و پیام‌های خطا به صورت یکجا
        // ==========================================
        $rules = [
            'group_id'      => 'required|exists:groups,id',
            'service_group' => 'required|in:wireguard,l2tp_cisco,openvpn',
        ];

        $messages = [
            'group_id.required'      => 'انتخاب تعرفه الزامی است.',
            'service_group.required' => 'انتخاب پروتکل الزامی است.',
        ];

        // اگر مشتری جدید بود، قوانین مشتری هم اضافه شود
        if ($this->customer_id === 'new') {
            $rules['newCustomerName']  = 'required|string|max:255';
            $rules['newCustomerPhone'] = 'nullable|string|max:20';
            $rules['newCustomerEmail'] = 'required|email|max:255';

            $messages['newCustomerName.required']  = 'وارد کردن نام مشتری الزامی است.';
            $messages['newCustomerEmail.required'] = 'وارد کردن ایمیل الزامی است.';
            $messages['newCustomerEmail.email']    = 'فرمت ایمیل صحیح نیست.';
        }

        // بررسی نوع ساخت (تکی یا گروهی) و افزودن قوانین مربوطه
        if ($this->creationType === 'single') {
            // اگر وایرگارد بود و یوزرنیم خالی بود، خودش قبل از ولیدیشن پرش کنه
            if ($this->service_group === 'wireguard' && empty($this->username)) {
                $this->generateRandomCredentials();
            }

            $rules['username']    = 'required|string|unique:accounts,username';
            $rules['password']    = 'required|string|min:4';
            $rules['customer_id'] = 'required';

            $messages['username.required']    = 'وارد کردن نام کاربری الزامی است.';
            $messages['username.unique']      = 'این نام کاربری قبلاً ثبت شده است.';
            $messages['password.required']    = 'وارد کردن کلمه عبور الزامی است.';
            $messages['password.min']         = 'کلمه عبور حداقل 4 کاراکتر باشد.';
            $messages['customer_id.required'] = 'لطفاً یک مشتری انتخاب کنید.';
        } else {
            $rules['bulkCount'] = 'required|numeric|min:2|max:100';
            $rules['prefix']    = 'nullable|string|max:10';

            $messages['bulkCount.required'] = 'وارد کردن تعداد الزامی است.';
            $messages['bulkCount.min']      = 'حداقل تعداد صدور باید 2 باشد.';
        }

        // ==========================================
        // ۲. اجرای یکپارچه اعتبارسنجی (شلیک تمام ارورها با هم)
        // ==========================================
        $this->validate($rules, $messages);

        // ==========================================
        // ۳. بررسی موجودی کیف پول (فقط وقتی اجرا میشه که فرم بدون خطا باشه)
        // ==========================================
        $group = Group::find($this->group_id);
        $agentCostPerAccount = $group->getFinalPriceFor($agent);
        $totalAccountsToCreate = $this->creationType === 'bulk' ? (int) $this->bulkCount : 1;
        $totalCost = $agentCostPerAccount * $totalAccountsToCreate;

        if ($agent->balance < $totalCost) {
            $this->errorMessage = 'موجودی کیف پول شما برای این عملیات کافی نیست. لطفاً ابتدا حساب خود را شارژ کنید.';
            $this->isErrorModalOpen = true;
            return;
        }

        // ==========================================
        // ۴. شروع تراکنش دیتابیس و ساخت اکانت
        // ==========================================
        DB::beginTransaction();
        try {
            $provisioningService = new \App\Services\AccountProvisioningService();
            $targetUser = null;
            $existingUserId = null;

            // ثبت یا پیدا کردن مشتری
            if ($this->customer_id === 'me') {
                $targetUser = User::firstOrCreate(
                    ['creator' => $agent->id, 'role' => 'customer', 'email' => 'archive_' . $agent->id . '@local.system'],
                    ['name' => '🗂️ آرشیو اکانت‌های من', 'username' => 'archive_agent_' . $agent->id, 'password' => \Illuminate\Support\Facades\Hash::make(Str::random(16)), 'is_active' => 1]
                );
                $existingUserId = $targetUser->id;
            } elseif (is_numeric($this->customer_id)) {
                $targetUser = User::find($this->customer_id);
                $existingUserId = $targetUser->id;
            } elseif ($this->customer_id === 'new') {
                $targetUser = new User([
                    'name'     => $this->newCustomerName,
                    'username' => $this->newCustomerPhone ?? Str::random(10),
                    'email'    => $this->newCustomerEmail,
                    'phone'    => $this->newCustomerPhone,
                ]);
            }

            $createdList = [];

            // ثبت اکانت تکی
            if ($this->creationType === 'single') {
                $overrides = [
                    'username'      => strtolower($this->username),
                    'password'      => $this->password,
                    'service_group' => $this->service_group,
                ];

                if ($this->service_group === 'wireguard' && $this->selectedWgServer !== 'auto') {
                    $overrides['wg_server_id'] = $this->selectedWgServer;
                }

                $preparedData = $provisioningService->prepareAccountData($group, $targetUser, $this->newCustomerPhone ?? null, $overrides);

                $result = $provisioningService->createFullAccount(
                    $preparedData['userData'], $preparedData['configData'], $existingUserId, true, false
                );

                if (is_array($result) && isset($result['status']) && !$result['status']) {
                    throw new \Exception($result['message']);
                }

                $accountObj = $result['account'] ?? null;
                $createdList[] = [
                    'id'            => $accountObj ? $accountObj->id : null,
                    'username'      => strtolower($this->username),
                    'password'      => $this->password,
                    'service_group' => $this->service_group,
                    'group_name'    => $group->name,
                ];

                // ثبت اکانت گروهی
            } else {
                $defaultWgServerId = $this->service_group === 'wireguard' ? \App\Models\Nas::where('type', 'wireguard')->value('id') : null;

                for ($i = 0; $i < $this->bulkCount; $i++) {
                    do {
                        $randUser = strtolower($this->prefix . Str::random(5) . rand(10, 99));
                    } while (\App\Models\Accounts::where('username', $randUser)->exists());

                    $randPass = (string) rand(100000, 999999);

                    $overrides = [
                        'username'      => $randUser,
                        'password'      => $randPass,
                        'service_group' => $this->service_group,
                        'wg_server_id'  => $defaultWgServerId
                    ];

                    $preparedData = $provisioningService->prepareAccountData($group, $targetUser, null, $overrides);

                    $result = $provisioningService->createFullAccount(
                        $preparedData['userData'], $preparedData['configData'], $existingUserId, true, false
                    );

                    if (is_array($result) && isset($result['status']) && !$result['status']) {
                        throw new \Exception("خطا در ساخت اکانت گروهی: " . $result['message']);
                    }

                    $accountObj = $result['account'] ?? null;
                    $createdList[] = [
                        'id'            => $accountObj ? $accountObj->id : null,
                        'username'      => $randUser,
                        'password'      => $randPass,
                        'service_group' => $this->service_group,
                        'group_name'    => $group->name,
                    ];
                }
            }

            DB::commit();

            $this->createdAccountsList = $createdList;
            $this->isSuccessModalOpen = true;

            $this->reset(['username', 'password', 'group_id', 'newCustomerName', 'newCustomerPhone', 'newCustomerEmail']);
            $this->customer_id = 'new';

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = 'خطای سیستمی رخ داد: ' . $e->getMessage();
            $this->isErrorModalOpen = true;
        }
    }

    public function resetFormAndCloseModal()
    {
        $this->isSuccessModalOpen = false;
        $this->createdAccountsList = [];
    }


    public function render()
    {
        $agent = Auth::user();

        // محاسبه موجودی
        $balance = $agent->balance;

        $hiddenGroups = DB::table('agent_hidden_groups')->where('agent_id',$agent->id)->pluck('group_id')->toArray();

        $groupsQuery = Group::where('is_enabled', 1)->whereNotIn('id',$hiddenGroups);

        // 🧠 فیلتر هوشمند گروه‌ها بر اساس پروتکل
        if ($this->service_group === 'wireguard') {$groupsQuery->where(function($q) {$q->where('name', 'like', '%وایرگارد%')
            ->orWhere('name', 'like', '%wireguard%')
            ->orWhere('name', 'like', '%wg%');
        });
        } else {
            // اگر وایرگارد نبود، گروه‌هایی که اسم وایرگارد دارند را فیلتر کن
            $groupsQuery->where(function($q) {$q->where('name', 'not like', '%وایرگارد%')
                ->where('name', 'not like', '%wireguard%')
                ->where('name', 'not like', '%wg%');
            });
        }

        // فیلتر نوار جستجو
        if (!empty($this->searchPlan)) {
            $groupsQuery->where('name', 'like', '\%' .$this->searchPlan . '%');
        }

        $availableGroups =$groupsQuery->get();
        $customers = User::where('creator',$agent->id)->where('role', 'customer')->get();

        $wgServers = \App\Models\Nas::where('is_enabled', 1)
            ->supportsProtocol('wireguard')
            ->get()
            ->map(function ($server) {
                $activeUsersCount = \App\Models\WireGuardUsers::where('server_id', $server->id)
                    ->where('is_enabled', 1)
                    ->count();

                $maxCapacity = $server->capacity ?? 250;

                $remainingCapacity = max(0, $maxCapacity - $activeUsersCount);

                $usagePercent = $maxCapacity > 0 ? min(100, round(($activeUsersCount / $maxCapacity) * 100)) : 0;

                $server->active_users_count = $activeUsersCount;
                $server->max_capacity = $maxCapacity;
                $server->remaining_capacity = $remainingCapacity;
                $server->usage_percent = $usagePercent;

                return $server;
            });

        return view('livewire.agent.account-create', [
            'balance' => $balance,
            'availableGroups' => $availableGroups,
            'wgServers' => $wgServers,
            'discountPercent' => $agent->discount_percent ?? 0,
            'customers' => $customers,
        ]);
    }
}
