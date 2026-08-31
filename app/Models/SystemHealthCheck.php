<?php

// app/Models/SystemHealthCheck.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemHealthCheck extends Model
{
    protected $fillable = [
        'service', 'status', 'summary', 'started_at', 'completed_at'
    ];

    protected $casts = [
        'summary' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function issues(): HasMany
    {
        return $this->hasMany(SystemHealthIssue::class, 'check_id');
    }
}
