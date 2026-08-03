<?php

namespace App\Livewire\Agent;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

#[Title('مدیریت مشتریان | پنل نمایندگی')]
#[Layout('layouts.agent')]
class CustomerManager extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // متغیرهای فرم ثبت/ویرایش مشتری
    public $isModalOpen = false;
    public $customerId = null;
    public $name, $phone, $email, $password;
    public $is_active = true;

    public function updatedSearch() { $this->resetPage(); }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['customerId', 'name', 'phone', 'email', 'password']);
        $this->is_active = true;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        // نماینده فقط می‌تواند مشتریان خودش را ویرایش کند
        $customer = User::where('creator', Auth::id())
            ->where('role', 'customer')
            ->findOrFail($id);

        $this->customerId = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->email = $customer->email;
        $this->is_active = $customer->is_active == 1;
        $this->password = '';

        $this->isModalOpen = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:16|unique:users,phone,' . $this->customerId,
            'email' => 'nullable|email|max:255|unique:users,email,' . $this->customerId,
        ];

        if (!$this->customerId) {
            $rules['password'] = 'required|string|min:6';
        } else {
            $rules['password'] = 'nullable|string|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'role' => 'customer',
            'is_active' => $this->is_active ? 1 : 0,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if (!$this->customerId) {
            $data['creator'] = Auth::id(); // ثبت به نام نماینده لاگین شده
        }

        User::updateOrCreate(['id' => $this->customerId], $data);

        $this->isModalOpen = false;
        session()->flash('message', $this->customerId ? 'اطلاعات مشتری بروزرسانی شد.' : 'مشتری جدید با موفقیت ثبت شد.');
    }

    public function toggleStatus($id)
    {
        $customer = User::where('creator', Auth::id())
            ->where('role', 'customer')
            ->findOrFail($id);

        $customer->is_active = $customer->is_active == 1 ? 0 : 1;
        $customer->save();
        session()->flash('message', 'وضعیت مشتری تغییر کرد.');
    }

    public function render()
    {
        // فیلتر مشتریان بر اساس نماینده لاگین شده و عبارت جستجو
        $query = User::with('vpnAccounts')
            ->where('creator', Auth::id())
            ->where('role', 'customer')
            ->latest('id');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $customers = $query->paginate($this->perPage);

        return view('livewire.agent.customer-manager', compact('customers'));
    }
}
