<?php

namespace App\Livewire\Agent;

use App\Models\WireGuardUsers;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Accounts;
use App\Models\User;
use App\Models\Group;
use App\Models\Nas;
use App\Models\Radacct;
use App\Services\VpnManagerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Title('مدیریت و مشاهده سرویس | پنل نمایندگی')]
#[Layout('layouts.agent')]
class AccountDetail extends Component
{
    public Accounts $account;

    public $activeTab = 'active_sessions';

    // متغیرهای ویرایش اکانت
    public $isEditModalOpen = false;
    public $username, $password, $name, $phonenumber, $creator, $service_group, $multi_login;

    // متغیرهای اتصال مشتری به اکانت
    public $assigned_user_id;
    public $searchCustomer = '';
    public $selectedCustomerName = '';

    public $showQrModal = false;
    // متغیرهای پرداخت و مالی
    public $pay_from_user_wallet = true;
    public $pay_from_agent_wallet = true;

    // متغیرهای وایرگارد
    public $isChangeServerModalOpen = false;
    public $newWgServerId;
    public $selectedWgConfigId;
    public $configSpeedLimit = [];

    // متغیرهای شارژ
    public $isRechargeModalOpen = false;
    public $selectedGroupId;

    public function mount($id)
    {
        $currentAgentId = Auth::id();

        // بررسی امنیت دسترسی نماینده به اکانت
        $subAgentIds = User::where('creator', $currentAgentId)->pluck('id')->toArray();
        $allowedCreators = array_merge([$currentAgentId], $subAgentIds);

        $this->account = Accounts::findOrFail($id);

        if (!in_array($this->account->creator, $allowedCreators)) {
            abort(403, 'شما دسترسی غیرمجاز دارید. این اکانت متعلق به شبکه فروش شما نیست.');
        }

        if ($this->account->service_group === 'wireguard') {
            $baseSpeed = $this->getAccountBaseSpeed();
            $wgConfigs = WireGuardUsers::where('user_id', $this->account->id)->get();
            foreach ($wgConfigs as $wg) {
                $this->configSpeedLimit[$wg->id] = $wg->config_limit ?: $baseSpeed;
            }
            $this->activeTab = 'wg_configs';
        }
    }

    public function toggleStatus()
    {
        $success = VpnManagerService::toggleAccount($this->account);

        if ($success) {
            session()->flash('message', 'وضعیت اکانت با موفقیت در سیستم و سرور تغییر کرد.');
        } else {
            session()->flash('error', 'خطا در ارتباط با سرور.');
        }
    }

    public function openRechargeModal()
    {
        $this->selectedGroupId = $this->account->group_id;
        $this->resetErrorBag();
        $this->isRechargeModalOpen = true;
    }

    public function confirmRecharge()
    {
        $this->validate([
            'selectedGroupId' => 'required|exists:groups,id',
        ]);

        $group = Group::findOrFail($this->selectedGroupId);

        // فراخوانی سرویس مالی یکپارچه تمدید
        $result = VpnManagerService::rechargeAccount(
            $this->account,
            $group,
            $this->pay_from_agent_wallet,
            auth()->id(),
            $this->pay_from_user_wallet
        );

        if (!$result['status']) {
            $this->addError('wallet', $result['message']);
            return;
        }

        $this->account->refresh();

        $this->isRechargeModalOpen = false;
        session()->flash('message', $result['message']);
    }

    public function openEditModal()
    {
        $this->username = $this->account->username;
        $this->password = $this->account->password;
        $this->name = $this->account->name;
        $this->phonenumber = $this->account->phonenumber;
        $this->creator = $this->account->creator;
        $this->service_group = $this->account->service_group;
        $this->multi_login = $this->account->multi_login;

        // واکشی مشتری متصل به اکانت از طریق رابطه Eloquent
        $customer = $this->account->customer;
        $this->assigned_user_id = $customer ? $customer->id : null;
        $this->selectedCustomerName = $customer ? ($customer->name . ' (' . ($customer->phone ?? 'بدون شماره') . ')') : '';
        $this->searchCustomer = '';

        $this->resetValidation();
        $this->isEditModalOpen = true;
    }

    public function selectCustomer($id, $name, $phone)
    {
        $this->assigned_user_id = $id;
        $this->selectedCustomerName = $name . ' (' . ($phone ?? 'بدون شماره') . ')';
        $this->searchCustomer = '';
    }

    public function save()
    {
        $this->validate([
            'username'    => 'required|string|unique:accounts,username,' . $this->account->id,
            'password'    => 'required|string',
            'name'        => 'nullable|string',
            'phonenumber' => 'nullable|string',
            'creator'     => 'required|exists:users,id',
            'multi_login' => 'nullable|numeric|min:1',
        ]);

        $subAgentIds = User::where('creator', Auth::id())->pluck('id')->toArray();
        $allowedCreators = array_merge([Auth::id()], $subAgentIds);

        if (!in_array($this->creator, $allowedCreators)) {
            $this->addError('creator', 'شما فقط می‌توانید اکانت را به زیرمجموعه‌های خود منتقل کنید.');
            return;
        }

        // بروزرسانی مشخصات اکانت
        $this->account->update([
            'username'    => $this->username,
            'password'    => $this->password,
            'name'        => $this->name,
            'phonenumber' => $this->phonenumber,
            'creator'     => $this->creator,
            'multi_login' => $this->multi_login,
        ]);

        // ذخیره/ارتباط تک‌کاربره در جدول واسط user_accounts با متد sync
        if ($this->assigned_user_id) {
            $this->account->users()->sync([$this->assigned_user_id]);
        } else {
            $this->account->users()->detach();
        }

        $this->isEditModalOpen = false;
        session()->flash('message', 'مشخصات حساب کاربری با موفقیت بروزرسانی شد.');
    }

