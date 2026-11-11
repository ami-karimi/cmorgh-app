<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\PromotionRedemption;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class PromotionService
{
    public function quote(int $amount, ?User $user, ?int $groupId, string $kind, ?string $code = null): array
    {
        $quote = ['original' => $amount, 'discount' => 0, 'total' => $amount, 'promotion_id' => null];
        if (!Schema::hasTable('promotions')) {
            if ($code) { $this->fail('سیستم تخفیف هنوز راه‌اندازی نشده است.'); }
            return $quote;
        }
        $query = Promotion::where('is_active', true);
        if ($code !== null && trim($code) !== '') {
            $query->where('kind', 'coupon')->where('code', strtoupper(trim($code)));
        } else {
            $query->where('kind', $kind)->whereNull('code');
        }
        foreach ($query->orderBy('id')->get() as $promotion) {
            if (!$this->eligible($promotion, $amount, $user, $groupId)) { continue; }
            $discount = $promotion->value_type === 'percent' ? intdiv($amount * (int) $promotion->value, 100) : (int) $promotion->value;
            if ($promotion->max_discount !== null) { $discount = min($discount, (int) $promotion->max_discount); }
            if ($kind !== 'topup_bonus') { $discount = min($discount, $amount); }
            if ($discount > $quote['discount']) {
                $quote = ['original'=>$amount, 'discount'=>$discount, 'total'=>$kind === 'topup_bonus' ? $amount : $amount-$discount, 'promotion_id'=>$promotion->id];
            }
        }
        if ($code && !$quote['promotion_id']) { $this->fail('کد تخفیف نامعتبر، منقضی یا برای این سفارش غیرقابل استفاده است.'); }
        return $quote;
    }

    private function eligible(Promotion $p, int $amount, ?User $user, ?int $groupId, bool $lock = false): bool
    {
        if (($p->starts_at && $p->starts_at->isFuture()) || ($p->ends_at && $p->ends_at->lte(now())) || $amount < $p->min_amount) { return false; }
        $audience = $user && in_array($user->role, ['agent','sub_agent'], true) ? 'agent' : 'customer';
        if ($p->audience !== 'all' && $p->audience !== $audience) { return false; }
        if ($p->group_ids && !in_array($groupId, $p->group_ids)) { return false; }
        if ($p->user_ids && (!$user || !in_array($user->id, $p->user_ids))) { return false; }
        $active = $p->redemptions()->where(fn ($q) => $q->whereNotNull('redeemed_at')->orWhereNull('reserved_until')->orWhere('reserved_until', '>', now()));
        $rows = $lock && ($p->usage_limit || $p->per_user_limit) ? (clone $active)->lockForUpdate()->get(['id','user_id']) : null;
        if ($p->usage_limit && ($rows ? $rows->count() : (clone $active)->count()) >= $p->usage_limit) { return false; }
        if ($p->per_user_limit && (!$user || ($rows ? $rows->where('user_id',$user->id)->count() : (clone $active)->where('user_id',$user->id)->count()) >= $p->per_user_limit)) { return false; }
        return true;
    }

    /** Caller must be inside its checkout/topup transaction; the promotion lock serializes reservations. */
    public function reserve(array $quote, User $user, ?int $groupId, string $context, int $referenceId, $until): void
    {
        if (!$quote['promotion_id']) { return; }
        $promotion = Promotion::lockForUpdate()->findOrFail($quote['promotion_id']);
        if (!$promotion->is_active || !$this->eligible($promotion, $quote['original'], $user, $groupId, true)) { $this->fail('ظرفیت یا زمان تخفیف تغییر کرده است؛ دوباره سفارش را بررسی کنید.'); }
        PromotionRedemption::create([
            'promotion_id'=>$promotion->id, 'user_id'=>$user->id, 'context'=>$context,
            'reference_id'=>$referenceId, 'amount'=>$quote['discount'], 'reserved_until'=>$until,
        ]);
    }

    public function redeem(string $context, int $referenceId): void
    {
        if (Schema::hasTable('promotion_redemptions')) {
            PromotionRedemption::where('context', $context)->where('reference_id', $referenceId)
                ->whereNull('redeemed_at')->update(['redeemed_at'=>now(), 'reserved_until'=>null]);
        }
    }

    public function release(string $context, int $referenceId): void
    {
        if (Schema::hasTable('promotion_redemptions')) {
            PromotionRedemption::where('context', $context)->where('reference_id', $referenceId)
                ->whereNull('redeemed_at')->update(['reserved_until'=>now()]);
        }
    }

    private function fail(string $message): never { throw ValidationException::withMessages(['coupon'=>$message]); }
}
