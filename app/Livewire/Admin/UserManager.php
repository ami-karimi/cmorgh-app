<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

#[Title('مدیریت مشتریان | همراه سیمرغ')]
#[Layout('layouts.admin')]
class UserManager extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public $selectedUsers = [];
    public $selectAll = false;
    public $bulkAction = '';
    public $newCreatorId = ''; // برای تغییر دسته‌جمعی نماینده

    // متغیرهای فرم مودال
    public $isFormOpen = false;
    public $userId = null;
    public $name,$phone, $email,$password;
    public $is_active = true;
    public $creator = 0; // سازنده در زمان ایجاد/ویرایش تکی

    public function updatedSearch() { $this->resetPage(); }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedUsers =$this->getUsersQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function create()
    {
        $this->resetForm();$this->isFormOpen = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $user = User::findOrFail($id);

        $this->userId =$user->id;
        $this->name =$user->name;
        $this->phone =$user->phone;
        $this->email =$user->email;
        $this->is_active =$user->is_active == 1;
        $this->creator =$user->creator ?? 0;
        $this->password = '';$this->isFormOpen = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:16|unique:users,phone,' . $this->userId,
            'email' => 'nullable|email|max:255|unique:users,email,' . $this->userId,
            'creator' => 'nullable|numeric'
        ];

        if (!$this->userId) {
            $rules['password'] = 'required\vert{}string\vert{}min:6';                  } else {$rules['password'] = 'nullable|string|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'role' => 'customer', // در اینجا نقش همیشه مشتری ثبت می‌شود
            'is_active' => $this->is_active ? 1 : 0,
            'creator' => $this->creator ?: 0,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->userId],$data);

        session()->flash('message', $this->userId ? 'اطلاعات مشتری ویرایش شد.' : 'مشتری جدید ثبت شد.');
        $this->resetForm();
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active =$user->is_active == 1 ? 0 : 1;
        $user->save();
        session()->flash('message', 'وضعیت کاربر تغییر کرد.');
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedUsers)) return;

        switch ($this->bulkAction) {
            case 'activate':
                User::whereIn('id', $this->selectedUsers)->update(['is_active' => 1]);
                break;
            case 'deactivate':
                User::whereIn('id', $this->selectedUsers)->update(['is_active' => 0]);
                break;
            case 'change_creator':
                if ($this->newCreatorId !== '') {
                    User::whereIn('id', $this->selectedUsers)->update(['creator' =>$this->newCreatorId]);
                } else {
                    session()->flash('error', 'لطفاً نماینده جدید را برای انتقال انتخاب کنید.');
                    return;
                }
                break;
            case 'delete':
                User::whereIn('id', $this->selectedUsers)->delete();
                break;
        }

        $this->reset(['selectedUsers', 'selectAll', 'bulkAction', 'newCreatorId']);
        session()->flash('message', 'عملیات گروهی با موفقیت اعمال شد.');
    }

    public function resetForm()
    {
        $this->reset(['userId', 'name', 'phone', 'email', 'password', 'creator']);$this->is_active = true;
        $this->isFormOpen = false;
        $this->resetValidation();
    }

    private function getUsersQuery()
    {
        // فراخوانی مشتریان عادی به همراه اکانت‌های متصل
        $query = User::with('vpnAccounts')->where('role', 'customer')->latest('id');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '\%' .$this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('vpnAccounts', function ($vpnQ) {
                        $vpnQ->where('username', 'like', '\%' .$this->search . '%');
                    });
            });
        }

        return $query;
    }

    public function render()
    {
        $users = $this->getUsersQuery()->paginate($this->perPage);

        // واکشی لیست تمامی نمایندگان و مدیران برای منوهای کشویی
        $agents = User::whereIn('role', ['admin', 'manager', 'agent', 'sub_agent'])->get();

        return view('livewire.admin.user-manager', compact('users', 'agents'));
    }
}
