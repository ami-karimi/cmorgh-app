<?php

namespace App\Livewire\Admin;

use App\Models\Promotion;
use App\Models\Group;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('تخفیف‌ها و بونوس شارژ')]
class PromotionsManager extends Component
{
    public $editingId = null;
    public $name = '', $code = '', $kind = 'coupon', $audience = 'customer', $value_type = 'percent', $value = 10;
    public $min_amount = 0, $max_discount = '', $starts_at = '', $ends_at = '', $usage_limit = '', $per_user_limit = '';
    public $group_ids = [], $user_ids_text = '', $is_active = true;

    public function boot(): void { abort_unless(auth()->check() && in_array(auth()->user()->role, ['admin','manager'], true), 403); }

    public function edit($id): void
    {
        $p = Promotion::findOrFail($id);
        $this->editingId = $p->id;
        foreach (['name','code','kind','audience','value_type','value','min_amount','max_discount','usage_limit','per_user_limit','is_active'] as $field) { $this->$field = $p->$field ?? ''; }
        $this->group_ids = $p->group_ids ?? [];
        $this->user_ids_text = implode(',', $p->user_ids ?? []);
        $this->starts_at = \App\Support\JalaliDateTime::input($p->starts_at);
        $this->ends_at = \App\Support\JalaliDateTime::input($p->ends_at);
        $this->resetValidation();
    }

    public function updatedKind(): void
    {
        if ($this->kind === 'topup_bonus') { $this->group_ids = []; }
        if ($this->kind === 'plan_discount') { $this->usage_limit = ''; $this->per_user_limit = ''; }
        if ($this->kind !== 'coupon') { $this->code = ''; }
        $this->resetValidation();
    }

    public function resetForm(): void { $this->reset(); $this->resetValidation(); }

    public function save(): void
    {
        $this->code = strtoupper(trim($this->code));
        foreach (['max_discount','usage_limit','per_user_limit','starts_at','ends_at'] as $f) { if ($this->$f === '') { $this->$f = null; } }
        $data = $this->validate([
            'name'=>'required|string|max:255', 'kind'=>'required|in:coupon,plan_discount,topup_bonus',
            'audience'=>'required|in:customer,agent,all', 'value_type'=>'required|in:percent,fixed',
            'value'=>'required|integer|min:1|max:'.($this->value_type === 'percent' ? 100 : 100000000),
            'min_amount'=>'required|integer|min:0|max:100000000', 'max_discount'=>'nullable|integer|min:1|max:100000000',
            'code'=>($this->kind === 'coupon' ? 'required' : 'nullable').'|regex:/^[A-Z0-9_-]{3,64}$/|unique:promotions,code,'.($this->editingId ?: 'NULL'),
            'starts_at'=>'nullable|string|max:32', 'ends_at'=>'nullable|string|max:32',
            'usage_limit'=>'nullable|integer|min:1', 'per_user_limit'=>'nullable|integer|min:1',
            'group_ids'=>'array', 'group_ids.*'=>'integer|exists:groups,id', 'is_active'=>'boolean',
        ]);
        foreach (['starts_at','ends_at'] as $field) {
            try { $data[$field] = \App\Support\JalaliDateTime::parse($this->$field); }
            catch (\InvalidArgumentException $e) { $this->addError($field,$e->getMessage()); return; }
        }
        if ($data['starts_at'] && $data['ends_at'] && $data['ends_at'] <= $data['starts_at']) {
            $this->addError('ends_at','پایان کمپین باید بعد از زمان شروع باشد.'); return;
        }
        if ($this->kind === 'plan_discount' && ($this->usage_limit || $this->per_user_limit)) {
            $this->addError('usage_limit', 'تخفیف خودکار پلن با تاریخ محدود می‌شود؛ سقف دفعات فقط برای کد و بونوس است.'); return;
        }
        if ($this->kind === 'topup_bonus' && $this->group_ids) { $this->addError('group_ids', 'بونوس شارژ به پلن مرتبط نیست.'); return; }
        $ids = array_filter(array_map('trim', explode(',', $this->user_ids_text)));
        foreach ($ids as $id) {
            if (!ctype_digit($id) || !\App\Models\User::whereKey($id)->exists()) { $this->addError('user_ids_text', 'شناسه کاربران انتخاب‌شده معتبر نیست.'); return; }
        }
        $data['user_ids'] = array_map('intval', $ids);
        $data['code'] = $this->kind === 'coupon' ? $this->code : null;
        $data['group_ids'] = array_map('intval', $this->group_ids);
        $data['created_by'] = auth()->id();
        Promotion::updateOrCreate(['id'=>$this->editingId], $data);
        $this->resetForm(); session()->flash('success', 'کمپین ذخیره شد.');
    }

    public function toggle($id): void
    {
        $p = Promotion::findOrFail($id); $p->update(['is_active'=>!$p->is_active]);
    }

    public function render()
    {
        return view('livewire.admin.promotions-manager', ['promotions'=>Promotion::withCount('redemptions')->latest()->get(), 'groups'=>Group::orderBy('name')->get()]);
    }
}
