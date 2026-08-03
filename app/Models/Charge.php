<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $table = 'charge_lists';

    public function roles()
    {
        return $this->hasMany(ChargeRole::class, 'charge_id', 'id');
    }
}
