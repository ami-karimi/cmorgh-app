<?php

namespace App\Services;

use App\Models\Accounts;
use App\Models\Financial;
use App\Models\StoreOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderFulfillmentService
{
    /** A durable payment must never be reported as failed merely because post-commit delivery failed. */
    public function attemptNow(int $orderId): bool
    {
        try { return $this->fulfill($orderId); }
        catch (\Throwable $e) { report($e); return false; }
    }

    public function retry(int $orderId, User $operator): void
    {
        DB::transaction(function () use ($orderId,$operator) {
            $order = StoreOrder::lockForUpdate()->findOrFail($orderId);
            $allowed = in_array($operator->role,['admin','manager'],true)
                || (in_array($operator->role,['agent','sub_agent'],true) && (int)$order->agent_id === (int)$operator->id);
            abort_unless($allowed,403);
            if (!$order->can_retry_fulfillment) {
                throw \Illuminate\Validation\ValidationException::withMessages(['receipt'=>'صدور مجدد مجاز نیست؛ سفارش تحویل داده شده، در حال پردازش است یا نتیجه سرور نیاز به بررسی دارد.']);
            }
            $order->update(['fulfillment_state'=>'waiting','fulfillment_error'=>null]);
            DB::afterCommit(fn () => $this->attemptNow($order->id));
        },3);
    }

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
        $provisioningStarted = false;
        try {
            // No transaction retry here: provisioning has external side effects.
            DB::transaction(function () use ($orderId, &$provisioningStarted) {
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
                $provisioningStarted = true;
                $account = $service->createFullAccount($data['userData'], $data['configData'], $user->id, false, false);
                if (!$account instanceof Accounts) { throw new \RuntimeException('صدور اکانت نتیجه معتبر برنگرداند.'); }
                $order->update(['account_id'=>$account->id, 'status'=>'approved', 'fulfillment_state'=>'completed', 'fulfillment_error'=>null]);
            });
            return true;
        } catch (\Throwable $e) {
            // Failures before provisioning, or before account/pivot INSERT, cannot have created a VPN peer.
            $safeSqlFailure = $e instanceof \Illuminate\Database\QueryException
                && preg_match('/insert\s+into\s+[`"]?(accounts|user_accounts)[`"]?\s*\(/i', $e->getSql());
            StoreOrder::whereKey($orderId)->whereNull('account_id')->update([
                'fulfillment_state'=>!$provisioningStarted || $safeSqlFailure ? 'failed' : 'requires_review',
                'fulfillment_error'=>$e->getMessage(),
            ]);
            report($e);
            return false;
        }
    }
}
