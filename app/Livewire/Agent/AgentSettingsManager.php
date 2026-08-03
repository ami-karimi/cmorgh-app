<?php

namespace App\Livewire\Agent;

use App\Models\Group;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\AgentBankAccount;
use App\Models\AgentStore;
use App\Models\AgentPlanPrice;
use App\Models\AgentHiddenGroups;


#[Title('تنظیمات | پنل نمایندگی')]
#[Layout('layouts.agent')]
class AgentSettingsManager extends Component
{
    use WithFileUploads;

    public $activeTab = 'branding';

    public $brand_name, $custom_domain, $logo, $current_logo, $domain_status;

    public $is_store_active = false;
    public $store_title;
    public $support_id;

    public $bank_name, $account_name, $card_number, $sheba_number;

    public $sub_agent_markup = 0;
    public $sellingPrices = [];

    public function mount()
    {
        $user = Auth::user();

        $this->brand_name = $user->brand_name;
        $this->custom_domain = $user->custom_domain;
        $this->current_logo = $user->brand_logo;
        $this->domain_status = $user->domain_status;

        $store = $user->store()->firstOrCreate([
            'user_id' => $user->id
        ]);

        $this->is_store_active = $store->is_active;
        $this->store_title = $store->title;
        $this->support_id = $store->support_id;

        $this->sub_agent_markup = $user->sub_agent_markup ?? 0;

        $customPrices = AgentPlanPrice::where('agent_id', $user->id)->pluck('selling_price', 'group_id')->toArray();
        $hiddenGroups = AgentHiddenGroups::where('agent_id', $user->id)->pluck('group_id')->toArray();
        $groups = Group::where('is_enabled', 1)->whereNotIn('id', $hiddenGroups)->get();

        foreach ($groups as $group) {
            $defaultCost = $group->getFinalPriceFor(auth()->user());
            $this->sellingPrices[$group->id] = $customPrices[$group->id] ?? $defaultCost;
        }

    }


    public function saveSellingPrice($groupId)
    {
        $this->validate([
            "sellingPrices.$groupId" => 'required|numeric|min:0',
        ]);

        AgentPlanPrice::updateOrCreate(
            ['agent_id' => Auth::id(), 'group_id' => $groupId],
            ['selling_price' => $this->sellingPrices[$groupId]]
        );

        session()->flash("price_msg_$groupId", 'قیمت فروش ثبت شد.');
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetValidation();
    }

    public function saveBranding()
    {
        $user = Auth::user();

        $this->validate([
            'brand_name' => 'required|string|max:50',
            'custom_domain' => 'nullable|string|max:100|unique:users,custom_domain,' . $user->id,
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($user->custom_domain !== $this->custom_domain && !empty($this->custom_domain)) {
            $user->domain_status = 'pending';
            $this->domain_status = 'pending';
        }

        if ($this->logo) {
            if ($user->brand_logo) Storage::disk('public')->delete($user->brand_logo);
            $path = $this->logo->store('agent-logos', 'public');
            $user->brand_logo = $path;
            $this->current_logo = $path;
        }

        $user->brand_name = $this->brand_name;
        $user->custom_domain = strtolower(trim($this->custom_domain));
        $user->save();

        session()->flash('branding_msg', 'تنظیمات دامنه و برندینگ ذخیره شد.');
    }

    public function saveBankAccount()
    {
        $this->validate([
            'bank_name' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
            'card_number' => 'required|string|size:16',
            'sheba_number' => 'nullable|string|max:30',
        ]);

        AgentBankAccount::create([
            'user_id' => Auth::id(),
            'bank_name' => $this->bank_name,
            'account_name' => $this->account_name,
            'card_number' => $this->card_number,
            'sheba_number' => $this->sheba_number,
        ]);

        $this->reset(['bank_name', 'account_name', 'card_number', 'sheba_number']);
        session()->flash('bank_msg', 'حساب بانکی با موفقیت اضافه شد.');
    }

    public function deleteBankAccount($id)
    {
        AgentBankAccount::where('user_id', Auth::id())->where('id', $id)->delete();
        session()->flash('bank_msg', 'حساب بانکی حذف شد.');
    }

    public function saveStoreSettings()
    {
        $this->validate([
            'store_title' => 'nullable|string|max:100',
            'support_id' => 'nullable|string|max:50',
        ]);

        Auth::user()->store()->update([
            'is_active' => $this->is_store_active,
            'title' => $this->store_title,
            'support_id' => $this->support_id,
        ]);

        session()->flash('store_msg', 'تنظیمات فروشگاه اختصاصی شما بروزرسانی شد.');
    }

    public function saveMarkup()
    {
        $this->validate([
            'sub_agent_markup' => 'required|numeric|min:0|max:100',
        ]);

        Auth::user()->update(['sub_agent_markup' => $this->sub_agent_markup]);
        session()->flash('markup_msg', 'درصد سود شما از خریدهای زیرنمایندگان با موفقیت بروزرسانی شد.');
    }


    public function render()
    {
        $user = Auth::user();
        $bankAccounts = AgentBankAccount::where('user_id', Auth::id())->latest()->get();


        $hiddenGroups = AgentHiddenGroups::where('agent_id', $user->id)->pluck('group_id')->toArray();
        $availableGroups = Group::where('is_enabled', 1)->whereNotIn('id', $hiddenGroups)->get();
        $discountPercent = $user->discount_percent ?? 0;

        return view('livewire.agent.agent-settings-manager', compact('bankAccounts', 'availableGroups', 'discountPercent'))
            ->layout('layouts.agent', ['title' => 'تنظیمات و پیکربندی پنل']);
    }
}
