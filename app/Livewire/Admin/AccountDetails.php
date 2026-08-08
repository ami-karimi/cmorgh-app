<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Accounts;
use App\Models\RadPostAuth;
use App\Models\Group;
use App\Models\Nas;
use App\Models\WireGuardUsers;
use App\Models\UserActivity;
use App\Models\User;
use App\Models\Financial;
use App\Services\WireguardService;
use App\Services\ActivityLogger;
use App\Services\VpnManagerService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;
use Livewire\WithPagination;
#[Title('جزئیات و مانیتورینگ اکانت | همراه سیمرغ')]
#[Layout('layouts.admin')]
class AccountDetails extends Component
{
    use WithPagination;
    public $accountId;
    public $activeTab;

    // مودال‌ها
    public $isChangeServerModalOpen = false;
    public $newWgServerId = '';
    public $configToMoveId = null;
    public $configSpeedLimit = [];

    // متغیرهای فرم ویرایش اکانت
    public $isEditModalOpen = false;
    public $username,$password, $group_id,$creator;
    public $expire_type = 'days',$expire_value = 30;
    public $multi_login = 2,$service_group = 'l2tp_cisco';
    public $is_enabled = true;

    // 🔍 متغیرهای جدید برای جستجوی زنده مشتری (مالک) و ایجادکننده (نماینده)
    public $assigned_user_id = null;
    public $searchCustomer = '';
    public $selectedCustomerName = '';

    public $searchCreator = '';
    public $selectedCreatorName = '';

    // متغیرهای تغییر دستی حجم و زمان (Adjustment)
    public $isAdjustmentModalOpen = false;
    public $adjustAction = 'add_days';
    public $adjustValue = '';

    // متغیرهای شارژ و تمدید
    public $isRechargeModalOpen = false;
    public $recharge_group_id;
    public $pay_from_agent_wallet = false;

    public function mount($id)
    {
        $this->accountId =$id;
        $account = Accounts::findOrFail($id);
        $baseSpeed =$this->getAccountBaseSpeed();

        if ($account->service_group === 'wireguard') {
            $wgConfigs = WireGuardUsers::where('user_id',$this->accountId)->get();
            foreach ($wgConfigs as$wg) {
                $this->configSpeedLimit[$wg->id] = $wg->config_limit ?: $baseSpeed;
            }
            $this->activeTab = 'wg_configs';
        } else {
            $this->activeTab = 'active_sessions';
        }

        $this->account = Accounts::with(['panelUser.parentAgent', 'creatorUser'])->findOrFail($id);

    }

    private function getAccountBaseSpeed()
    {
        $account = Accounts::with('group')->find($this->accountId);
        if ($account && $account->group &&$account->group->charge_id) {
            $charge = \App\Models\Charge::find($account->group->charge_id);
            if ($charge && str_contains($charge->name, '-')) {
                $parts = explode('-',$charge->name);
                $speedValue = trim(end($parts));
                return "{$speedValue}/{$speedValue}";
            }
        }
        return '10M/10M';
    }

    // 🔴 ۱. تغییر وضعیت اکانت با سرویس جامع
    public function toggleStatus()
    {
        $account = Accounts::findOrFail($this->accountId);
        $success = VpnManagerService::toggleAccount($account);

        if ($success) {
            session()->flash('message', 'وضعیت اکانت با موفقیت در سیستم و سرور تغییر کرد.');
        } else {
            session()->flash('error', 'خطا در ارتباط با سرور.');
        }
    }

    public function recharge()
    {
        $this->openRechargeModal();
    }

    public function openRechargeModal()
    {
        $account = Accounts::findOrFail($this->accountId);
        $this->recharge_group_id =$account->group_id;
        $this->pay_from_agent_wallet = false;
        $this->isRechargeModalOpen = true;
    }

    public function confirmRecharge()
    {
        $this->validate([
            'recharge_group_id' => 'required|exists:groups,id'
        ]);

        $account = Accounts::findOrFail($this->accountId);
        $group = Group::findOrFail($this->recharge_group_id);

        // فراخوانی متد یکپارچه‌ی تمدید از سرویس VpnManagerService
        $result = VpnManagerService::rechargeAccount(
            $account,
            $group,
            $this->pay_from_agent_wallet,
            auth()->id() ,
        );

        if (!$result['status']) {
            // اگر خطای مالی (مثل نبود موجودی) رخ داد
            $this->addError('wallet', $result['message']);
            return;
        }

        $this->isRechargeModalOpen = false;
        session()->flash('message', $result['message']);
    }

