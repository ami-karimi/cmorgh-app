<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nas extends Model
{
    protected $table = 'nas';

    protected $guarded = [];

    protected $casts = [
        'server_type' => 'array',
        'is_enabled' => 'boolean',
        'in_app' => 'boolean',
        'unlimited' => 'boolean',
    ];

    public function scopeSupportsProtocol($query, $protocol)
    {
        return $query->whereJsonContains('server_type', $protocol);
    }

    public function getUsersOnline(){
        return $this->hasMany(Radacct::class,'nasipaddress','ipaddress')->where('acctstoptime','=',NULL);
    }
}
