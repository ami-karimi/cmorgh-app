<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Group; // مدل جدول گروه ها
use App\Models\Charge; // برای لیست کردن رول‌های شارژ در دراپ‌داون

#[Title('مدیریت گروه‌ها و تعرفه‌ها | همراه سیمرغ')]
#[Layout('layouts.admin')]
class GroupManager extends Component
{
    public $groupId = null;
    public $isFormOpen = false;

    // فیلدهای دیتابیس
    public $name, $charge_id;
    public $price = 0, $price_reseler = 0;
    public $group_type = 'expire', $group_volume = 0;
    public $expire_type = 'days', $expire_value;
    public $multi_login = 200, $first_login = 1, $is_enabled = 1;

    public function resetForm()
    {
        $this->reset();
        $this->group_type = 'expire';
        $this->expire_type = 'days';
        $this->multi_login = 200;
        $this->first_login = 1;
        $this->is_enabled = 1;
    }

    public function edit($id)
    {
        $group = Group::findOrFail($id);

        $this->groupId = $group->id;
        $this->name = $group->name;
        $this->charge_id = $group->charge_id;
        $this->price = $group->price;
        $this->price_reseler = $group->price_reseler;
        $this->group_type = $group->group_type;
        $this->group_volume = $group->group_volume;
        $this->expire_type = $group->expire_type;
        $this->expire_value = $group->expire_value;
        $this->multi_login = $group->multi_login;
        $this->first_login = $group->first_login;
        $this->is_enabled = $group->is_enabled;

        $this->isFormOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'charge_id' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'price_reseler' => 'required|numeric|min:0',
            'group_volume' => 'required|numeric|min:0',
            'expire_value' => 'required|numeric|min:0',
        ]);

        Group::updateOrCreate(
            ['id' => $this->groupId],
            [
                'name' => $this->name,
                'charge_id' => $this->charge_id,
                'price' => $this->price,
                'price_reseler' => $this->price_reseler,
                'group_type' => $this->group_type,
                'group_volume' => $this->group_volume,
                'expire_type' => $this->expire_type,
                'expire_value' => $this->expire_value,
                'multi_login' => $this->multi_login,
                'first_login' => $this->first_login ? 1 : 0,
                'is_enabled' => $this->is_enabled ? 1 : 0,
                // برای ردیف‌های جدید، مقدار sort_order را به صورت پیش‌فرض در آخر قرار می‌دهیم
                'sort_order' => $this->groupId ? Group::find($this->groupId)->sort_order : Group::max('sort_order') + 1,
            ]
        );

        session()->flash('message', $this->groupId ? 'گروه کاربری ویرایش شد.' : 'گروه جدید با موفقیت ایجاد شد.');
        $this->resetForm();
    }

    // متد اختصاصی برای دریافت اکشن Drag & Drop از فرانت‌اند
    public function updateOrder($orderedItems)
    {
        foreach ($orderedItems as $item) {
            Group::where('id', $item['value'])->update(['sort_order' => $item['order']]);
        }
    }

    public function toggleStatus($id)
    {
        $group = Group::findOrFail($id);
        $group->is_enabled = !$group->is_enabled;
        $group->save();
    }

    public function render()
    {
        $groups = Group::orderBy('sort_order', 'asc')->get();
        $charges = Charge::where('status', 1)->get(); // فقط رول‌های فعال

        return view('livewire.admin.groups-list', compact('groups', 'charges'));
    }
}
