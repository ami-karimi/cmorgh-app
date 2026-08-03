<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    protected $table = 'accounts';
    protected $guarded = ['id'];


    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    public function panelUser()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    public function creatorUser()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    public function formatBytes($bytes, $precision = 2)
    {
        if ($bytes <= 0) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    public function getUsedFormattedAttribute()
    {
        return $this->formatBytes($this->usage ?? 0);
    }
    public function getMaxFormattedAttribute()
    {
        return ($this->max_usage && $this->max_usage > 0)
            ? $this->formatBytes($this->max_usage)
            : 'نامحدود ∞';
    }
    public function getRemainingFormattedAttribute()
    {
        if (!$this->max_usage || $this->max_usage <= 0) {
            return 'نامحدود ∞';
        }

        $remainingBytes = max(0, $this->max_usage - ($this->usage ?? 0));
        return $this->formatBytes($remainingBytes);
    }
    public function getUsagePercentageAttribute()
    {
        if (!$this->max_usage || $this->max_usage <= 0) {
            return 0;
        }

        $percent = (($this->usage ?? 0) / $this->max_usage) * 100;
        return min(100, round($percent, 1));
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'user_accounts', 'account_id', 'user_id')->withTimestamps();
    }

    public function getCustomerAttribute()
    {
        return $this->users()->first();
    }


}