    // 🔴 ۲. باز کردن مودال ویرایش اکانت
    public function openEditModal()
    {
        $this->resetValidation();
        $account = Accounts::findOrFail($this->accountId);

        $this->username =$account->username;
        $this->password =$account->password;
        $this->group_id =$account->group_id;
        $this->creator =$account->creator;
        $this->expire_type =$account->expire_type ?? 'days';
        $this->expire_value =$account->expire_value ?? 30;
        $this->multi_login =$account->multi_login ?? 2;
        $this->service_group =$account->service_group;
        $this->is_enabled = (bool)$account->is_enabled;

        // واکشی کاربر متصل (مشتری) از جدول user_accounts
        $pivot = DB::table('user_accounts')->where('account_id',$account->id)->first();
        if ($pivot) {
            $customer = User::find($pivot->user_id);
            if ($customer) {
                $this->assigned_user_id =$customer->id;
                $this->selectedCustomerName = $customer->name . ' (' . ($customer->phone ?? 'بدون شماره') . ')';
            }
        } else {
            $this->assigned_user_id = null;
            $this->selectedCustomerName = '';
        }

        // واکشی ایجادکننده (نماینده/مدیر)
        $creatorUser = User::find($account->creator);
        if ($creatorUser) {$this->selectedCreatorName = $creatorUser->name . ' (' .$creatorUser->role . ')';
        } else {
            $this->selectedCreatorName = 'مدیر کل / سیستم';
        }

        $this->isEditModalOpen = true;
    }

    // انتخاب مشتری از لیست سرچ زنده
    public function selectCustomer($id, $name,$phone)
    {
        $this->assigned_user_id =$id;
        $this->selectedCustomerName = $name . ' (' . ($phone ?? 'بدون شماره') . ')';
        $this->searchCustomer = '';
    }

    // انتخاب ایجادکننده از لیست سرچ زنده
    public function selectCreator($id, $name,$role)
    {
        $this->creator =$id;
        $this->selectedCreatorName =$name . ' (' . $role . ')';$this->searchCreator = '';
    }

    // 🔴 ۳. ذخیره ویرایشات اکانت
    public function save()
    {
        $this->validate([
            'username' => 'required|string|max:20|unique:accounts,username,' . $this->accountId,
            'password' => 'required|string|max:255',
            'group_id' => 'required|integer|exists:groups,id',
            'creator'  => 'required|integer',
        ], [
            'creator.required' => 'تعیین ایجادکننده الزامی است.',
            'group_id.required' => 'انتخاب گروه الزامی است.',
        ]);

        $account = Accounts::findOrFail($this->accountId);

        // بروزرسانی اکانت اصلی
        $account->update([
            'username' => $this->username,
            'password' => $this->password,
            'group_id' => $this->group_id,
            'creator'  => $this->creator,
            'is_enabled' => $this->is_enabled ? 1 : 0,
            'expire_type' => $this->expire_type,
            'expire_value' => $this->expire_value,
            'multi_login' => $this->multi_login,
        ]);

        // بروزرسانی یا تخصیص کاربر (مشتری) در جدول user_accounts
        if ($this->assigned_user_id) {
            DB::table('user_accounts')->updateOrInsert(
                ['account_id' => $account->id],
                [
                    'user_id' => $this->assigned_user_id,
                    'updated_at' => now(),
                    'created_at' => DB::raw('COALESCE(created_at, NOW())')
                ]
            );
        } else {
            // در صورت حذف تخصیص کاربر
            DB::table('user_accounts')->where('account_id', $account->id)->delete();
        }

        ActivityLogger::log($account->id, "مشخصات اکانت توسط ادمین ویرایش شد.");
        $this->isEditModalOpen = false;
        session()->flash('message', 'مشخصات اکانت با موفقیت بروزرسانی شد.');
    }

