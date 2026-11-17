<?php

use App\Models\Nas;

return [

    /*
    |--------------------------------------------------------------------------
    | Device Model
    |--------------------------------------------------------------------------
    */

    'device_model' => Nas::class,

    'device_name_column' => 'name',

    'device_active_column' => 'is_active',

    /*
    |--------------------------------------------------------------------------
    | Backup
    |--------------------------------------------------------------------------
    */

    'folder_root' => env('BACKUP_FOLDER_ROOT', 'cmorgh-backups'),

    'encrypt' => (bool) env('BACKUP_ENCRYPT', true),

    'encryption_key' => env('BACKUP_ENCRYPTION_KEY'),

    'download_link_minutes' => 10,

    /*
    |--------------------------------------------------------------------------
    | MikroTik REST API
    |--------------------------------------------------------------------------
    */

    'mikrotik' => [

        'scheme' => env('MIKROTIK_REST_SCHEME', 'https'),

        'port' => (int) env('MIKROTIK_REST_PORT', 443),

        'base_path' => env('MIKROTIK_REST_BASE_PATH', '/rest'),

        'verify_ssl' => (bool) env('MIKROTIK_REST_VERIFY_SSL', false),

        'timeout' => (int) env('MIKROTIK_REST_TIMEOUT', 55),

        /*
        |--------------------------------------------------------------------------
        | ستون‌های جدول روتر
        |--------------------------------------------------------------------------
        */

        'host_column' => 'host',

        'fallback_host_column' => 'ip',

        'username_column' => 'username',

        'password_column' => 'password',

        'port_column' => 'rest_port',
    ],

    /*
    |--------------------------------------------------------------------------
    | SFTP برای دریافت فایل backup از MikroTik
    |--------------------------------------------------------------------------
    */

    'sftp' => [
        'enabled' => (bool) env('MIKROTIK_SFTP_ENABLED', false),

        'port' => (int) env('MIKROTIK_SFTP_PORT', 22),

        'username_column' => 'ssh_username',

        'password_column' => 'ssh_password',

        'fallback_username_column' => 'username',

        'fallback_password_column' => 'password',
    ],

    'ftp' => [
        'enabled' => (bool) env('MIKROTIK_FTP_ENABLED', true),

        'port' => (int) env('MIKROTIK_FTP_PORT', 6044),

        'passive' => (bool) env('MIKROTIK_FTP_PASSIVE', true),

        'username_column' => 'mikrotik_username',

        'password_column' => 'mikrotik_password',

        'fallback_username_column' => 'mikrotik_username',

        'fallback_password_column' => 'mikrotik_password',
    ],

];
