<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['group_ids'=>'array', 'user_ids'=>'array', 'starts_at'=>'datetime', 'ends_at'=>'datetime', 'is_active'=>'boolean'];

    public function redemptions() { return $this->hasMany(PromotionRedemption::class); }
}
