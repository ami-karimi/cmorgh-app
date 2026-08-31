<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WireGuardUsers extends Model
{
    protected $table = 'wireguard_users';
    protected $guarded = ['id'];
    public function server()
    {
        return $this->belongsTo(Nas::class, 'server_id');
    }

}
