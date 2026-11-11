<?php

namespace App\Console\Commands;

use App\Models\StoreOrder;
use App\Services\OrderFulfillmentService;
use Illuminate\Console\Command;

class FulfillPaidOrders extends Command
{
    protected $signature = 'orders:fulfill-paid {--order= : Issue one already-paid waiting order only}';
    protected $description = 'Issue committed paid orders directly; no queue worker required';

    public function handle(OrderFulfillmentService $service): int
    {
        $query = StoreOrder::where('status','paid')->where('fulfillment_state','waiting')->whereNotNull('paid_at')->whereNull('account_id');
        $targetId = $this->option('order');
        if ($targetId !== null) {
            if (!ctype_digit((string)$targetId) || (int)$targetId < 1 || !$query->whereKey($targetId)->exists()) {
                $this->error('Order must be paid, waiting and not yet delivered; review failed/uncertain orders in the admin panel.');
                return self::FAILURE;
            }
        }
        $ids = $query->orderBy('id')->limit(50)->pluck('id');
        $issued = 0;
        foreach ($ids as $id) { $issued += (int)$service->attemptNow($id); }
        $this->info("Checked: {$ids->count()}; issued: {$issued}");
        StoreOrder::where('fulfillment_state','processing')->where('fulfillment_started_at','<',now()->subMinutes(30))
            ->whereNull('account_id')->update(['fulfillment_error'=>'صدور بیش از ۳۰ دقیقه در حال پردازش است؛ وضعیت سرور و worker را بررسی کنید.']);
        return $targetId !== null && !$issued ? self::FAILURE : self::SUCCESS;
    }
}
