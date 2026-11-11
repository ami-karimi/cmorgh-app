<?php

namespace App\Services;

use App\Models\Accounts;
use App\Models\Financial;
use App\Models\StoreOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderFulfillmentService
{
    public function fulfill(int $orderId): bool
    {
        $claimed = DB::transaction(function () use ($orderId) {
            $order = StoreOrder::lockForUpdate()->findOrFail($orderId);
            if (!$order->paid_at || $order->account_id || $order->status !== 'paid' || $order->fulfillment_state !== 'waiting') { return false; }
            // Persist the claim BEFORE any external VPN operation; a crash must not blindly create another peer.
            $order->update(['fulfillment_state'=>'processing', 'fulfillment_started_at'=>now()]);
            return true;
        }, 3);
        if (!$claimed) { return false; }
        try {
            // No transaction retry here: provisioning has external side effects.
            DB::transaction(function () use ($orderId) {
                $order = StoreOrder::with(['group','user'])->lockForUpdate()->findOrFail($orderId);
                $group = $order->group; $user = $order->user;
                if (!$group || !$user || !$group->is_enabled) { throw new \RuntimeException('پلن یا کاربر سفارش در دسترس نیست.'); }
                $chain = []; $agentId = $order->agent_id;
                while ($agentId) {
                    if (isset($chain[$agentId])) { throw new \RuntimeException('چرخه در ساختار نمایندگان وجود دارد.'); }
                    $agent = User::findOrFail($agentId);
                    if (in_array($agent->role, ['agent','sub_agent'], true)) { $chain[$agentId] = $agent; }
                    else { break; }
                    $agentId = $agent->creator;
                }
                $snapshot = $order->agent_costs;
                if (is_array($snapshot)) {
                    $chain = [];
                    foreach ($snapshot as $agentId => $cost) { $chain[$agentId] = User::findOrFail($agentId); }
                }
                $ids = array_keys($chain); sort($ids);
                if ($ids) { User::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get(); }
                foreach ($chain as $agent) {
                    $cost = is_array($snapshot) ? (int) $snapshot[$agent->id] : (int) $group->getFinalPriceFor($agent);
                    if (Financial::lockedBalance($agent->id) < $cost) { throw new \RuntimeException('موجودی نماینده برای صدور سفارش کافی نیست.'); }
                    Financial::create([
                        'creator'=>$user->id, 'for'=>$agent->id, 'type'=>'minus', 'approved'=>1, 'price'=>$cost,
                        'description'=>'هزینه عمده سفارش #'.$order->id, 'store_order_id'=>$order->id, 'entry_kind'=>'agent_cost',
                    ]);
                }
                $service = app(AccountProvisioningService::class);
                $data = $service->prepareAccountData($group, $user, $order->phone);
                // Customer paid the exact order price already; do NOT call the old cascade debit again.
                $account = $service->createFullAccount($data['userData'], $data['configData'], $user->id, false, false);
                if (!$account instanceof Accounts) { throw new \RuntimeException('صدور اکانت نتیجه معتبر برنگرداند.'); }
                $order->update(['account_id'=>$account->id, 'status'=>'approved', 'fulfillment_state'=>'completed', 'fulfillment_error'=>null]);
            });
            return true;
        } catch (\Throwable $e) {
            StoreOrder::whereKey($orderId)->whereNull('account_id')->update([
                'fulfillment_state'=>'requires_review', 'fulfillment_error'=>$e->getMessage(),
            ]);
            report($e);
            return false;
        }
    }
}
