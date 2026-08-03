<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Charge;
use App\Models\ChargeRole;

#[Title('مدیریت بسته‌های شارژ | همراه سیمرغ')]
#[Layout('layouts.admin')]
class ChargeManager extends Component
{
    use WithPagination;

    public $chargeId = null;
    public $name = '';
    public $status = 1;

    // آرایه‌ای برای نگهداری چندین رول (بازه زمانی)
    public $roles = [];

    public $isFormOpen = false;

    public function mount()
    {
        // در ابتدا حداقل یک فرم رول خالی وجود داشته باشد
        if (empty($this->roles)) {
            $this->addRole();
        }
    }

    // اضافه کردن یک بازه زمانی (رول) جدید به فرم
    public function addRole()
    {
        $this->roles[] = [
            'id' => null,
            'ras_access' => 'all',
            'max_bandwidth' => 0,
            'rate_limit' => 0,
            'start_at' => '00:00:00',
            'end_at' => '23:59:59',
            'access_days' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        ];
    }

    // حذف یک بازه زمانی از فرم
    public function removeRole($index)
    {
        unset($this->roles[$index]);
        $this->roles = array_values($this->roles); // مرتب‌سازی مجدد ایندکس‌ها
    }

    public function resetForm()
    {
        $this->reset(['chargeId', 'name', 'status']);
        $this->roles = [];
        $this->addRole(); // ایجاد یک رول پیش‌فرض
        $this->isFormOpen = false;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $charge = Charge::with('roles')->findOrFail($id);

        $this->chargeId = $charge->id;
        $this->name = $charge->name;
        $this->status = $charge->status;

        $this->roles = $charge->roles->map(function($role) {
            return [
                'id' => $role->id,
                'ras_access' => $role->ras_access,
                'max_bandwidth' => $role->max_bandwidth,
                'rate_limit' => $role->rate_limit,
                'start_at' => $role->start_at,
                'end_at' => $role->end_at,
                'access_days' => explode(',', $role->access_days),
            ];
        })->toArray();

        if (empty($this->roles)) $this->addRole();

        $this->isFormOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:600',
            'status' => 'required|boolean',
            'roles' => 'required|array|min:1',
            'roles.*.max_bandwidth' => 'required|numeric|min:0',
            'roles.*.rate_limit' => 'required|numeric|min:0',
            'roles.*.start_at' => 'required|date_format:H:i:s',
            'roles.*.end_at' => 'required|date_format:H:i:s',
            'roles.*.access_days' => 'required|array|min:1',
        ], [
            'roles.*.access_days.required' => 'انتخاب حداقل یک روز برای هر بازه الزامی است.',
        ]);

        // 1. ذخیره اطلاعات اصلی بسته
        $charge = Charge::updateOrCreate(
            ['id' => $this->chargeId],
            ['name' => $this->name, 'status' => $this->status]
        );

        // 2. مدیریت رول‌های (بازه های زمانی) بسته
        $savedRoleIds = [];

        foreach ($this->roles as $roleData) {
            $role = ChargeRole::updateOrCreate(
                [
                    'id' => $roleData['id'],
                    'charge_id' => $charge->id
                ],
                [
                    'ras_access' => $roleData['ras_access'] ?? 'all',
                    'max_bandwidth' => $roleData['max_bandwidth'],
                    'rate_limit' => $roleData['rate_limit'],
                    'access_days' => implode(',', $roleData['access_days']),
                    'start_at' => $roleData['start_at'],
                    'end_at' => $roleData['end_at'],
                ]
            );
            $savedRoleIds[] = $role->id;
        }

        // حذف رول‌هایی که در دیتابیس هستند اما کاربر در فرم آن‌ها را پاک کرده است
        ChargeRole::where('charge_id', $charge->id)
            ->whereNotIn('id', $savedRoleIds)
            ->delete();

        session()->flash('message', $this->chargeId ? 'بسته شارژ با موفقیت ویرایش شد.' : 'بسته شارژ جدید ایجاد شد.');
        $this->resetForm();
    }

    public function delete($id)
    {
        $charge = Charge::findOrFail($id);
        ChargeRole::where('charge_id', $charge->id)->delete();
        $charge->delete();

        session()->flash('message', 'بسته با موفقیت حذف شد.');
    }

    public function render()
    {
        $charges = Charge::with('roles')->latest('id')->paginate(10);
        return view('livewire.admin.charge-manager', compact('charges'))->with(['header' => 'مدیریت بسته‌های شارژ']);
    }
}
