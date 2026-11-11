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
