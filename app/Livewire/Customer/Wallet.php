<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use App\Models\TopupRequest;
use App\Models\AgentBankAccount;
use App\Models\Financial;
use App\Services\BankPaymentService;

#[Layout('layouts.customer')]
#[Title('کیف پول و شارژ خودکار')]
class Wallet extends Component
{
    public $amount = 100000;
    #[Locked] public $requestId = null;
    public function boot(): void { abort_unless(auth()->check() && auth()->user()->role === 'customer', 403); }
    public function mount(): void
    {
        $this->requestId = TopupRequest::where('user_id',auth()->id())->where('purpose','wallet')->active()->value('id');
    }
    private function bank()
    {
        return AgentBankAccount::where('is_show',1)->where('type','card')->whereNotNull('account_number')
            ->whereHas('user',fn ($q) => $q->whereIn('role',['manager','admin']))->orderBy('id')->first();
    }
    public function createRequest(): void
    {
        $this->validate(['amount'=>'required|integer|min:1000|max:100000000']);
        if (!$bank = $this->bank()) { $this->addError('amount','شماره حساب پیامکی برای شارژ خودکار هنوز تنظیم نشده است.'); return; }
        try {
            $request = app(BankPaymentService::class)->createRequest(auth()->id(), (int) $this->amount, null, $bank->account_number);
            $this->requestId = $request->id;
        } catch (\Throwable $e) {
            if (!$e instanceof \Illuminate\Validation\ValidationException) { report($e); }
            $this->addError('amount', $e instanceof \Illuminate\Validation\ValidationException ? collect($e->errors())->flatten()->first() : 'ثبت درخواست انجام نشد.');
        }
    }
    public function cancelRequest(): void
    {
        if ($this->requestId) { app(BankPaymentService::class)->cancelRequest($this->requestId, auth()->id()); }
    }
    public function checkStatus(): void
    {
        if (!$this->requestId) { return; }
        $request = TopupRequest::where('user_id',auth()->id())->find($this->requestId);
        if (!$request) { $this->requestId = null; return; }
        if ($request->status === 'completed') { session()->flash('success', 'کیف پول شما شارژ شد.'); }
    }
    public function newRequest(): void { $this->requestId = null; $this->resetValidation(); }
    public function render()
    {
        $request = $this->requestId ? TopupRequest::where('user_id',auth()->id())->find($this->requestId) : null;
        $quote = is_numeric($this->amount) && $this->amount > 0 && $this->amount <= 100000000
            ? app(\App\Services\PromotionService::class)->quote((int) $this->amount, auth()->user(), null, 'topup_bonus') : ['discount'=>0];
        return view('livewire.customer.wallet', [
            'bank'=>$this->bank(), 'topup'=>$request, 'bonusPreview'=>$quote['discount'],
            'transactions'=>Financial::where('for',auth()->id())->latest()->limit(30)->get(),
        ]);
    }
}
