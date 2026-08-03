<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StoreOrder;
use Illuminate\Support\Facades\Auth;

class MyOrders extends Component
{
    use WithPagination;

    // متغیرهای مربوط به مودال نمایش فیش واریزی
    public $isReceiptModalOpen = false;
    public $selectedReceipt = null;

    // باز کردن مودال نمایش فیش
    public function viewReceipt($imagePath)
    {
        $this->selectedReceipt = $imagePath;
        $this->isReceiptModalOpen = true;
    }

    public function render()
    {
        // واکشی سفارشات فقط برای مشتری لاگین‌شده فعلی
        $orders = StoreOrder::where('user_id', Auth::id())
            ->with('group')
            ->latest()
            ->paginate(10);

        return view('livewire.customer.my-orders', [
            'orders' => $orders
        ])->layout('layouts.customer')->title('سفارشات من');
    }
}
