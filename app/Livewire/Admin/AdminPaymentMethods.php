<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminPaymentMethods extends Component
{
    public $isModalOpen = false;
    public $methodId = null;

    // فیلد نوع روش پرداخت
    public $type = 'card'; // card (تومانی), crypto (ارزی/کریپتو), custom (سایر)
    public $bank_name, $account_name, $card_number, $sheba_number;
    public $is_show = true;

    public function openModal($id = null)
    {
        $this->resetValidation();
        if ($id) {
            $method = DB::table('agent_bank_accounts')->where('id', $id)->first();
            $this->methodId = $method->id;
            $this->type = $method->type ?? (str_contains(strtolower($method->bank_name ?? ''), 'crypto') ? 'crypto' : 'card');
            $this->bank_name = $method->bank_name ?? '';
            $this->account_name = $method->account_name;
            $this->card_number = $method->card_number;
            $this->sheba_number = $method->sheba_number ?? '';
            $this->is_show = isset($method->is_show) ? (bool)$method->is_show : true;
        } else {
            $this->reset(['methodId', 'bank_name', 'account_name', 'card_number', 'sheba_number']);
            $this->type = 'card';
            $this->is_show = true;
        }
        $this->isModalOpen = true;
    }

    public function toggleShow($id)
    {
        $method = DB::table('agent_bank_accounts')->where('id', $id)->first();
        if ($method) {
            $currentStatus = isset($method->is_show) ? $method->is_show : 1;
            DB::table('agent_bank_accounts')->where('id', $id)->update([
                'is_show' => $currentStatus ? 0 : 1,
                'updated_at' => now()
            ]);
            session()->flash('success', 'وضعیت نمایش روش پرداخت تغییر کرد.');
        }
    }

    public function save()
    {
        if ($this->type === 'card') {
            $this->validate([
                'account_name' => 'required|string|max:255',
                'card_number'  => 'required|string|max:30',
                'bank_name'    => 'nullable|string|max:255',
                'sheba_number' => 'nullable|string|max:50',
            ], [
                'account_name.required' => 'نام صاحب حساب الزامی است.',
                'card_number.required'  => 'شماره کارت الزامی است.',
            ]);
        } else {
            // اعتبارسنجی برای پرداخت ارزی / کریپتو
            $this->validate([
                'account_name' => 'required|string|max:255', // نام ارز / شبکه مثلا Tether (TRC20)
                'card_number'  => 'required|string|max:255', // آدرس ولت
            ], [
                'account_name.required' => 'عنوان ارز / شبکه (مثلاً تتر TRC20) الزامی است.',
                'card_number.required'  => 'آدرس کیف‌پول (Wallet Address) الزامی است.',
            ]);
        }

        $data = [
            'user_id'      => Auth::id(), // ثبت آیدی ایجاد کننده
            'type'         => $this->type,
            'bank_name'    => $this->type === 'crypto' ? ($this->bank_name ?: 'کیف پول ارزی') : $this->bank_name,
            'account_name' => $this->account_name,
            'card_number'  => $this->card_number,
            'sheba_number' => $this->sheba_number,
            'is_show'      => $this->is_show ? 1 : 0,
            'updated_at'   => now()
        ];

        if ($this->methodId) {
            DB::table('agent_bank_accounts')->where('id', $this->methodId)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('agent_bank_accounts')->insert($data);
        }

        $this->isModalOpen = false;
        session()->flash('success', 'روش پرداخت با موفقیت ثبت/ویرایش شد.');
    }

    public function delete($id)
    {
        DB::table('agent_bank_accounts')->where('id', $id)->delete();
        session()->flash('success', 'روش پرداخت حذف شد.');
    }

    public function render()
    {
        $methods = DB::table('agent_bank_accounts')
            ->latest('id')
            ->get()
            ->map(function ($method) {
                // دریافت مشخصات ایجادکننده
                $creator = User::find($method->user_id);
                $method->creator_name = $creator ? $creator->name : 'مدیر کل';
                return $method;
            });

        return view('livewire.admin.admin-payment-methods', compact('methods'))
            ->layout('layouts.admin')->title('مدیریت شیوه‌های پرداخت');
    }
}
