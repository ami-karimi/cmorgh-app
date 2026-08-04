<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Accounts;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

#[Title('مدیریت اکانت‌ها | همراه سیمرغ')]
#[Layout('layouts.admin')]
class AccountManager extends Component
{
    use WithPagination;

    // --- متغیرهای فیلتر و صفحه‌بندی ---
    public $search = '';
    public $filterStatus = '';
    public $filterGroup = '';
    public $filterCreator = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $perPage = 10;

    // --- متغیرهای عملیات گروهی ---
    public $selectedAccounts = [];
    public $selectAll = false;
    public $bulkAction = '';
    public $bulkGroupId = '';
    public $bulkCreatorId = '';
    public $bulkExpireDate = '';
    public $bulkAddDays = '';
    public $bulkAddVolume = '';
    public $bulkReduceDays = '';
    public $bulkReduceVolume = '';

    // --- متغیرهای فرم مودال (تک کاربر) ---
    public $accountId = null;
    public $isFormOpen = false;
    public $username, $password,$name, $phonenumber,$group_id;
    public $multi_login = 2,$service_group = 'l2tp_cisco';
    public $is_enabled = true,$in_app = true;
    public $can_create_wg = true, $can_create_op = true,$can_create_v2 = true;

    // ریست کردن صفحه‌بندی با تغییر فیلترها
    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedFilterGroup() { $this->resetPage(); }
    public function updatedFilterCreator() { $this->resetPage(); }
    public function updatedFilterDateFrom() { $this->resetPage(); }
    public function updatedFilterDateTo() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    // انتخاب هوشمند تمام کاربران صفحه فعلی
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedAccounts =$this->getAccountsQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedAccounts = [];
        }
    }

    // لغو انتخاب هنگام رفتن به صفحه بعدی/قبلی
    public function updatingPage()
    {
        $this->selectAll = false;
        $this->selectedAccounts = [];
    }

    // متد اجرایی عملیات گروهی
    public function executeBulkAction()
    {
        if (empty($this->selectedAccounts)) {
            session()->flash('error', 'هیچ کاربری انتخاب نشده است.');
            return;
        }

        switch ($this->bulkAction) {
            case 'enable':
                Accounts::whereIn('id', $this->selectedAccounts)->update(['is_enabled' => 1]);
                break;
            case 'disable':
                Accounts::whereIn('id', $this->selectedAccounts)->update(['is_enabled' => 0]);
                break;
            case 'delete':
                Accounts::whereIn('id', $this->selectedAccounts)->delete();
                break;
            case 'change_group':
                $this->validate(['bulkGroupId' => 'required']);
                Accounts::whereIn('id', $this->selectedAccounts)->update(['group_id' =>$this->bulkGroupId]);
                break;
            case 'change_creator':
                $this->validate(['bulkCreatorId' => 'required']);
                Accounts::whereIn('id', $this->selectedAccounts)->update(['creator' =>$this->bulkCreatorId]);
                break;
            case 'set_expire':
                $this->validate(['bulkExpireDate' => 'required|date']);
                Accounts::whereIn('id', $this->selectedAccounts)->update([
                    'expire_date' => $this->bulkExpireDate,
                    'expired' => 0
                ]);
                break;
            case 'add_days':
                $this->validate(['bulkAddDays' => 'required|integer|min:1']);
                foreach ($this->selectedAccounts as$id) {
                    $acc = Accounts::find($id);
                    if ($acc) {
                        $currentExpire =$acc->expire_date ? Carbon::parse($acc->expire_date) : now();$acc->update([
                            'expire_date' => $currentExpire->addDays($this->bulkAddDays),
                            'expired' => 0
                        ]);
                    }
                }
                break;
            case 'reduce_days':
                $this->validate(['bulkReduceDays' => 'required|integer|min:1']);
                foreach ($this->selectedAccounts as$id) {
                    $acc = Accounts::find($id);
                    if ($acc &&$acc->expire_date) {
                        $currentExpire = Carbon::parse($acc->expire_date);
                        $newExpire =$currentExpire->subDays($this->bulkReduceDays);$acc->update([
                            'expire_date' => $newExpire,
                            'expired' => $newExpire->isPast() ? 1 :$acc->expired
                        ]);
                    }
                }
                break;
            case 'add_volume':
                $this->validate(['bulkAddVolume' => 'required|numeric|min:0.1']);
                $bytesToAdd =$this->bulkAddVolume * 1073741824;
                foreach ($this->selectedAccounts as$id) {
                    $acc = Accounts::find($id);
                    if ($acc) {$acc->update(['max_usage' => $acc->max_usage +$bytesToAdd]);
                    }
                }
                break;
            case 'reduce_volume':
                $this->validate(['bulkReduceVolume' => 'required|numeric|min:0.1']);
                $bytesToSubtract =$this->bulkReduceVolume * 1073741824;
                foreach ($this->selectedAccounts as$id) {
                    $acc = Accounts::find($id);
                    if ($acc) {$newMaxUsage = max(0, $acc->max_usage -$bytesToSubtract);
                        $acc->update(['max_usage' =>$newMaxUsage]);
                    }
                }
                break;
            case 'recharge':
                foreach ($this->selectedAccounts as$id) {
                    $acc = Accounts::with('group')->find($id);
                    if ($acc && $acc->group) {$newExpireDate = clone now();
                        if ($acc->group->expire_type === 'days') {
                            $newExpireDate->addDays($acc->group->expire_value);
                        } elseif ($acc->group->expire_type === 'months') {
                            $newExpireDate->addMonths($acc->group->expire_value);
                        }

                        $acc->update([
                            'usage' => 0,
                            'max_usage' => $acc->group->group_volume * 1073741824,                             'expire_date' =>$newExpireDate,
                            'expired' => 0,
                            'is_enabled' => 1
                        ]);
                    }
                }
                break;
        }

        // ریست کامل متغیرها بعد از انجام عملیات
        $this->reset([
            'selectedAccounts', 'selectAll', 'bulkAction', 'bulkGroupId', 'bulkCreatorId',
            'bulkExpireDate', 'bulkAddDays', 'bulkAddVolume', 'bulkReduceDays', 'bulkReduceVolume'
        ]);
        session()->flash('message', 'عملیات گروهی با موفقیت اعمال شد.');
    }

    public function resetForm()
    {
        $this->reset(['accountId', 'username', 'password', 'name', 'phonenumber', 'group_id']);$this->multi_login = 2;
        $this->service_group = 'l2tp_cisco';$this->is_enabled = true;
        $this->in_app = true;
        $this->can_create_wg = true;
        $this->can_create_op = true;
        $this->can_create_v2 = true;
        $this->isFormOpen = false;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $account = Accounts::findOrFail($id);
        $this->accountId =$account->id;
        $this->username =$account->username;
        $this->password =$account->password;
        $this->name =$account->name;
        $this->phonenumber =$account->phonenumber;
        $this->group_id =$account->group_id;
        $this->multi_login =$account->multi_login;
        $this->service_group =$account->service_group;
        $this->is_enabled =$account->is_enabled;
        $this->in_app =$account->in_app;
        $this->can_create_wg =$account->can_create_wg;
        $this->can_create_op =$account->can_create_op;
        $this->can_create_v2 =$account->can_create_v2;

        $this->isFormOpen = true;
    }

    public function save()
    {
        $this->validate([
            'username' => 'required|string|max:20|unique:accounts,username,' . $this->accountId,
            'password' => 'required|string|max:255',
            'group_id' => 'required|integer|exists:groups,id',
            'multi_login' => 'required|integer|min:1',
        ]);

        $group = Group::find($this->group_id);

        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'name' => $this->name,
            'phonenumber' => $this->phonenumber,
            'group_id' => $this->group_id,
            'multi_login' => $this->multi_login,
            'service_group' => $this->service_group,
            'is_enabled' => $this->is_enabled ? 1 : 0,
            'in_app' => $this->in_app ? 1 : 0,
            'can_create_wg' => $this->can_create_wg ? 1 : 0,
            'can_create_op' => $this->can_create_op ? 1 : 0,
            'can_create_v2' => $this->can_create_v2 ? 1 : 0,
        ];

        if (!$this->accountId) {$data['creator'] = auth()->id() ?? 1;
            $data['max_usage'] =$group->group_volume * 1073741824;
            $data['expire_type'] =$group->expire_type;

        }

        Accounts::updateOrCreate(['id' => $this->accountId],$data);
        session()->flash('message', $this->accountId ? 'اکانت ویرایش شد.' : 'اکانت جدید ساخته شد.');
        $this->resetForm();
    }

    public function toggleStatus($id)
    {
        $acc = Accounts::findOrFail($id);
        $acc->is_enabled = !$acc->is_enabled;
        $acc->save();
    }

    private function getAccountsQuery()
    {
        $query = Accounts::with(['group', 'creatorUser'])->latest('id');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('username', 'like', '\%' .$this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%')
                    ->orWhere('phonenumber', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            match ($this->filterStatus) {
                'online' => $query->where('is_online', 1),
                'offline' => $query->where('is_online', 0),                 'enabled' =>$query->where('is_enabled', 1),
                'disabled' => $query->where('is_enabled', 0),                 'expired' =>$query->where('expired', 1),
                default => null,
            };
        }

        if ($this->filterGroup) $query->where('group_id',$this->filterGroup);
        if ($this->filterCreator) $query->where('creator',$this->filterCreator);

        if ($this->filterDateFrom) {
            try {
                $gregorianFrom = Jalalian::fromFormat('Y/m/d',$this->filterDateFrom)->toCarbon()->startOfDay();
                $query->where('created_at', '>=',$gregorianFrom);
            } catch (\Exception $e) {}
        }

        if ($this->filterDateTo) {
            try {
                $gregorianTo = Jalalian::fromFormat('Y/m/d',$this->filterDateTo)->toCarbon()->endOfDay();
                $query->where('created_at', '<=',$gregorianTo);
            } catch (\Exception $e) {}
        }

        return $query;
    }

    public function render()
    {
        $accounts = $this->getAccountsQuery()->paginate($this->perPage);
        $groups = Group::where('is_enabled', 1)->orderBy('sort_order', 'asc')->get();
        $creators = User::where('is_active', 1)->whereIn('role',['agent','sub_agent','admin','manager'])->get();

        return view('livewire.admin.account-manager', compact('accounts', 'groups', 'creators'));
    }
}
