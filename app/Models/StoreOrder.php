<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreOrder extends Model
{
    protected $table = 'store_orders';

    protected $fillable = [
        'agent_id','user_id', 'group_id', 'phone', 'email', 'price', 'receipt_image', 'status','account_id'
    ];

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
