<?php
namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('ایجاد مدیر یا نماینده | همراه سیمرغ')]
#[Layout('layouts.admin')]
class ManagerCreate extends Component
{
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'agent';
    public bool $is_active = true;

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|max:100',
            'role' => 'required|in:manager,admin,agent,sub_agent',
        ], [
            'name.required' => 'نام الزامی است.',
            'phone.required' => 'شماره تماس الزامی است.',
            'phone.unique' => 'این شماره قبلاً ثبت شده است.',
            'password.min' => 'رمز عبور حداقل ۶ کاراکتر باشد.',
        ]);

        $creator = in_array($this->role, ['agent', 'sub_agent'], true) ? auth()->id() : 0;

        User::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email ?: null,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'creator' => $creator,
            'is_active' => $this->is_active ? 1 : 0,
        ]);

        session()->flash('message', 'مدیر/نماینده با موفقیت ایجاد شد.');
        return redirect()->route('admin.managers.list');
    }

    public function render()
    {
        return view('livewire.admin.manager-create')->with(['header' => 'ایجاد مدیر یا نماینده']);
    }
}
