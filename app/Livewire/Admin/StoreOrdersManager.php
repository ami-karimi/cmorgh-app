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


    public function approveOrder($orderId)
    {
        try {
            DB::transaction(function () use ($orderId) {

               $order = StoreOrder::with(['user', 'group'])->findOrFail($orderId);

                if ($order->status === 'approved') {
                    throw new \Exception('این سفارش قبلاً تایید و پردازش شده است.');
                }

                $user = $order->user;
                $group = $order->group;

                if ($order->agent_id && $user->creator != $order->agent_id) {
                    $user->creator = $order->agent_id;
                    $user->save();
                }

                $accService = new \App\Services\AccountProvisioningService();

                $preparedData = $accService->prepareAccountData($group, $user, $order->phone);

                $create = $accService->createFullAccount(
                    $preparedData['userData'],
                    $preparedData['configData'],
                    $user->id,
                    true,
                    false
                );
                if (is_array($create) && isset($create['status']) &&$create['status'] === false) {
                    throw new \Exception($create['message'] ?? 'خطایی در صدور اکانت رخ داد.');
                }


                $order->update(['status' => 'approved','account_id' => $create->id]);

            });

            session()->flash('success', 'فیش واریزی تایید و اکانت با موفقیت صادر شد.');
            $this->isReceiptModalOpen = false;

        } catch (\Exception $e) {
            $this->addError('receipt', 'خطا در پردازش سفارش: ' . $e->getMessage());
        }
    }

    // رد کردن سفارش
    public function rejectOrder($orderId)
    {
        $order = StoreOrder::findOrFail($orderId);
        $order->update(['status' => 'rejected']);

        session()->flash('error', 'سفارش رد شد.');
        $this->isReceiptModalOpen = false;
    }

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
