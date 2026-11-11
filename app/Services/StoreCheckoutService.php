<?php

namespace App\Services;

use App\Models\Financial;
use App\Models\Group;
use App\Models\StoreOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreCheckoutService
{
    public function quote(User $user, int $groupId, ?int $sellerId, ?string $code = null): array
    {
        $group = Group::where('is_enabled', 1)->findOrFail($groupId);
        if ($sellerId && DB::table('agent_hidden_groups')->where('agent_id', $sellerId)->where('group_id', $groupId)->exists()) {
            $this->fail('این پلن در فروشگاه انتخاب‌شده قابل خرید نیست.');
        }
        $base = (int) round($group->getBaseSellingPriceFor($sellerId ? User::findOrFail($sellerId) : null));
        $quote = app(PromotionService::class)->quote($base, $user, $groupId, 'plan_discount', $code ?: null);
        if ($sellerId) {
            $seller = User::findOrFail($sellerId);
            if (in_array($seller->role,['agent','sub_agent'],true)) {
                $floor = (int) round($group->getFinalPriceFor($seller));
                $quote['total'] = max($quote['total'], $floor);
                $quote['discount'] = max(0, $base-$quote['total']);
                if (!$quote['discount']) {
                    if ($code) { $this->fail('این کد در حاشیه سود این فروشگاه قابل اعمال نیست.'); }
                    $quote['promotion_id'] = null;
                }
            }
        }
        return $quote;
    }

    public function create(User $user, int $groupId, ?int $sellerId, string $method, string $key, ?string $code = null, ?string $receipt = null, ?string $expectedAccount = null, ?int $expectedPrice = null): StoreOrder
    {
        if (!in_array($method, ['wallet', 'bank', 'receipt'], true)) { $this->fail('روش پرداخت معتبر نیست.'); }
        $order = DB::transaction(function () use ($user, $groupId, $sellerId, $method, $key, $code, $receipt, $expectedAccount, $expectedPrice) {
            $user = User::lockForUpdate()->findOrFail($user->id);
            $old = StoreOrder::where('checkout_key', $key)->first();
            if ($old) {
                if ((int) $old->user_id !== (int) $user->id) { $this->fail('سفارش متعلق به این حساب نیست.'); }
                return $old;
            }
            $quote = $this->quote($user, $groupId, $sellerId, $code);
            if ($expectedPrice !== null && $expectedPrice !== $quote['total']) {
                $this->fail('قیمت یا تخفیف تغییر کرده است؛ مبلغ جدید را بررسی و دوباره تأیید کنید.');
            }
            if ($method === 'bank' && (!$expectedAccount || $quote['total'] < 1000)) {
                $this->fail('پرداخت خودکار به حساب بانکی پیکربندی‌شده و حداقل ۱٬۰۰۰ تومان نیاز دارد؛ برای سفارش رایگان از کیف پول استفاده کنید.');
            }
            if ($method === 'receipt' && !$receipt) { $this->fail('فیش واریزی الزامی است.'); }
            $order = StoreOrder::create([
                'user_id'=>$user->id, 'agent_id'=>$sellerId, 'group_id'=>$groupId,
                'phone'=>$user->phone, 'email'=>$user->email, 'price'=>$quote['total'],
                'original_price'=>$quote['original'], 'discount_amount'=>$quote['discount'], 'promotion_id'=>$quote['promotion_id'],
                'agent_costs'=>$this->snapshotAgentCosts($sellerId, Group::findOrFail($groupId)),
                'receipt_image'=>$receipt ?: $method.'_payment', 'payment_method'=>$method, 'checkout_key'=>$key,
                'status'=>'pending', 'fulfillment_state'=>'waiting',
            ]);
            // Hold capacity until payment or explicit cancellation; delayed SMS processing must not oversubscribe coupon limits.
            app(PromotionService::class)->reserve($quote, $user, $groupId, 'order', $order->id, null);
            if ($method === 'wallet') {
                $this->capture($order->id);
            } elseif ($method === 'bank') {
                app(BankPaymentService::class)->createRequest($user->id, (int) $order->price, $order->id, $expectedAccount);
            }
            return $order;
        }, 3);
        return $order->fresh();
    }

    /** Bank caller already credited the genuine deposit; wallet caller supplies existing credit. */
    public function capture(int $orderId): void
    {
        $order = StoreOrder::lockForUpdate()->findOrFail($orderId);
        if ($order->paid_at) { return; }
        if ($order->status !== 'pending') { $this->fail('این سفارش قابل پرداخت نیست.'); }
        $user = User::lockForUpdate()->findOrFail($order->user_id);
        if (Financial::lockedBalance($user->id) < (int) $order->price) { $this->fail('موجودی کیف پول برای مبلغ نهایی سفارش کافی نیست.'); }
        Financial::create([
            'creator'=>$user->id, 'for'=>$user->id, 'type'=>'minus', 'approved'=>1, 'price'=>(int) $order->price,
            'description'=>'رزرو هزینه سفارش #'.$order->id, 'store_order_id'=>$order->id, 'entry_kind'=>'order_payment',
        ]);
        $order->update(['paid_at'=>now(), 'status'=>'paid']);
        app(PromotionService::class)->redeem('order', $order->id);
        // Issue immediately after the durable payment commit, regardless of QUEUE_CONNECTION/worker availability.
        // Never perform external provisioning inside a payment transaction that may be retried.
        DB::afterCommit(fn () => app(OrderFulfillmentService::class)->attemptNow($order->id));
    }

    public function approveReceipt(int $orderId, User $operator): void
    {
        DB::transaction(function () use ($orderId, $operator) {
            $order = StoreOrder::lockForUpdate()->findOrFail($orderId);
            $this->authorizeOperator($order, $operator);
            if ($order->payment_method !== 'receipt' || $order->paid_at || $order->status !== 'pending') { $this->fail('فقط فیش پرداخت‌نشده قابل تأیید دستی است.'); }
            User::lockForUpdate()->findOrFail($order->user_id);
            Financial::create([
                'creator'=>$operator->id, 'for'=>$order->user_id, 'price'=>(int) $order->price, 'type'=>'plus', 'approved'=>1,
                'description'=>'تأیید فیش سفارش #'.$order->id, 'store_order_id'=>$order->id, 'entry_kind'=>'receipt_credit',
            ]);
            $this->capture($order->id);
        }, 3);
    }

    public function rejectReceipt(int $orderId, User $operator): void
    {
        DB::transaction(function () use ($orderId, $operator) {
            $order = StoreOrder::lockForUpdate()->findOrFail($orderId);
            $this->authorizeOperator($order, $operator);
            if ($order->payment_method !== 'receipt' || $order->paid_at || $order->status !== 'pending') { $this->fail('سفارش پرداخت‌شده یا بانکی قابل رد با این عملیات نیست.'); }
            $order->update(['status'=>'rejected']);
            app(PromotionService::class)->release('order', $order->id);
        }, 3);
    }

    public function refund(int $orderId, User $operator): void
    {
        DB::transaction(function () use ($orderId, $operator) {
            $order = StoreOrder::lockForUpdate()->findOrFail($orderId);
            $this->authorizeOperator($order, $operator);
            if (!$order->paid_at || $order->account_id || $order->status === 'refunded' || $order->fulfillment_state === 'processing') {
                $this->fail('این سفارش قابل بازگشت اعتبار نیست؛ ابتدا وضعیت صدور را بررسی کنید.');
            }
            User::lockForUpdate()->findOrFail($order->user_id);
            Financial::create([
                'creator'=>$operator->id, 'for'=>$order->user_id, 'price'=>(int) $order->price, 'type'=>'plus', 'approved'=>1,
                'description'=>'بازگشت هزینه سفارش #'.$order->id, 'store_order_id'=>$order->id, 'entry_kind'=>'order_refund',
            ]);
            $order->update(['status'=>'refunded', 'fulfillment_state'=>'refunded']);
        }, 3);
    }

    private function snapshotAgentCosts(?int $sellerId, Group $group): array
    {
        $costs = [];
        while ($sellerId) {
            if (isset($costs[$sellerId])) { $this->fail('چرخه در ساختار نمایندگی وجود دارد.'); }
            $seller = User::findOrFail($sellerId);
            if (!in_array($seller->role,['agent','sub_agent'],true)) { break; }
            $costs[$sellerId] = (int) round($group->getFinalPriceFor($seller));
            $sellerId = $seller->creator;
        }
        return $costs;
    }

    private function authorizeOperator(StoreOrder $order, User $operator): void
    {
        if (!in_array($operator->role, ['admin','manager'], true) && !((int) $order->agent_id === (int) $operator->id && in_array($operator->role, ['agent','sub_agent'], true))) {
            $this->fail('دسترسی به سفارش مجاز نیست.');
        }
    }

    private function fail(string $message): never { throw ValidationException::withMessages(['checkout'=>$message]); }
}
