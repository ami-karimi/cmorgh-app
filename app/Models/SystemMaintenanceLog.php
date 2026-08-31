<?php
// app/Models/SystemMaintenanceLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemMaintenanceLog extends Model
{
    protected $fillable = [
        'admin_id', 'action', 'service', 'target', 'status', 'message', 'error',
        'started_at', 'finished_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
