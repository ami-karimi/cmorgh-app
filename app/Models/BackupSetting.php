<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
{
    protected $fillable = [
        'enabled',
        'mikrotik_enabled',
        'database_enabled',
        'schedule',
        'run_at',
        'retention_days',
        'encrypt_files',
        'notify_on_failure',
        'notify_on_success',

        'storage_driver',
        'storage_host',
        'storage_port',
        'storage_username',
        'storage_password',
        'storage_private_key',
        'storage_root',
        'storage_folder_root',
        'storage_timeout',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'mikrotik_enabled' => 'boolean',
        'database_enabled' => 'boolean',
        'encrypt_files' => 'boolean',
        'notify_on_failure' => 'boolean',
        'notify_on_success' => 'boolean',
        'storage_port' => 'integer',
        'storage_timeout' => 'integer',
        'storage_password' => 'encrypted',
        'storage_private_key' => 'encrypted',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'enabled' => true,
                'mikrotik_enabled' => true,
                'database_enabled' => true,
                'run_at' => '03:00',
                'retention_days' => 30,
                'encrypt_files' => true,
                'storage_driver' => 'sftp',
                'storage_port' => 22,
                'storage_timeout' => 120,
            ]
        );
    }
}
