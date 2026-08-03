<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WireguardUsers extends Model
{
    protected $table = 'wireguard_users';
    protected $guarded = ['id'];
}
