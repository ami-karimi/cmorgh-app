<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreOrder extends Model
{
    protected $table = 'store_orders';

    protected $fillable = [
        'agent_id','user_id', 'group_id', 'phone', 'email', 'price', 'receipt_image', 'status','account_id', 'payment_method', 'original_price', 'discount_amount', 'promotion_id', 'paid_at', 'fulfillment_state', 'fulfillment_error', 'fulfillment_started_at', 'checkout_key', 'agent_costs'
    ];

    protected $casts = ['paid_at'=>'datetime', 'fulfillment_started_at'=>'datetime', 'agent_costs'=>'array'];

    public function getCanRetryFulfillmentAttribute(): bool
    {
        if (!$this->paid_at || $this->status !== 'paid' || $this->account_id) { return false; }
        if (in_array($this->fulfillment_state,['waiting','failed'],true)) { return true; }
        return $this->fulfillment_state === 'requires_review'
            && str_contains($this->fulfillment_error ?? '', 'Unknown column')
            && (bool) preg_match('/insert\s+into\s+[`"]?accounts[`"]?\s*\(/i', $this->fulfillment_error ?? '');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => $this->payment_method === 'bank' ? 'در انتظار پرداخت بانکی' : 'در انتظار بررسی فیش',
            'paid' => match ($this->fulfillment_state) {
                'failed' => 'پرداخت تأیید شد؛ خطای صدور',
                'requires_review' => 'پرداخت تأیید شد؛ نیازمند بررسی صدور',
                'processing' => 'پرداخت تأیید شد؛ در حال صدور',
                default => 'پرداخت تأیید شده',
            },
            'approved' => $this->account_id ? 'اکانت صادر شده' : 'تأیید شده',
            'rejected' => 'رد شده',
            'cancelled' => 'لغو شده',
            'refunded' => 'هزینه برگشت داده شد',
            default => 'وضعیت نامشخص',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'approved', 'refunded' => 'commerce-badge-success',
            'pending' => 'commerce-badge-warning',
            'paid' => in_array($this->fulfillment_state,['failed','requires_review'],true) ? 'commerce-badge-warning' : '',
            default => 'commerce-badge-muted',
        };
    }

    public function bankPayment() { return $this->hasOne(TopupRequest::class, 'store_order_id'); }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function account()
    {
        return $this->belongsTo(Accounts::class, 'account_id');
    }

}
