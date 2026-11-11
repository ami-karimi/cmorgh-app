<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\BankMessage;
use App\Models\Financial;
use App\Models\TopupRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BankPaymentService
{
    public function createRequest(int $userId, int $amount, ?int $orderId = null, ?string $expectedAccount = null): TopupRequest
    {
        if ($amount < 1000 || $amount > 100000000) {
            $this->fail('مبلغ شارژ باید بین ۱٬۰۰۰ تا ۱۰۰٬۰۰۰٬۰۰۰ تومان باشد.');
        }
        return DB::transaction(function () use ($userId, $amount, $orderId, $expectedAccount) {
            $user = \App\Models\User::lockForUpdate()->findOrFail($userId);
            if ($orderId && !\App\Models\StoreOrder::whereKey($orderId)->where('user_id',$userId)->where('payment_method','bank')->where('status','pending')
                ->whereNull('paid_at')->where('price',$amount)->exists()) {
                $this->fail('درخواست پرداخت به سفارش معتبر متعلق به این کاربر متصل نیست.');
            }
            $expired = TopupRequest::where('user_id',$userId)->where('status','pending')->where('expires_at','<=',now())->lockForUpdate()->get();
            foreach ($expired as $old) {
                $old->update(['status'=>'expired']);
                app(PromotionService::class)->release('topup',$old->id);
                if ($old->store_order_id) {
                    \App\Models\StoreOrder::whereKey($old->store_order_id)->whereNull('paid_at')->update(['status'=>'cancelled']);
                    app(PromotionService::class)->release('order',$old->store_order_id);
                }
            }
            if (TopupRequest::where('user_id', $userId)->whereIn('status', ['pending', 'paid'])->exists()) {
                $this->fail('شما یک درخواست فعال دارید؛ ابتدا آن را تکمیل یا لغو کنید.');
            }
            $used = TopupRequest::whereIn('status', ['pending', 'paid'])
                ->whereBetween('payable_amount', [($amount + 1) * 10, ($amount + 999) * 10])->pluck('payable_amount')->all();
            $offsets = range(1, 999);
            shuffle($offsets);
            foreach ($offsets as $offset) {
                $payable = ($amount + $offset) * 10;
                if (!in_array($payable, $used)) {
                    $quote = $orderId ? ['original'=>$amount, 'discount'=>0, 'promotion_id'=>null] : app(PromotionService::class)->quote($amount, $user, null, 'topup_bonus');
                    $extra = [];
                    if (\Illuminate\Support\Facades\Schema::hasColumn('topup_requests', 'purpose')) {
                        $extra = ['purpose'=>$orderId ? 'order' : 'wallet', 'store_order_id'=>$orderId,
                            'expected_account_number'=>$expectedAccount, 'bonus_amount'=>$quote['discount'], 'bonus_promotion_id'=>$quote['promotion_id']];
                    }
                    $request = TopupRequest::create(array_merge($extra, [
                        'user_id' => $userId, 'requested_amount' => $amount,
                        'unique_amount' => $offset * 10, 'payable_amount' => $payable,
                        'status' => 'pending', 'expires_at' => now()->addMinutes(20),
                    ]));
                    app(PromotionService::class)->reserve($quote, $user, null, 'topup', $request->id, null);
                    return $request;
                }
            }
            $this->fail('برای این مبلغ ظرفیت درخواست فعال پر است؛ مبلغ دیگری انتخاب کنید.');
        }, 3);
    }

    public function cancelRequest(int $requestId, int $userId): bool
    {
        return DB::transaction(function () use ($requestId, $userId) {
            $request = TopupRequest::where('user_id', $userId)->lockForUpdate()->findOrFail($requestId);
            if ($request->status !== 'pending') { return false; }
            $request->update(['status' => 'expired']);
            app(PromotionService::class)->release('topup', $request->id);
            if ($request->store_order_id) {
                \App\Models\StoreOrder::whereKey($request->store_order_id)->whereNull('paid_at')->update(['status'=>'cancelled']);
                app(PromotionService::class)->release('order', $request->store_order_id);
            }
            return true;
        }, 3);
    }

    public function approve(int $requestId): TopupRequest
    {
        return DB::transaction(function () use ($requestId) {
            $request = TopupRequest::lockForUpdate()->findOrFail($requestId);
            $this->ensurePayable($request);
            return $this->credit($request, 'approve_top_up');
        }, 3);
    }

    public function reject(int $requestId, string $reason): void
    {
        DB::transaction(function () use ($requestId, $reason) {
            $request = TopupRequest::lockForUpdate()->findOrFail($requestId);
            $this->ensurePayable($request);
            $old = $request->status;
            $request->update(['status' => 'rejected', 'rejection_reason' => $reason]);
            app(PromotionService::class)->release('topup', $request->id);
            if ($request->store_order_id) {
                \App\Models\StoreOrder::whereKey($request->store_order_id)->whereNull('paid_at')->update(['status'=>'rejected']);
                app(PromotionService::class)->release('order', $request->store_order_id);
            }
            $this->audit($request, 'reject_top_up', ['status' => $old], ['status' => 'rejected', 'reason' => $reason]);
        }, 3);
    }

    /** A checked message is not necessarily consumed: the relation is the source of truth. */
    public function match(int $requestId, int $messageId, bool $automatic = false): TopupRequest
    {
        return DB::transaction(function () use ($requestId, $messageId, $automatic) {
            // Consistent lock order across manual, automatic and retry paths.
            $request = TopupRequest::lockForUpdate()->findOrFail($requestId);
            $message = BankMessage::lockForUpdate()->findOrFail($messageId);
            $this->ensurePayable($request);
            if ($request->matched_bank_message_id || $message->matchedRequest()->exists()) {
                $this->fail('این درخواست یا پیام بانکی قبلاً تطبیق داده شده است.');
            }
            if ((int) $request->payable_amount !== (int) $message->deposit_amount) {
                $this->fail('مبلغ ریالی درخواست با پیام بانکی مطابقت ندارد.');
            }
            if (!$this->withinPaymentWindow($request, $message)) {
                $this->fail('زمان تراکنش بانکی خارج از مهلت پرداخت این درخواست است.');
            }
            if ($automatic && $request->status !== 'pending') {
                $this->fail('درخواست برای تطبیق خودکار قابل پردازش نیست.');
            }
            $request->update([
                'matched_bank_message_id' => $message->id,
                'matched_at' => now(),
                'match_type' => $automatic ? 'automatic' : 'manual',
            ]);
            $message->update(['processed' => true, 'processed_at' => now(), 'processing_error' => null]);
            return $this->credit($request, $automatic ? 'automatic_match' : 'manual_match');
        }, 3);
    }

    public function withinPaymentWindow(TopupRequest $request, BankMessage $message): bool
    {
        $time = $message->transaction_datetime;
        // SMS timestamps have minute precision; allow the request's first minute, not older messages.
        if ($request->expected_account_number && $request->expected_account_number !== $message->account_number) { return false; }
        return $time && $request->created_at
            && $time->gte($request->created_at->copy()->startOfMinute())
            && (!$request->expires_at || $time->lte($request->expires_at))
            && $time->lte(now()->addMinute());
    }

    public function processMessage(int $messageId): bool
    {
        $message = BankMessage::findOrFail($messageId);
        if ($message->matchedRequest()->exists()) {
            return false;
        }
        $candidates = TopupRequest::where('payable_amount', $message->deposit_amount)
            ->where('status', 'pending')->whereNull('matched_bank_message_id')->get()
            ->filter(fn ($request) => $this->withinPaymentWindow($request, $message));
        if ($candidates->count() !== 1) {
            BankMessage::whereKey($message->id)->whereDoesntHave('matchedRequest')->update([
                'processed' => true, 'processed_at' => now(),
                'processing_error' => $candidates->count() > 1
                    ? 'چند درخواست با مبلغ و زمان یکسان یافت شد؛ بررسی دستی لازم است.'
                    : 'درخواست سازگار با مبلغ و زمان تراکنش یافت نشد.',
            ]);
            return false;
        }
        try {
            $this->match($candidates->first()->id, $message->id, true);
            return true;
        } catch (\Throwable $e) {
            // A concurrent successful worker must not have its success overwritten.
            if (!$message->matchedRequest()->exists()) {
                BankMessage::whereKey($message->id)->whereDoesntHave('matchedRequest')->update(['processed' => true, 'processed_at' => now(), 'processing_error' => $e->getMessage()]);
            }
            report($e);
            return false;
        }
    }

    public function processPending(int $limit = 50): array
    {
        $ids = BankMessage::where('processed', false)->orderBy('id')->limit($limit)->pluck('id');
        $matched = 0;
        foreach ($ids as $id) {
            try {
                $matched += (int) $this->processMessage($id);
            } catch (\Throwable $e) {
                report($e);
            }
        }
        $result = ['processed' => $ids->count(), 'matched' => $matched];
        Log::info('Bank payment processing completed', $result);
        return $result;
    }

    private function ensurePayable(TopupRequest $request): void
    {
        if (!in_array($request->status, ['pending', 'paid'], true)) {
            $this->fail('این درخواست قبلاً نهایی شده یا قابل تأیید نیست.');
        }
        if (Financial::where('top_up_request_id', $request->id)->exists()) {
            $this->fail('این درخواست قبلاً به کیف پول اضافه شده است.');
        }
    }

    private function credit(TopupRequest $request, string $action): TopupRequest
    {
        $payingUser = \App\Models\User::lockForUpdate()->find($request->user_id);
        if (!$payingUser || $request->requested_amount <= 0) {
            $this->fail('کاربر یا مبلغ درخواست معتبر نیست.');
        }
        $old = $request->status;
        $financial = Financial::create([
            'creator' => auth()->id() ?? $request->user_id,
            'for' => $request->user_id,
            // Wallet ledger is in toman; payable_amount and bank SMS are in rial.
            'price' => $request->requested_amount,
            'type' => 'plus', 'approved' => 1,
            'description' => 'شارژ کیف پول - درخواست #'.$request->id,
            'top_up_request_id' => $request->id,
        ]);
        if ($request->purpose !== 'order' && (int) $request->bonus_amount > 0) {
            Financial::create([
                'creator'=>auth()->id() ?? $request->user_id, 'for'=>$request->user_id,
                'price'=>(int) $request->bonus_amount, 'type'=>'plus', 'approved'=>1,
                'description'=>'بونوس شارژ #'.$request->id, 'bonus_topup_id'=>$request->id,
            ]);
        }
        app(PromotionService::class)->redeem('topup', $request->id);
        if ($request->store_order_id) { app(StoreCheckoutService::class)->capture($request->store_order_id); }
        $request->update(['status' => 'completed', 'settled_at' => now()]);
        $this->audit($request, $action, ['status' => $old], [
            'status' => 'completed', 'financial_id' => $financial->id,
            'matched_bank_message_id' => $request->matched_bank_message_id,
        ]);
        return $request;
    }

    private function audit(TopupRequest $request, string $action, array $old, array $new): void
    {
        AuditLog::create([
            'user_id' => auth()->id(), 'action' => $action,
            'entity' => 'topup_request', 'entity_id' => $request->id,
            'old_value' => $old, 'new_value' => $new, 'ip' => request()->ip(),
        ]);
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['payment' => $message]);
    }
}
