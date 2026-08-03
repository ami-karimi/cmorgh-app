<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Financial; // تغییر به مدل صحیح شما

#[Title('پروفایل و جزئیات کاربر | همراه سیمرغ')]
#[Layout('layouts.admin')]
class UserDetails extends Component
{
    public User $user;

    // متغیرهای فرم تراکنش
    public $isTxModalOpen = false;
    public $txId = null;
    public $txType = 'plus';
    public $txPrice = '';
    public $txDesc = '';
    public $txApproved = true;

    public function mount($id)
    {
        $this->user = User::with(['vpnAccounts.group'])->findOrFail($id);
    }

    public function toggleUserStatus()
    {
        $this->user->is_active = $this->user->is_active == 1 ? 0 : 1;
        $this->user->save();
        session()->flash('message', 'وضعیت حساب کاربر بروزرسانی شد.');
    }

    // --- بخش مدیریت تراکنش‌ها ---

    // محاسبه موجودی کل کاربر (جمع واریزی‌های تایید شده منهای کسری‌ها)
    public function getBalanceProperty()
    {
        $plus = Financial::where('for', $this->user->id)
            ->whereIn('type', ['plus', 'plus_amn'])
            ->where('approved', 1)
            ->sum('price');

        $minus = Financial::where('for', $this->user->id)
            ->where('type', 'minus')
            ->where('approved', 1)
            ->sum('price');

        return $plus - $minus;
    }

    // باز کردن مودال تراکنش (برای ایجاد یا ویرایش)
    public function openTxModal($action = 'plus', $id = null)
    {
        $this->resetValidation();
        $this->reset(['txId', 'txType', 'txPrice', 'txDesc']);
        $this->txApproved = true;

        if ($id) {
            $tx = Financial::findOrFail($id);
            $this->txId = $tx->id;
            $this->txType = $tx->type;
            $this->txPrice = $tx->price;
            $this->txDesc = $tx->description;
            $this->txApproved = $tx->approved == 1;
        } else {
            $this->txType = $action;
        }

        $this->isTxModalOpen = true;
    }

    // ذخیره یا ویرایش تراکنش
    public function saveTransaction()
    {
        $this->validate([
            'txPrice' => 'required|numeric|min:0',
            'txType'  => 'required|in:plus,minus,plus_amn',
        ]);

        Financial::updateOrCreate(
            ['id' => $this->txId],
            [
                'creator'     => auth()->id(),
                'for'         => $this->user->id,
                'type'        => $this->txType,
                'price'       => $this->txPrice,
                'description' => $this->txDesc,
                'approved'    => $this->txApproved ? 1 : 0,
            ]
        );

        $this->isTxModalOpen = false;
        session()->flash('message', 'تراکنش با موفقیت ثبت/ویرایش شد.');
    }

    // تغییر وضعیت تایید یا رد تراکنش
    public function toggleTxApproval($id)
    {
        $tx = Financial::findOrFail($id);
        $tx->approved = $tx->approved == 1 ? 0 : 1;
        $tx->save();
        session()->flash('message', 'وضعیت تایید تراکنش تغییر کرد.');
    }

    // حذف تراکنش
    public function deleteTransaction($id)
    {
        Financial::findOrFail($id)->delete();
        session()->flash('message', 'تراکنش حذف شد.');
    }

    public function render()
    {
        // فراخوانی تراکنش‌های کاربر (با استفاده از مدل Financial)
        $transactions = Financial::where('for', $this->user->id)->latest()->get();

        return view('livewire.admin.user-details', compact('transactions'));
    }
}