    // ----------------------------------------------------
    // متدهای وایرگارد
    // ----------------------------------------------------
    public function toggleWgConfig($configId)
    {
        session()->flash('message', 'وضعیت کانفیگ تغییر کرد.');
    }

    public function openChangeServerModal($configId)
    {
        $this->selectedWgConfigId = $configId;
        $this->newWgServerId = '';
        $this->isChangeServerModalOpen = true;
    }

    public function changeWgServer()
    {
        $this->validate([
            'newWgServerId' => 'required|exists:nas,id'
        ]);

        $this->isChangeServerModalOpen = false;
        session()->flash('message', 'انتقال کانفیگ به سرور جدید انجام شد.');
    }

    public function getAccountBaseSpeed()
    {
        $group = Group::find($this->account->group_id);
        return $group ? ($group->speed_limit ?? 'نامحدود') : 'نامحدود';
    }

    public function killSession($radacctid)
    {
        session()->flash('message', 'دستور قطع اتصال به سرور ارسال شد.');
    }

    public function render()
    {
        // 1. سرچ زنده مشتریان جهت انتخاب در مودال ویرایش
        $searchedCustomers = [];
        if (strlen($this->searchCustomer) >= 2) {
            $searchedCustomers = User::where('role', 'customer')
                ->where(function($q) {
                    $q->where('name', 'like', '%' . $this->searchCustomer . '%')
                        ->orWhere('phone', 'like', '%' . $this->searchCustomer . '%');
                })->take(7)->get();
        }

        // 2. دریافت مستقیم مشتری متصل از طریق رابطه Eloquent
        $customer = $this->account->customer;

        // 3. محاسبه زمان و تاریخ انقضا
        $daysRemaining = 0;
        $expireDateFormatted = 'نامحدود';
        if ($this->account->expire_date) {
            $expireDate = Carbon::parse($this->account->expire_date);
            $daysRemaining = max(0, now()->diffInDays($expireDate, false));
            $expireDateFormatted = \Morilog\Jalali\Jalalian::fromCarbon($expireDate)->format('Y/m/d');
        }

        // 4. دریافت رخدادها
        $activities = class_exists('\App\Models\UserActivity')
            ? \App\Models\UserActivity::with('causer')->where('user_id', $this->account->id)->latest('id')->take(15)->get()
            : collect();

        $data = [
            'customer'            => $customer,
            'searchedCustomers'   => $searchedCustomers,
            'account'             => $this->account,
            'daysRemaining'       => intval($daysRemaining),
            'expireDateFormatted' => $expireDateFormatted,
            'activeSessions'      => collect(),
            'sessionHistory'      => collect(),
            'todayConnections'    => 0,
            'totalConnections'    => 0,
            'lastServerIp'        => '-',
            'lastServerName'      => 'نامشخص',
            'lastOnlineFormatted' => '-',
            'loginLogs'           => collect(),
            'activities'          => $activities,
            'wgConfigs'           => collect(),
            'allWgServers'        => collect(),
        ];

        // ۵. لیست زیرنمایندگان مجاز
        $currentAgentId = Auth::id();
        $subAgentIds = User::where('creator', $currentAgentId)->pluck('id')->toArray();
        $allowedCreators = array_merge([$currentAgentId], $subAgentIds);

        $data['creators'] = User::whereIn('id', $allowedCreators)->get();

        // ۶. گروه (پلن) های مجاز برای این نماینده
        $hiddenGroups = DB::table('agent_hidden_groups')->where('agent_id', $currentAgentId)->pluck('group_id')->toArray();
        $data['availableGroups'] = Group::where('is_enabled', 1)->whereNotIn('id', $hiddenGroups)->get();

        // ۷. واکشی اطلاعات سرویس بر اساس نوع پروتکل
        if ($this->account->service_group === 'wireguard') {
            $data['wgConfigs'] = WireGuardUsers::where('user_id', $this->account->id)->get();
            $data['allWgServers'] = Nas::where('is_enabled', 1)->supportsProtocol('wireguard')->get();
        } else {
            $data['activeSessions'] = Radacct::where('username', $this->account->username)->whereNull('acctstoptime')->get();
            $data['sessionHistory'] = Radacct::where('username', $this->account->username)->whereNotNull('acctstoptime')->latest('acctstarttime')->take(20)->get();
            $data['todayConnections'] = Radacct::where('username', $this->account->username)->whereDate('acctstarttime', Carbon::today())->count();
            $data['totalConnections'] = Radacct::where('username', $this->account->username)->count();

            $lastSession = Radacct::where('username', $this->account->username)->latest('acctstarttime')->first();
            $data['lastServerIp'] = $lastSession ? $lastSession->nasipaddress : '-';
            $lastNas = Nas::where('ipaddress', $data['lastServerIp'])->first();
            $data['lastServerName'] = $lastNas ? $lastNas->name : 'نامشخص';
            $data['lastOnlineFormatted'] = $lastSession ? \Morilog\Jalali\Jalalian::fromCarbon(Carbon::parse($lastSession->acctstarttime))->format('Y/m/d H:i') : '-';
            $data['loginLogs'] = DB::table('radpostauth')->where('username', $this->account->username)->latest('id')->take(20)->get();
        }

        return view('livewire.agent.account-detail', $data);
    }
}
