<?php
namespace App\Livewire\Agent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StoreOrder;
use App\Models\Financial;
use App\Services\AccountProvisioningService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
#[Title('سفارشات | پنل نمایندگی')]
#[Layout('layouts.agent')]
class AgentOrdersManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public $isReceiptModalOpen = false;
    public $selectedOrderId = null;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }

    public function viewReceipt($orderId)
    {
        $order = StoreOrder::where('agent_id', Auth::id())->find($orderId);

        if ($order) {
            $this->selectedOrderId =$orderId;
            $this->isReceiptModalOpen = true;
        } else {
            session()->flash('error', 'سفارش یافت نشد یا شما دسترسی ندارید.');
        }
    }

    public function boot(): void
    {
        abort_unless(auth()->check() && in_array(auth()->user()->role, ['agent','sub_agent'], true), 403);
    }

    private function paymentAction(string $method, $orderId, string $success): void
    {
        $this->resetValidation('receipt');
        try {
            app(\App\Services\StoreCheckoutService::class)->$method((int) $orderId, auth()->user());
            if ($method === 'approveReceipt') {
                $issued = StoreOrder::findOrFail($orderId);
                if (!$issued->account_id || $issued->status !== 'approved') {
                    $message = 'پرداخت تأیید شد، اما صدور کامل نشد؛ علت را در جزئیات سفارش بررسی کنید. دوباره پرداخت نکنید.';
                    $this->addError('receipt', $message);
                    session()->flash('error', $message);
                    return;
                }
                $success = 'فیش تأیید و اکانت بلافاصله صادر شد.';
            }
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

    public function retryFulfillment($orderId): void
    {
        $this->resetValidation('receipt');
        try {
            app(\App\Services\OrderFulfillmentService::class)->retry((int)$orderId, auth()->user());
            $order = \App\Models\StoreOrder::findOrFail($orderId);
            if ($order->account_id && $order->status === 'approved') {
                session()->flash('success', 'اکانت همین سفارش با موفقیت صادر شد؛ هزینه دوباره کسر نشد.');
            } else {
                $this->addError('receipt', 'صدور تکمیل نشد؛ علت خطا در جزئیات سفارش نمایش داده شده است.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError('receipt', collect($e->errors())->flatten()->first());
        }
    }

    public function render()
    {
        $agentId = Auth::id();

        // 📊 محاسبه آمار برای ویجت‌ها
        $stats = [
            'walletBalance' => Auth::user()->balance ?? 0,

            'totalSales' => StoreOrder::where('agent_id', $agentId)
                ->where('status', 'approved')
                ->sum('price'),

            'todaySales' => StoreOrder::where('agent_id', $agentId)
                ->where('status', 'approved')
                ->whereDate('created_at', Carbon::today())
                ->sum('price'),

            'pendingOrders' => StoreOrder::where('agent_id', $agentId)
                ->where('status', 'pending')
                ->count(),
        ];

        // 🔒 کوئری امن: فقط سفارشات این نماینده
        $query = StoreOrder::with(['user', 'group', 'account'])
            ->where('agent_id', $agentId)
            ->latest();

        if ($this->statusFilter) {
            $query->where('status',$this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('phone', 'like', '%' .$this->search . '%')
                    ->orWhere('id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($u) {
                        $u->where('name', 'like', '%' .$this->search . '%')
                            ->orWhere('username', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $orders =$query->paginate(15);
        $selectedOrder =$this->selectedOrderId
            ? StoreOrder::with(['user', 'group', 'account'])->where('agent_id', auth()->id())->find($this->selectedOrderId)
            : null;

        return view('livewire.agent.agent-orders-manager', [
            'orders'        => $orders,
            'selectedOrder' => $selectedOrder,
            'stats'         => $stats,
        ]);
    }
}
