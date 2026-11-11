<?php

namespace App\Livewire\Admin;

use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StoreOrder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Financial;

#[Title('مدیریت سفارشات فروشگاه | همراه سیمرغ')]
#[Layout('layouts.admin')]
class StoreOrdersManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = ''; // pending, approved, rejected

    // متغیرهای مودال نمایش فیش
    public $isReceiptModalOpen = false;
    public $selectedOrder = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    // باز کردن مودال فیش واریزی
    public function viewReceipt($orderId)
    {
        $this->selectedOrder = StoreOrder::with(['user', 'agent', 'group'])->findOrFail($orderId);
        $this->isReceiptModalOpen = true;
    }


    public function boot(): void
    {
        abort_unless(auth()->check() && in_array(auth()->user()->role, ['admin','manager'], true), 403);
    }

    private function paymentAction(string $method, $orderId, string $success): void
    {
        try {
            app(\App\Services\StoreCheckoutService::class)->$method((int) $orderId, auth()->user());
            session()->flash('success', $success);
            $this->isReceiptModalOpen = false;
        } catch (\Throwable $e) {
            if (!$e instanceof \Illuminate\Validation\ValidationException) { report($e); }
            $message = $e instanceof \Illuminate\Validation\ValidationException ? collect($e->errors())->flatten()->first() : 'عملیات انجام نشد؛ گزارش خطا ثبت شد.';
            $this->addError('receipt', $message);
            session()->flash('error', $message);
        }
    }

    public function approveOrder($orderId): void { $this->paymentAction('approveReceipt', $orderId, 'فیش تأیید شد؛ سفارش برای صدور ثبت شد.'); }
    public function rejectOrder($orderId): void { $this->paymentAction('rejectReceipt', $orderId, 'سفارش رد شد.'); }
    public function refundOrder($orderId): void { $this->paymentAction('refund', $orderId, 'هزینه سفارش به کیف پول مشتری برگشت داده شد.'); }

    public function render()
    {
        $orders = StoreOrder::with(['user', 'agent', 'group'])
            ->when($this->search, function($q) {
                $q->where('phone', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function($userQ) {
                        $userQ->where('name', 'like', '%'.$this->search.'%');
                    });
            })
            ->when($this->statusFilter, function($q) {
                $q->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.store-orders-manager', [
            'orders' => $orders
        ])->layout('layouts.admin')->title('مدیریت سفارشات فروشگاه');
    }
}
