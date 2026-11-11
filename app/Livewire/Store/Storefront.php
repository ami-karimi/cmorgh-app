<?php

namespace App\Livewire\Store;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Locked;
use App\Models\User;
use App\Models\Group;
use App\Models\AgentStore;
use App\Models\StoreOrder;
use App\Models\AgentBankAccount;
use App\Services\StoreCheckoutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Storefront extends Component
{
    use WithFileUploads;
    public $storeData = [];
    #[Locked] public $sellerId = null;
    public $bankDetails = null;
    public $step = 1, $selectedProtocol = 'all', $selectedPlan = null;
    #[Locked] public $selectedPlanPrice = 0;
    public $name, $phone, $email, $receipt;
    public $paymentMethod = 'receipt', $couponCode = '', $discountAmount = 0;
    #[Locked] public $checkoutKey = '';
    #[Locked] public $pendingOrderId = null;

    public function mount(): void
    {
        $host = preg_replace('/^www\./', '', request()->getHost());
        $agent = User::where('custom_domain', $host)->where('domain_status', 'approved')->first();
        if (auth()->check()) { $agent = auth()->user()->parentAgent; }
        if ($agent && in_array($agent->role, ['agent','sub_agent'], true)) {
            $store = AgentStore::where('user_id', $agent->id)->where('is_active', 1)->first();
            abort_unless($store, 403, 'این فروشگاه غیرفعال است.');
            $this->sellerId = $agent->id;
            $this->storeData = ['brand_name'=>$agent->brand_name ?: 'فروشگاه VPN', 'logo'=>$agent->brand_logo ? asset('storage/'.$agent->brand_logo) : null,
                'support_link'=>$store->support_id, 'description'=>$store->title ?: 'خرید و تحویل سرویس'];
        } else {
            $this->storeData = ['brand_name'=>setting('SITE_TITLE','همراه سیمرغ'), 'logo'=>setting('SITE_LOGO'),
                'support_link'=>setting('TELEGRAM_SUPPORT'), 'description'=>'پلتفرم مدیریت و فروش سرویس'];
        }
        $this->bankDetails = $this->resolveBank();
        $this->checkoutKey = (string) Str::uuid();
        if (auth()->check()) {
            $this->name = auth()->user()->name; $this->phone = auth()->user()->phone; $this->email = auth()->user()->email;
            $this->paymentMethod = 'wallet';
        }
    }

    private function resolveBank()
    {
        $query = AgentBankAccount::where('is_show',1)->where('type','card');
        if ($this->sellerId) {
            $bank = (clone $query)->where('user_id',$this->sellerId)->first();
            if ($bank) { return $bank; }
        }
        return $query->whereHas('user', fn ($q) => $q->whereIn('role',['manager','admin']))->orderBy('id')->first();
    }

    public function selectPlan($planId): void
    {
        $this->selectedPlan = Group::where('is_enabled',1)->findOrFail($planId);
        if ($this->sellerId && DB::table('agent_hidden_groups')->where('agent_id',$this->sellerId)->where('group_id',$planId)->exists()) { abort(403); }
        $this->couponCode = ''; $this->discountAmount = 0; $this->checkoutKey = (string) Str::uuid();
        $this->refreshQuote(); $this->step = 2;
    }

    private function refreshQuote(): void
    {
        if (!$this->selectedPlan) { return; }
        if (auth()->check()) {
            $quote = app(StoreCheckoutService::class)->quote(auth()->user(), $this->selectedPlan->id, $this->sellerId, $this->couponCode);
            $this->selectedPlanPrice = $quote['total']; $this->discountAmount = $quote['discount'];
        } else {
            $this->selectedPlanPrice = $this->selectedPlan->getSellingPriceFor($this->sellerId ? User::find($this->sellerId) : null);
        }
    }

    public function applyCoupon()
    {
        if (!auth()->check()) { return redirect()->route('login'); }
        $this->validate(['couponCode'=>'nullable|string|max:64']);
        $this->resetValidation();
        try { $this->refreshQuote(); }
        catch (\Illuminate\Validation\ValidationException $e) { $this->addError('couponCode', collect($e->errors())->flatten()->first()); }
    }

    public function removeCoupon(): void { $this->couponCode = ''; $this->resetValidation(); $this->refreshQuote(); }

    public function goToPayment()
    {
        if (!auth()->check()) { return redirect()->route('login'); }
        $this->refreshQuote(); $this->step = 3;
    }
    public function previousStep(): void { if ($this->step > 1 && $this->step < 4) { $this->step--; } }

    public function submitOrder()
    {
        if (!auth()->check()) { return redirect()->route('login'); }
        $this->validate(['paymentMethod'=>'required|in:wallet,bank,receipt', 'couponCode'=>'nullable|string|max:64']);
        $path = null;
        if ($this->paymentMethod === 'receipt') {
            $this->validate(['receipt'=>'required|image|max:2048']);
            $path = $this->receipt->store('attachments/orders','public');
        }
        try {
            $bank = $this->resolveBank();
            $order = app(StoreCheckoutService::class)->create(auth()->user(), (int) $this->selectedPlan?->id,
                $this->sellerId, $this->paymentMethod, $this->checkoutKey, $this->couponCode, $path, $bank?->account_number, (int) $this->selectedPlanPrice);
            $this->pendingOrderId = $order->id; $this->step = 4;
        } catch (\Throwable $e) {
            if (!$e instanceof \Illuminate\Validation\ValidationException) { report($e); }
            $message = $e instanceof \Illuminate\Validation\ValidationException ? collect($e->errors())->flatten()->first() : 'ثبت سفارش انجام نشد؛ خطا در گزارش سیستم ثبت شد.';
            try { $this->refreshQuote(); } catch (\Throwable) { /* Keep the invalid-code message; no payment was captured. */ }
            $this->addError('wallet', $message);
        }
    }

    public function checkOrderStatus(): void
    {
        if ($this->pendingOrderId && auth()->check()) {
            $order = StoreOrder::where('user_id', auth()->id())->find($this->pendingOrderId);
            if (!$order) { $this->pendingOrderId = null; }
        }
    }

    public function cancelBankOrder(): void
    {
        $order = StoreOrder::where('user_id',auth()->id())->with('bankPayment')->findOrFail($this->pendingOrderId);
        if ($order->bankPayment) { app(\App\Services\BankPaymentService::class)->cancelRequest($order->bankPayment->id, auth()->id()); }
    }

    public function resetWizard(): void
    {
        $this->reset('step','selectedPlan','selectedPlanPrice','receipt','pendingOrderId','couponCode','discountAmount');
        $this->checkoutKey = (string) Str::uuid();
    }

    public function render()
    {
        $query = Group::where('is_enabled',1);
        if ($this->sellerId) { $query->whereNotIn('id',DB::table('agent_hidden_groups')->where('agent_id',$this->sellerId)->pluck('group_id')); }
        $seller = $this->sellerId ? User::find($this->sellerId) : null;
        $plans = $query->get()->map(function ($group) use ($seller) { $group->final_sell_price = $group->getSellingPriceFor($seller); return $group; });
        if ($this->selectedProtocol !== 'all') {
            $plans = $plans->filter(function ($p) { $wg = str_contains(strtolower($p->name),'wireguard') || str_contains($p->name,'وایرگارد') || str_contains(strtolower($p->name),'wg'); return $this->selectedProtocol === 'wireguard' ? $wg : !$wg; });
        }
        $order = $this->pendingOrderId && auth()->check() ? StoreOrder::where('user_id',auth()->id())->with(['bankPayment','account'])->find($this->pendingOrderId) : null;
        return view('livewire.store.storefront', ['plans'=>$plans, 'pendingOrder'=>$order])->layout('layouts.store')->title($this->storeData['brand_name'] ?? 'فروشگاه');
    }
}
