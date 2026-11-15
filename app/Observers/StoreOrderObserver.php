<?php
namespace App\Observers;
use App\Models\StoreOrder;
use App\Services\NotificationService;
class StoreOrderObserver
{
    public function created(StoreOrder $order): void { $this->notify($order, 'سفارش جدید ثبت شد', 'سفارش جدیدی ثبت شده و نیاز به بررسی دارد.', 'order.created'); }
    public function updated(StoreOrder $order): void
    {
        if ($order->wasChanged('status') || $order->wasChanged('fulfillment_state') || $order->wasChanged('account_id')) {
            $title = $order->status === 'paid' || $order->status === 'approved' ? 'سفارش تأیید شد' : ($order->status === 'rejected' ? 'سفارش رد شد' : 'وضعیت سفارش تغییر کرد');
            $message = $order->account_id ? 'سفارش شما تأیید شد و اکانت با موفقیت صادر شد.' : "وضعیت سفارش به «{$order->status_label}» تغییر کرد.";
            $this->notify($order, $title, $message, 'order.updated');
        }
    }
    private function notify(StoreOrder $order, string $adminTitle, string $message, string $type): void
    {
        NotificationService::admins($adminTitle, "شماره سفارش #{$order->id}: {$message}", $type, route('admin.store.orders'), ['order_id' => $order->id]);
        NotificationService::send($order->user, $adminTitle, $message, $type, route('customer.orders'), ['order_id' => $order->id]);
        NotificationService::send($order->agent, $adminTitle, $message, $type, route('reseller.store.orders'), ['order_id' => $order->id]);
    }
}
