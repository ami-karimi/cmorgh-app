<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WireGuardUsers extends Model
{
    protected $table = 'wireguard_users';
    protected $guarded = ['id'];
    public function server()
    {
        return $this->belongsTo(Nas::class, 'server_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Accounts::class, 'user_id');
    }

}
