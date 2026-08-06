<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\AgentHiddenGroups;
use App\Models\Financial;
use App\Models\Accounts;

#[Title('پروفایل همکار | همراه سیمرغ')]
#[Layout('layouts.admin')]
class ManagerEdit extends Component
{
    use WithPagination;

    public User $manager;
    public $discount_percent = 0;
    public $sub_agent_markup = 0;


    public $store_title = '';
    public $store_support_id = '';
    public $store_is_active = 1;

    // --- متغیرهای فرم ویرایش پروفایل ---
    public $name, $phone,$email, $password,$is_active;

    // --- متغیرهای فیلتر و افزودن تراکنش ---
    public $trxType = '',$trxSearch = '';
    public $newPrice, $newDescription,$newType = 'plus';

    // === متغیرهای ویرایش تراکنش ===
    public $editingTrxId = null;
    public $editPrice,$editType, $editDescription,$editApproved;

    public $hiddenGroups = [];

    // === متغیرهای جدید دامنه و فروشگاه نماینده ===
    public $custom_domain = '';
    public $brand_name = '';
    public $domain_status = 'none'; // none, pending, approved, rejected
    public $is_store_active = true;

    public function updatingTrxType() { $this->resetPage('trxPage'); }
    public function updatingTrxSearch() { $this->resetPage('trxPage'); }

    public function mount(User $manager)
    {
        $this->manager = $manager;
        $this->name = $manager->name;
        $this->phone = $manager->phone;
        $this->email = $manager->email;
        $this->is_active = $manager->is_active ?? true;
        $this->discount_percent = $manager->discount_percent ?? 0;
        $this->sub_agent_markup = $manager->sub_agent_markup ?? 0;

        // تنظیمات هماهنگ شده با دیتابیس شما
        $this->custom_domain = $manager->custom_domain ?? '';
        $this->brand_name = $manager->brand_name ?? '';
        $this->domain_status = $manager->domain_status ?? 'none';

        // اگر فیلدی برای روشن/خاموش کردن کل فروشگاه دارید (در صورت عدم وجود این متغیر را نادیده بگیرید)
        $this->is_store_active = $manager->is_store_active ?? true;

        $this->hiddenGroups = AgentHiddenGroups::where('agent_id', $this->manager->id)
            ->pluck('group_id')
            ->toArray();

        $agentStore = \Illuminate\Support\Facades\DB::table('agent_stores')
            ->where('user_id', $manager->id)
            ->first();

        $this->store_title = $agentStore->title ?? '';
        $this->store_support_id = $agentStore->support_id ?? '';
        $this->store_is_active = $agentStore->is_active ?? 1;

    }

    public function updateStoreInfo()
    {
        $this->validate([
            'store_title'      => 'nullable|string|max:255',
            'store_support_id' => 'nullable|string|max:255',
        ]);

        \Illuminate\Support\Facades\DB::table('agent_stores')->updateOrInsert(
            ['user_id' => $this->manager->id],
            [
                'title'      => $this->store_title,
                'support_id' => $this->store_support_id,
                'is_active'  => $this->store_is_active ? 1 : 0,
                'updated_at' => now(),
            ]
        );

        session()->flash('store_info_message', 'اطلاعات فروشگاه با موفقیت بروزرسانی شد.');
    }

    public function toggleStoreStatus()
    {
        $this->store_is_active = !$this->store_is_active;

        \Illuminate\Support\Facades\DB::table('agent_stores')->updateOrInsert(
            ['user_id' => $this->manager->id],
            [
                'is_active'  => $this->store_is_active ? 1 : 0,
                'updated_at' => now(),
            ]
        );

        $statusText = $this->store_is_active ? 'فعال' : 'غیرفعال';
        session()->flash('store_info_message', "نمایش فروشگاه نماینده {$statusText} شد.");
    }

