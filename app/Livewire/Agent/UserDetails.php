<?php

namespace App\Livewire\Agent;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Financial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

#[Title('پرونده جامع مشتری و سرویس‌ها | پنل نمایندگی')]
#[Layout('layouts.agent')]
class UserDetails extends Component
{
    use WithPagination;

    public User $customer;
    public $creatorName; // برای ذخیره نام ایجاد کننده

    // متغیرهای ویرایش پروفایل کاربر
    public $isEditModalOpen = false;
    public $editName, $editPhone,$editEmail, $editPassword,$editRole;

    // متغیرهای بخش مالی
    public $newPrice, $newDescription,$newType = 'plus';
    public $isTrxModalOpen = false;

    public function mount($id)
    {
        $currentAgentId = Auth::id();

        // پیدا کردن ID تمام زیرنمایندگان این شخص
        $subAgentIds = User::where('creator', $currentAgentId)->pluck('id')->toArray();$allowedCreators = array_merge([$currentAgentId],$subAgentIds);

        // دریافت کاربر با شرط امنیت دسترسی
        $this->customer = User::with('vpnAccounts')
            ->whereIn('creator', $allowedCreators)
            ->findOrFail($id);

        // تشخیص نام ایجاد کننده (آیا خود نماینده ساخته یا زیرمجموعه‌اش؟)
        if ($this->customer->creator == $currentAgentId) {$this->creatorName = 'شما (پروفایل شخصی نماینده)';
        } else {
            $creatorUser = User::find($this->customer->creator);$this->creatorName = $creatorUser ? $creatorUser->name . ' (زیرنماینده)' : 'نامشخص';
        }
    }

    // باز کردن مودال ویرایش مشخصات
    public function openEditModal()
    {
        $this->editName =$this->customer->name;
        $this->editPhone =$this->customer->phone;
        $this->editEmail =$this->customer->email;
        $this->editRole =$this->customer->role;
        $this->editPassword = ''; // رمز عبور فقط در صورت وارد شدن تغییر می‌کند

        $this->resetValidation();$this->isEditModalOpen = true;
    }

    // ذخیره تغییرات مشخصات کاربر
    public function updateProfile()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editPhone' => 'nullable|string|max:20',
            'editEmail' => 'nullable|email|max:255',
            'editRole' => 'required|in:customer,sub_agent',
            'editPassword' => 'nullable|min:6',
        ]);

        $this->customer->name =$this->editName;
        $this->customer->phone =$this->editPhone;
        $this->customer->email =$this->editEmail;
        $this->customer->role =$this->editRole;

        if (!empty($this->editPassword)) {
            $this->customer->password = Hash::make($this->editPassword);
        }

        $this->customer->save();$this->isEditModalOpen = false;
        session()->flash('profile_msg', 'مشخصات کاربر با موفقیت بروزرسانی شد.');
    }

    // تغییر وضعیت کاربر (فعال/مسدود)
    public function toggleUserStatus()
    {
        $this->customer->is_active =$this->customer->is_active == 1 ? 0 : 1;
        $this->customer->save();
        session()->flash('profile_msg', 'وضعیت دسترسی کاربر تغییر کرد.');
    }

    // باز کردن مودال تراکنش
    public function openTrxModal()
    {
        $this->reset(['newPrice', 'newDescription', 'newType']);
        $this->resetValidation();$this->isTrxModalOpen = true;
    }

    // ثبت تراکنش
    public function addTransaction()
    {
        $this->validate([
            'newPrice' => 'required|numeric|min:0',
            'newType' => 'required|in:plus,minus',
            'newDescription' => 'required|string|max:255',
        ]);

        Financial::create([
            'creator' => Auth::id(),
            'for' => $this->customer->id,
            'type' => $this->newType,
            'price' => $this->newPrice,
            'description' => $this->newDescription,
            'approved' => 1,
        ]);

        $this->isTrxModalOpen = false;
        session()->flash('trx_msg', 'تراکنش مالی با موفقیت ثبت شد.');
    }

    public function render()
    {
        $plus = Financial::where('for', $this->customer->id)->whereIn('type', ['plus', 'plus_amn'])->where('approved', 1)->sum('price');$minus = Financial::where('for', $this->customer->id)->whereIn('type', ['minus', 'minus_amn'])->where('approved', 1)->sum('price');$balance = $plus -$minus;

        $transactions = Financial::where('for',$this->customer->id)->latest()->paginate(5);

        return view('livewire.agent.user-details', [
            'balance' => $balance,
            'transactions' => $transactions,
        ]);
    }
}