    public function submitAdjustment()
    {
        $this->validate([
            'adjustAction' => 'required|in:add_days,reduce_days,add_volume,reduce_volume',
            'adjustValue' => 'required|numeric|min:0.1'
        ]);

        $account = Accounts::findOrFail($this->accountId);
        $val = (float)$this->adjustValue;
        $logText = "";
        $GB_IN_BYTES = 1073741824;

        switch ($this->adjustAction) {
            case 'add_days':
                $currentExpire =$account->expire_date ? Carbon::parse($account->expire_date) : now();$account->update(['expire_date' => $currentExpire->addDays((int)$val), 'expired' => 0]);
                $logText = "افزایش دستی {$val} روز";
                break;
            case 'reduce_days':
                if ($account->expire_date) {$newExpire = Carbon::parse($account->expire_date)->subDays((int)$val);
                    $account->update(['expire_date' =>$newExpire, 'expired' => $newExpire->isPast() ? 1 :$account->expired]);
                    $logText = "کسر دستی {$val} روز";
                }
                break;
            case 'add_volume':
                $account->update(['max_usage' =>$account->max_usage + ($val * $GB_IN_BYTES)]);
                $logText = "افزایش دستی {$val} گیگابایت";
                break;
            case 'reduce_volume':
                $account->update(['max_usage' => max(0,$account->max_usage - ($val * $GB_IN_BYTES))]);
                $logText = "کسر دستی {$val} گیگابایت";
                break;
        }

        if ($logText) ActivityLogger::log($account->id, $logText);$this->reset(['isAdjustmentModalOpen', 'adjustValue']);
        session()->flash('message', $logText);
    }

    public function toggleWgConfig($configId)
    {
        $wgUser = WireGuardUsers::findOrFail($configId);$account = Accounts::where('username', $wgUser->profile_name)->first() ?? Accounts::find($this->accountId);
        if ($account) {
            VpnManagerService::toggleAccount($account);
            session()->flash('message', 'وضعیت کانفیگ وایرگارد تغییر کرد.');
        }
    }

    public function createWgConfig()
    {
        $account = Accounts::findOrFail($this->accountId);
        $server = Nas::findOrFail($account->wg_server_id ?? Nas::first()->id);
        $response = (new WireguardService($server))->createClient($account->username);
        if ($response['status']) {
            WireGuardUsers::create([
                'profile_name' => $response['data']['config_file'],
                'user_id' => $account->id,
                'server_id' => $server->id,
                'public_key' => $response['data']['client_public_key'],
                'client_private_key' => $response['data']['client_private_key'],
                'user_ip' => $response['data']['ip_address'],
                'rx' => 0, 'tx' => 0, 'is_enabled' => 1
            ]);
            session()->flash('message', 'کانفیگ ساخته شد.');
        } else {
            session()->flash('error', $response['message']);
        }
    }

    public function deleteWgConfig($configId)
    {
        $wgUser = WireGuardUsers::findOrFail($configId);$server = Nas::findOrFail($wgUser->server_id);$response = (new WireguardService($server))->removeClient($wgUser->public_key);
        if ($response['status']) {$wgUser->delete();
            session()->flash('message', 'کانفیگ حذف شد.');
        } else {
            session()->flash('error', $response['message']);
        }
    }

    public function openChangeServerModal($configId)
    {
        $this->configToMoveId = $configId;
        $this->isChangeServerModalOpen = true;
    }




    public function changeWgServer()
    {
        $account = Accounts::findOrFail($this->accountId);

        $result = VpnManagerService::changeWireguardServer(
            $account,
            $this->configToMoveId,
            $this->newWgServerId
        );

        if ($result['status']) {
            session()->flash('message', $result['message']);
            $this->isChangeServerModalOpen = false;
            $this->reset(['configToMoveId', 'newWgServerId']);
        } else {
            session()->flash('error', $result['message']);
        }
        $this->isChangeServerModalOpen = false;
    }

