<?php
// app/Models/SystemHealthIssue.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemHealthIssue extends Model
{
    protected $fillable = [
        'check_id', 'service', 'server_id', 'user_id', 'username',
        'issue_type', 'severity', 'details', 'status',
        'detected_at', 'resolved_at', 'resolved_by'
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function check(): BelongsTo
    {
        return $this->belongsTo(SystemHealthCheck::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