    public function toggleGroupVisibility($groupId)
    {
        if (in_array($groupId,$this->hiddenGroups)) {
            AgentHiddenGroups::where('agent_id', $this->manager->id)
                ->where('group_id', $groupId)
                ->delete();

            $this->hiddenGroups = array_diff($this->hiddenGroups, [$groupId]);
            session()->flash('group_message', 'گروه سرویس برای این نماینده قابل نمایش شد.');
        } else {
            AgentHiddenGroups::insert([
                'agent_id'   => $this->manager->id,
                'group_id'   => $groupId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->hiddenGroups[] =$groupId;
            session()->flash('group_message', 'گروه سرویس برای این نماینده مخفی شد.');
        }
    }

    public function updateDiscount()
    {
        $this->validate([
            'discount_percent' => 'required|numeric|min:0|max:100',
            'sub_agent_markup' => 'nullable|numeric|min:0|max:500',
        ]);

        $this->manager->update([
            'discount_percent' => $this->discount_percent,
            'sub_agent_markup' => $this->sub_agent_markup ?? 0,
        ]);

        session()->flash('discount_message', 'درصد تخفیف و کارمزد زیر‌نماینده با موفقیت بروزرسانی شد.');
    }

// --- متدهای مدیریت فروشگاه و دامنه ---
    public function updateStoreDomain()
    {
        $this->validate([
            'custom_domain' => 'nullable|string|max:255',
            'brand_name'    => 'nullable|string|max:255',
        ]);

        $cleanDomain = strtolower(trim($this->custom_domain));

        $this->manager->update([
            'custom_domain' => $cleanDomain,
            'brand_name'    => $this->brand_name,
        ]);

        session()->flash('store_message', 'تنظیمات برند و دامنه با موفقیت بروزرسانی شد.');
    }
    public function approveDomain()
    {
        $this->domain_status = 'approved';
        $this->manager->update([
            'domain_status' => 'approved',
        ]);

        session()->flash('store_message', 'دامنه اختصاصی نماینده با موفقیت تایید شد.');
    }

    public function rejectDomain()
    {
        $this->domain_status = 'rejected';
        $this->manager->update([
            'domain_status' => 'rejected',
        ]);

        session()->flash('store_message', 'درخواست دامنه نماینده رد شد.');
    }

    public function toggleStoreActive()
    {
        $this->is_store_active = !$this->is_store_active;
        $this->manager->update([
            'is_store_active' => $this->is_store_active,
            'store_enabled'   => $this->is_store_active,
        ]);

        $statusText =$this->is_store_active ? 'فعال' : 'غیرفعال';
        session()->flash('store_message', "فروشگاه اختصاصی نماینده {$statusText} شد.");
    }

    public function toggleStatus()
    {
        $this->manager->is_active = !$this->manager->is_active;
        $this->manager->save();

        $this->is_active =$this->manager->is_active;

        $message =$this->manager->is_active ? 'حساب کاربری با موفقیت فعال شد.' : 'حساب کاربری موقتاً تعلیق شد.';
        session()->flash('profile_message', $message);
    }

    public function updateProfile()
    {
        // تبدیل رشته خالی به null برای جلوگیری از ارور دیتابیس (وقتی ایمیل خالی ارسال میشه)
        $this->email = empty($this->email) ? null : $this->email;

        $rules = [
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $this->manager->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $this->manager->id,
        ];

        if (!empty($this->password)) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        $data = [
            'name'      => $this->name,
            'phone'     => $this->phone,
            'email'     => $this->email,
        ];

        if (!empty($this->password)) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($this->password);
        }

        $this->manager->update($data);

        $this->password = ''; // پاک کردن فیلد پسورد بعد از ذخیره

        session()->flash('profile_message', 'اطلاعات هویتی با موفقیت بروزرسانی شد.');
    }


    public function addTransaction()
    {
        $this->validate([
            'newPrice'       => 'required|numeric|min:1',
            'newType'        => 'required|in:plus,minus,plus_amn,minus_amn',
            'newDescription' => 'required|string|max:255',
        ]);

        Financial::create([
            'creator'     => auth()->id(),
            'for'         => $this->manager->id,
            'price'       => $this->newPrice,
            'type'        => $this->newType,
            'description' => $this->newDescription,
            'approved'    => 1,
        ]);

        $this->reset(['newPrice', 'newDescription']);
        $this->newType = 'plus';$this->manager->unsetRelation('financials');

        session()->flash('trx_message', 'تراکنش جدید با موفقیت ثبت شد.');
    }

    public function editTransaction($trxId)
    {
        $trx = Financial::findOrFail($trxId);
        if ($trx->for !==$this->manager->id) return;

        $this->editingTrxId   =$trx->id;
        $this->editPrice      =$trx->price;
        $this->editType       =$trx->type;
        $this->editDescription=$trx->description;
        $this->editApproved   =$trx->approved;
    }

    public function cancelEdit()
    {
        $this->reset(['editingTrxId', 'editPrice', 'editType', 'editDescription', 'editApproved']);
    }

    public function updateTransaction()
    {
        $this->validate([
            'editPrice'       => 'required|numeric|min:0',
            'editType'        => 'required|in:plus,minus,plus_amn,minus_amn',
            'editDescription' => 'required|string|max:255',
            'editApproved'    => 'required|boolean',
        ]);

        $trx = Financial::findOrFail($this->editingTrxId);

        if ($trx->for === $this->manager->id) {$trx->update([
            'price'       => $this->editPrice,
            'type'        => $this->editType,
            'description' => $this->editDescription,
            'approved'    => $this->editApproved,
        ]);
        }

        $this->cancelEdit();$this->manager->unsetRelation('financials');
        session()->flash('trx_message', 'تراکنش با موفقیت ویرایش شد.');
    }

    public function render()
    {
        $balance =$this->manager->balance;
        $debt = $balance < 0 ? abs($balance) : 0;

        $transactions =$this->manager->financials()
            ->when($this->trxType, function ($query) { $query->where('type',$this->trxType); })
            ->when($this->trxSearch, function ($query) { $query->where('description', 'like', '\%' .$this->trxSearch . '%'); })
            ->latest()
            ->paginate(5, pageName: 'trxPage');

        $parentAgent = $this->manager->creator ? User::find($this->manager->creator) : null;
        $subAgentsCount = User::where('creator',$this->manager->id)->where('role', 'sub_agent')->count();

        // 🟢 محاسبه واقعی اکانت‌های صادر شده توسط این نماینده و زیرنمایندگانش
        $subAgentIds = User::where('creator', $this->manager->id)->pluck('id')->toArray();$allAgentIds = array_merge([$this->manager->id],$subAgentIds);

        $accountsQuery = Accounts::whereIn('creator',$allAgentIds);

        $totalAccounts = (clone$accountsQuery)->count();

        $activeAccounts = (clone$accountsQuery)
            ->where('is_enabled', 1)
            ->where(function($q) {$q->whereNull('expire_date')
                ->orWhere('expire_date', '>', now());
            })->count();

        $allGroups = \App\Models\Group::where('is_enabled', 1)->get();

        return view('livewire.admin.manager-edit', [
            'balance'        => $balance,
            'debt'           => $debt,
            'transactions'   => $transactions,
            'parentAgent'    => $parentAgent,
            'subAgentsCount' => $subAgentsCount,
            'totalAccounts'  => $totalAccounts,
            'activeAccounts' => $activeAccounts,
            'allGroups'      => $allGroups,
        ])->with(['header' => 'مشاهده و ویرایش پروفایل همکار']);
    }
}
