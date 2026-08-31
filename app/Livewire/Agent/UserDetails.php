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
    public $creatorName;

    // متغیرهای ویرایش پروفایل
    public $isEditModalOpen = false;
    public $editName, $editPhone, $editEmail, $editPassword, $editRole;

    // متغیرهای بخش مالی
    public $newPrice, $newDescription, $newType = 'plus';
    public $isTrxModalOpen = false;

    // فیلترهای تراکنش
    public $transactionFilter = 'all';
    public $transactionSearch = '';

    public function mount($id)
    {
        $currentAgentId = Auth::id();

        $subAgentIds = User::where('creator', $currentAgentId)->pluck('id')->toArray();
        $allowedCreators = array_merge([$currentAgentId], $subAgentIds);

        $this->customer = User::with('vpnAccounts.group')
            ->whereIn('creator', $allowedCreators)
            ->findOrFail($id);

        if ($this->customer->creator == $currentAgentId) {
            $this->creatorName = 'شما (پروفایل شخصی نماینده)';
        } else {
            $creatorUser = User::find($this->customer->creator);
            $this->creatorName = $creatorUser ? $creatorUser->name . ' (زیرنماینده)' : 'نامشخص';
        }
    }

    public function updatedTransactionFilter()
    {
        $this->resetPage();
    }

    public function updatedTransactionSearch()
    {
        $this->resetPage();
    }

    public function openEditModal()
    {
        $this->editName = $this->customer->name;
        $this->editPhone = $this->customer->phone;
        $this->editEmail = $this->customer->email;
        $this->editRole = $this->customer->role;
        $this->editPassword = '';

        $this->resetValidation();
        $this->isEditModalOpen = true;
    }

    public function updateProfile()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editPhone' => 'nullable|string|max:20',
            'editEmail' => 'nullable|email|max:255',
            'editRole' => 'required|in:customer,sub_agent',
            'editPassword' => 'nullable|min:6',
        ]);

        $this->customer->name = $this->editName;
        $this->customer->phone = $this->editPhone;
        $this->customer->email = $this->editEmail;
        $this->customer->role = $this->editRole;

        if (!empty($this->editPassword)) {
            $this->customer->password = Hash::make($this->editPassword);
        }

        $this->customer->save();
        $this->isEditModalOpen = false;
        session()->flash('profile_msg', 'مشخصات کاربر با موفقیت بروزرسانی شد.');
    }

    public function toggleUserStatus()
    {
        $this->customer->is_active = $this->customer->is_active == 1 ? 0 : 1;
        $this->customer->save();
        session()->flash('profile_msg', 'وضعیت دسترسی کاربر تغییر کرد.');
    }

    public function openTrxModal()
    {
        $this->reset(['newPrice', 'newDescription', 'newType']);
        $this->resetValidation();
        $this->isTrxModalOpen = true;
    }

    public function addTransaction()
    {
        $this->validate([
            'newPrice' => 'required|numeric|min:0',
            'newType' => 'required|in:plus,minus',
            'newDescription' => 'required|string|max:255',
        ]);

        // بررسی کسر بیش از موجودی
        if ($this->newType === 'minus') {
            $balance = $this->getBalance();
            if ($this->newPrice > $balance) {
                $this->addError('newPrice', 'مبلغ برداشت بیشتر از موجودی فعلی است.');
                return;
            }
        }

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

    private function getBalance()
    {
        $plus = Financial::where('for', $this->customer->id)
            ->whereIn('type', ['plus', 'plus_amn'])
            ->where('approved', 1)
            ->sum('price');
        $minus = Financial::where('for', $this->customer->id)
            ->whereIn('type', ['minus', 'minus_amn'])
            ->where('approved', 1)
            ->sum('price');
        return $plus - $minus;
    }

    public function render()
    {
        $query = Financial::where('for', $this->customer->id);

        // فیلتر نوع
        if ($this->transactionFilter === 'plus') {
            $query->whereIn('type', ['plus', 'plus_amn']);
        } elseif ($this->transactionFilter === 'minus') {
            $query->whereIn('type', ['minus', 'minus_amn']);
        }

        // جستجو در شرح
        if (!empty($this->transactionSearch)) {
            $query->where('description', 'like', '%' . $this->transactionSearch . '%');
        }

        $transactions = $query->latest()->paginate(10);

        $balance = $this->getBalance();

        return view('livewire.agent.user-details', [
            'balance' => $balance,
            'transactions' => $transactions,
        ]);
    }
}
