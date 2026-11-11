<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionRedemption extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['reserved_until'=>'datetime', 'redeemed_at'=>'datetime'];
}
