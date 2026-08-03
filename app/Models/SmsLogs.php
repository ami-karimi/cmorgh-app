<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLogs extends Model
{
    protected $guarded = [];
    protected $casts = [
        'parameters' => 'array',
    ];
}
