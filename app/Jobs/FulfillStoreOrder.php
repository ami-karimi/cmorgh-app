<?php

namespace App\Jobs;

use App\Services\OrderFulfillmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FulfillStoreOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 1;
    public function __construct(public int $orderId) {}
    public function handle(OrderFulfillmentService $service): void { $service->fulfill($this->orderId); }
}
