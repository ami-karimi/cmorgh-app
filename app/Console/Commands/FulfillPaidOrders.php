<?php

namespace App\Console\Commands;

use App\Models\StoreOrder;
use App\Jobs\FulfillStoreOrder;
use Illuminate\Console\Command;

class FulfillPaidOrders extends Command
{
    protected $signature = 'orders:fulfill-paid';
    protected $description = 'Dispatch undelivered paid orders; never blindly retry uncertain VPN operations';
    public function handle(): int
    {
        StoreOrder::where('status','paid')->where('fulfillment_state','waiting')->whereNotNull('paid_at')->whereNull('account_id')
            ->orderBy('id')->limit(50)->pluck('id')->each(fn ($id) => FulfillStoreOrder::dispatch($id));
        // A killed worker leaves a durable processing claim for operator reconciliation.
        StoreOrder::where('fulfillment_state','processing')->where('fulfillment_started_at','<',now()->subMinutes(30))
            ->whereNull('account_id')->update(['fulfillment_error'=>'صدور بیش از ۳۰ دقیقه در حال پردازش است؛ وضعیت سرور و worker را بررسی کنید.']);
        return self::SUCCESS;
    }
}
