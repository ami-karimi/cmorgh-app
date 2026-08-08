<?php

namespace App\Livewire\Agent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Financial;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
#[Title('مدیریت مالی | پنل نمایندگی')]
#[Layout('layouts.agent')]
class FinancialManager extends Component
{
    use WithFileUploads;

    public $activeTab = 'my_wallet'; // my_wallet OR sub_agents

    // متغیرهای فرم آپلود فیش نماینده
    public $myAmount, $myDescription,$myReceipt;

    // متغیرهای فرم شارژ/کسر مستقیم زیرنماینده
    public $subAgentId,$subAmount, $subType = 'plus',$subDescription;

    public function mount()
    {
        // در صورت نیاز مقداردهی اولیه
    }

    // ==========================================
    // 🔴 ۱. ثبت فیش واریزی توسط خود نماینده (برای مدیر)
    // ==========================================
    public function submitMyReceipt()
    {
        $this->validate([
            'myAmount' => 'required|numeric|min:1000',
            'myReceipt' => 'required|image|max:2048', // حداکثر 2 مگابایت
            'myDescription' => 'nullable|string|max:255',
        ]);

        $agent = Auth::user();
        $path =$this->myReceipt->store('attachments/payments', 'public');

        Financial::create([
            'creator'     => $agent->id,
            'for'         => $agent->id,
            'type'        => 'plus',
            'price'       => $this->myAmount,
            'description' => $this->myDescription ?? 'ثبت فیش واریزی',
            'attachment'  => $path,
            'approved'    => 0, // نیاز به تایید مدیر دارد
        ]);

        $this->reset(['myAmount', 'myReceipt', 'myDescription']);
        session()->flash('success', 'فیش شما با موفقیت ثبت شد و منتظر تایید مدیریت است.');
    }

    // ==========================================
    // 🔵 ۲. شارژ یا کسر مستقیم زیرنماینده توسط نماینده
    // ==========================================
    public function manageSubAgentBalance()
    {
        $this->validate([
            'subAgentId' => 'required|exists:users,id',
            'subAmount'  => 'required|numeric|min:1000',
            'subType'    => 'required|in:plus,minus',
            'subDescription' => 'nullable|string|max:255',
        ]);

        $agent = Auth::user();

        // اگر نماینده می‌خواهد زیرنماینده را شارژ کند، باید خودش موجودی کافی داشته باشد!
        if ($this->subType === 'plus' &&$agent->balance < $this->subAmount) {$this->addError('subAmount', 'موجودی کیف پول شما برای این شارژ کافی نیست!');
            return;
        }

        DB::transaction(function () use ($agent) {
            // ۱. ثبت تراکنش برای زیرنماینده (تایید شده)
            Financial::create([
                'creator'     => $agent->id,
                'for'         => $this->subAgentId,
                'type'        => $this->subType,
                'price'       => $this->subAmount,
                'description' => $this->subDescription ?? 'شارژ مستقیم توسط نماینده ارشد',
                'approved'    => 1,
            ]);

            // ۲. کسر/اضافه به کیف پول نماینده ارشد (بسیار مهم برای بالانس ماندن اقتصاد سیستم)
            $agentTransactionType =$this->subType === 'plus' ? 'minus' : 'plus';
            $agentDesc =$this->subType === 'plus' ? "انتقال شارژ به زیرنماینده (#{$this->subAgentId})" : "کسر از زیرنماینده و بازگشت به کیف پول (#{$this->subAgentId})";

            Financial::create([
                'creator'     => $agent->id,
                'for'         => $agent->id,
                'type'        => $agentTransactionType,
                'price'       => $this->subAmount,
                'description' => $agentDesc,
                'approved'    => 1,
            ]);
        });

        $this->reset(['subAgentId', 'subAmount', 'subDescription', 'subType']);
        session()->flash('success_sub', 'عملیات مالی روی زیرنماینده با موفقیت انجام شد.');
    }

    // ==========================================
    // 🟢 ۳. تایید یا رد فیشِ زیرنماینده
    // ==========================================
    public function toggleSubAgentReceipt($transactionId,$status)
    {
        $transaction = Financial::findOrFail($transactionId);$agent = Auth::user();

        // بررسی امنیتی: آیا این تراکنش واقعاً برای یکی از زیرنمایندگانِ همین نماینده است؟
        $isMySubAgent = User::where('creator', $agent->id)->where('id',$transaction->for)->exists();
        if (!$isMySubAgent) {
            return;
        }

        // اگر مدیر می‌خواهد فیش را تایید کند (status = 1)
        if ($status == 1 &&$transaction->approved == 0) {
            if ($agent->balance <$transaction->price) {
                session()->flash('error_sub', 'موجودی شما برای تایید این فیش کافی نیست (مبلغ فیش از کیف شما کسر می‌شود).');
                return;
            }

            DB::transaction(function () use ($agent,$transaction) {
                // ۱. تایید فیش زیرنماینده
                $transaction->update(['approved' => 1]);

                // ۲. کسر مبلغ از نماینده بالادستی
                Financial::create([
                    'creator'     => $agent->id,
                    'for'         => $agent->id,
                    'type'        => 'minus',
                    'price'       => $transaction->price,
                    'description' => "تایید فیش و کسر مبلغ برای زیرنماینده (#{$transaction->for})",
                    'approved'    => 1,
                ]);
            });
            session()->flash('success_sub', 'فیش تایید شد و مبلغ از کیف پول شما کسر و به زیرنماینده منتقل گردید.');
        }
        // اگر می‌خواهد رد کند (status = 2 یا حذف)
        else {
            $transaction->update(['approved' => 2]); // فرض بر این است که 2 یعنی رد شده
            session()->flash('success_sub', 'فیش رد شد.');
        }
    }

    public function render()
    {
        $agent = Auth::user();

        // تراکنش‌های خود نماینده
        $myTransactions = Financial::where('for',$agent->id)->latest()->get();

        // استخراج زیرنمایندگان
        $subAgents = User::where('creator',$agent->id)->where('role', 'sub_agent')->get();
        $subAgentIds =$subAgents->pluck('id')->toArray();

        // تراکنش‌های زیرنمایندگان (فقط plus و plus_amn برای بررسی فیش‌ها، یا کل تراکنش‌ها)
        $subTransactions = Financial::whereIn('for',$subAgentIds)->latest()->get();

        return view('livewire.agent.financial-manager', [
            'myTransactions' => $myTransactions,
            'subAgents' => $subAgents,
            'subTransactions' => $subTransactions,
            'balance' => $agent->balance, // از طریق متد getBalanceAttribute که قبلاً ساختیم
        ]);
    }


}
