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

    public function approveOrder($orderId)
    {
        try {
            DB::transaction(function () use ($orderId) {$order = StoreOrder::with(['user', 'group'])
                ->where('agent_id', Auth::id())
                ->findOrFail($orderId);

                if ($order->status === 'approved') {
                    throw new \Exception('این سفارش قبلاً تایید شده است.');
                }

                $user =$order->user;
                $group =$order->group;

                if (!$user || !$group) {
                    throw new \Exception('اطلاعات کاربر یا بسته درخواستی یافت نشد!');
                }


                // آماده‌سازی و ساخت اکانت
                $accService = new AccountProvisioningService();
                $preparedData = $accService->prepareAccountData($group, $user,$order->phone);

                $createdAccount = $accService->createFullAccount($preparedData['userData'],
                    $preparedData['configData'],$user->id,
                    true,
                    false
                );

                if (is_array($createdAccount) && isset($createdAccount['status']) &&$createdAccount['status'] === false) {
                    throw new \Exception($createdAccount['message'] ?? 'خطایی در صدور اکانت رخ داد.');
                }

                // تایید نهایی سفارش
                $order->update([
                    'status'     => 'approved',
                    'account_id' => $createdAccount->id ?? $createdAccount['id'] ?? null,
                ]);
            });

            session()->flash('success', 'فیش تایید و اکانت با موفقیت صادر شد.');
            $this->isReceiptModalOpen = false;

        } catch (\Exception $e) {
            session()->flash('error', 'خطا در تایید سفارش: ' . $e->getMessage());
        }
    }

    public function rejectOrder($orderId)
    {
        try {
            $order = StoreOrder::where('agent_id', Auth::id())->findOrFail($orderId);$order->update(['status' => 'rejected']);

            session()->flash('success', 'سفارش با موفقیت رد شد.');
            $this->isReceiptModalOpen = false;
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در رد سفارش: ' . $e->getMessage());
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
                $q->where('phone', 'like', '\%' .$this->search . '%')
                    ->orWhere('id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($u) {
                        $u->where('name', 'like', '\%' .$this->search . '%')
                            ->orWhere('username', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $orders =$query->paginate(15);
        $selectedOrder =$this->selectedOrderId
            ? StoreOrder::with(['user', 'group', 'account'])->find($this->selectedOrderId)
            : null;

        return view('livewire.agent.agent-orders-manager', [
            'orders'        => $orders,
            'selectedOrder' => $selectedOrder,
            'stats'         => $stats,
        ]);
    }
}
