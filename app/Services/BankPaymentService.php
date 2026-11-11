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
    public function createRequest(int $userId, int $amount): TopupRequest
    {
        if ($amount < 1000 || $amount > 100000000) {
            $this->fail('مبلغ شارژ باید بین ۱٬۰۰۰ تا ۱۰۰٬۰۰۰٬۰۰۰ تومان باشد.');
        }
        return DB::transaction(function () use ($userId, $amount) {
            \App\Models\User::lockForUpdate()->findOrFail($userId);
            TopupRequest::where('user_id', $userId)->where('status', 'pending')
                ->where('expires_at', '<=', now())->update(['status' => 'expired']);
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
                    return TopupRequest::create([
                        'user_id' => $userId, 'requested_amount' => $amount,
                        'unique_amount' => $offset * 10, 'payable_amount' => $payable,
                        'status' => 'pending', 'expires_at' => now()->addMinutes(20),
                    ]);
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
        if (!$request->user()->exists() || $request->requested_amount <= 0) {
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
