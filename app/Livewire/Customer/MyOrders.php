<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Locked;
use App\Models\StoreOrder;
use App\Models\AgentBankAccount;

class MyOrders extends Component
{
    use WithPagination;
    public $isReceiptModalOpen = false, $selectedReceipt = null;
    #[Locked] public $pendingOrderId = null;

    public function boot(): void { abort_unless(auth()->check() && auth()->user()->role === 'customer',403); }
    public function viewReceipt($orderId): void
    {
        $order = StoreOrder::where('user_id',auth()->id())->findOrFail($orderId);
        if ($order->payment_method !== 'receipt') { return; }
        $this->selectedReceipt = $order->receipt_image; $this->isReceiptModalOpen = true;
    }
    public function showPayment($orderId): void
    {
        StoreOrder::where('user_id',auth()->id())->findOrFail($orderId);
        $this->pendingOrderId = $orderId;
    }
    public function checkOrderStatus(): void
    {
        if ($this->pendingOrderId && !StoreOrder::where('user_id',auth()->id())->whereKey($this->pendingOrderId)->exists()) { $this->pendingOrderId = null; }
    }
    public function cancelBankOrder(): void
    {
        $order = StoreOrder::where('user_id',auth()->id())->with('bankPayment')->findOrFail($this->pendingOrderId);
        if ($order->bankPayment) { app(\App\Services\BankPaymentService::class)->cancelRequest($order->bankPayment->id,auth()->id()); }
    }
    public function resetWizard(): void { $this->pendingOrderId = null; }
    public function render()
    {
        $order = $this->pendingOrderId ? StoreOrder::where('user_id',auth()->id())->with(['bankPayment','account'])->find($this->pendingOrderId) : null;
        $bank = $order?->bankPayment?->expected_account_number ? AgentBankAccount::where('account_number',$order->bankPayment->expected_account_number)->first() : null;
        return view('livewire.customer.my-orders', [
            'orders'=>StoreOrder::where('user_id',auth()->id())->with(['group','account','bankPayment'])->latest()->paginate(10),
            'pendingOrder'=>$order, 'bankDetails'=>$bank,
        ])->layout('layouts.customer')->title('سفارشات من');
    }
}