    public function updateConfigSpeed($configId)
    {
        $wgUser = WireGuardUsers::findOrFail($configId);
        $newSpeed = trim($this->configSpeedLimit[$configId]);$server = Nas::findOrFail($wgUser->server_id);$response = (new WireguardService($server))->updateClientSpeed($wgUser->user_ip, $wgUser->profile_name,$newSpeed);
        if ($response['status']) {
            $wgUser->update(['config_limit' =>$newSpeed]);
            session()->flash('success_' . $configId,$response['message']);
        }
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    public function killSession($sessionId)
    {
        DB::table('radacct')->where('radacctid', $sessionId)->update(['acctstoptime' => now(), 'acctterminatecause' => 'Admin-Kill']);
        session()->flash('message', 'نشست قطع شد.');
    }

    public function render()
    {
        $account = Accounts::with('group')->findOrFail($this->accountId);
        $activities = UserActivity::with('causer')->where('user_id', $account->id)->latest('id')->take(15)->get();
        $groups = Group::where('is_enabled', 1)->get();
        $creators = User::whereIn('role', ['admin', 'manager', 'agent', 'sub_agent'])->get();

        // 🔍 سرچ زنده برای یافتن مشتری (کاربر متصل)
        $searchedCustomers = [];
        if (strlen($this->searchCustomer) >= 2) {
            $searchedCustomers = User::where('role', 'customer')
                ->where(function($q) {
                    $q->where('name', 'like', '%' . $this->searchCustomer . '%')
                        ->orWhere('phone', 'like', '%' . $this->searchCustomer . '%');
                })->take(7)->get();
        }

        // 🔍 سرچ زنده برای یافتن ایجادکننده (نماینده/مدیر)
        $searchedCreators = [];
        if (strlen($this->searchCreator) >= 2) {
            $searchedCreators = User::whereIn('role', ['admin', 'manager', 'agent', 'sub_agent'])
                ->where(function($q) {
                    $q->where('name', 'like', '%' . $this->searchCreator . '%')
                        ->orWhere('phone', 'like', '%' . $this->searchCreator . '%');
                })->take(7)->get();
        }

        $daysRemaining = 0;
        $expireDateFormatted = 'بدون انقضا';
        if ($account->expire_date) {
            $daysRemaining = max(0, ceil(now()->diffInDays(Carbon::parse($account->expire_date), false)));
            $expireDateFormatted = Jalalian::forge($account->expire_date)->format('Y-m-d H:i:s');
        }

        $loginLogs = [];
        if ($this->activeTab === 'login_logs') {
            $loginLogs = RadPostAuth::
                where('username', $account->username)
                ->orderBy('id', 'desc')
                ->paginate(10);
        }

        $data = [
            'account'             => $account,
            'daysRemaining'       => $daysRemaining,
            'expireDateFormatted' => $expireDateFormatted,
            'activities'          => $activities,
            'groups'              => $groups,
            'creators'            => $creators,
            'searchedCustomers'   => $searchedCustomers,
            'searchedCreators'    => $searchedCreators,
            'activeSessions'      => collect(),
            'sessionHistory'      => collect(),
            'loginLogs'           => $loginLogs,
            'totalConnections'    => 0,
            'todayConnections'    => 0,
            'lastOnlineFormatted' => 'ثبت نشده',
            'lastServerIp'        => '0.0.0.0',
            'lastServerName'      => 'نامشخص',
            'wgConfigs'           => collect(),
            'allWgServers'        => collect(),
        ];



        if ($account->service_group === 'wireguard') {
            $data['wgConfigs'] = WireGuardUsers::where('user_id', $account->id)->get();
            $data['allWgServers'] = Nas::where('is_enabled', 1)->supportsProtocol('wireguard')->get();
        } else {
            $data['totalConnections'] = DB::table('radacct')->where('username', $account->username)->count();
            $data['todayConnections'] = DB::table('radacct')->where('username', $account->username)->whereDate('acctstarttime', Carbon::today())->count();
            $lastSession = DB::table('radacct')->where('username', $account->username)->latest('acctstarttime')->first();
            $data['lastOnlineFormatted'] = $lastSession ? Jalalian::forge($lastSession->acctstarttime)->format('Y-m-d H:i:s') : 'ثبت نشده';
            $data['lastServerIp'] = $lastSession->nasipaddress ?? '0.0.0.0';
            $data['activeSessions'] = DB::table('radacct')->where('username', $account->username)->whereNull('acctstoptime')->get();
            $data['sessionHistory'] = DB::table('radacct')->where('username', $account->username)->whereNotNull('acctstoptime')->latest('acctstarttime')->take(15)->get();
            $data['loginLogs'] = DB::table('radpostauth')->where('username', $account->username)->latest('created_at')->take(15)->get();
            $server = DB::table('nas')->where('name', $data['lastServerIp'])->first();
            $data['lastServerName'] = $server->name ?? 'سرور رادیوس';
        }

        return view('livewire.admin.account-details', $data);
    }
}
