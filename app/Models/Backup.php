<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $fillable = [
        'type',
        'device_id',
        'device_name',
        'disk',
        'folder',
        'filename',
        'path',
        'size',
        'checksum',
        'encrypted',
        'status',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'encrypted' => 'boolean',
        'size' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public const TYPE_LABELS = [
        'mikrotik_binary' => 'بکاپ باینری MikroTik',
        'mikrotik_export' => 'Export تنظیمات MikroTik',
        'database' => 'بکاپ دیتابیس',
    ];

    public const STATUS_LABELS = [
        'queued' => 'در صف',
        'running' => 'در حال اجرا',
        'success' => 'موفق',
        'failed' => 'ناموفق',
        'expired' => 'منقضی شده',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? 'نامشخص';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getSizeForHumansAttribute(): string
    {
        $bytes = (int) $this->size;

        if ($bytes <= 0) {
            return '—';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = (int) floor(log($bytes, 1024));

        return round($bytes / (1024 ** $index), 2) . ' ' . $units[$index];
    }
}
