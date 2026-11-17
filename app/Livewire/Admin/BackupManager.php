<?php

namespace App\Livewire\Admin;

use App\Jobs\BackupDatabaseJob;
use App\Jobs\BackupMikroTikJob;
use App\Jobs\CleanupOldBackupsJob;
use App\Models\Backup;
use App\Models\BackupSetting;
use App\Models\DeviceBackupSetting;
use App\Services\Backup\BackupDeviceResolver;
use App\Services\Backup\BackupStorageService;
use App\Services\Backup\MikroTikRestService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class BackupManager extends Component
{
    use WithPagination;

    public string $tab = 'devices';

    public string $typeFilter = '';

    public string $statusFilter = '';

    /*
    |--------------------------------------------------------------------------
    | تنظیمات عمومی
    |--------------------------------------------------------------------------
    */

    public bool $settingsModal = false;

    public bool $enabled = true;

    public bool $mikrotik_enabled = true;

    public bool $database_enabled = true;

    public string $run_at = '03:00';

    public int $retention_days = 30;

    public bool $encrypt_files = true;

    /*
    |--------------------------------------------------------------------------
    | تنظیمات هاست دانلود
    |--------------------------------------------------------------------------
    */

    public bool $storageModal = false;

    public string $storageDriver = 'sftp';

    public ?string $storageHost = null;

    public ?int $storagePort = null;

    public ?string $storageUsername = null;

    public ?string $storagePassword = null;

    public ?string $storagePrivateKey = null;

    public string $storageRoot = '/';

    public string $storageFolderRoot = 'cmorgh-backups';

    public int $storageTimeout = 120;

    public ?string $connectionTestResult = null;

    public bool $connectionTestStatus = false;

    /*
    |--------------------------------------------------------------------------
    | تنظیمات هر روتر
    |--------------------------------------------------------------------------
    */

    public bool $deviceModal = false;

    public ?int $editingDeviceId = null;

    public bool $deviceEnabled = true;

    public string $deviceFrequency = 'daily';

    public string $deviceRunAt = '03:00';

    public int $deviceWeekday = 6;

    public int $deviceDayOfMonth = 1;

    public ?int $deviceRetentionDays = null;

    public string $deviceEncrypt = 'inherit';

    public function boot(): void
    {
        abort_unless(
            auth()->check() &&
            in_array(
                auth()->user()->role,
                ['admin', 'manager'],
                true
            ),
            403
        );
    }

    /*
    |--------------------------------------------------------------------------
    | بکاپ دستی
    |--------------------------------------------------------------------------
    */


    public function testConnection(): void
    {
        $this->testStorageConnection();
    }

    public function backupDatabaseNow(): void
    {
        BackupDatabaseJob::dispatch()
            ->onQueue('backups');

        session()->flash(
            'success',
            'بکاپ دیتابیس در صف قرار گرفت.'
        );
    }

    public function backupDeviceNow(int $deviceId): void
    {
        BackupMikroTikJob::dispatch(
            $deviceId,
            now()->toDateTimeString(),
            true
        )->onQueue('backups');

        session()->flash(
            'success',
            'بکاپ روتر در صف قرار گرفت.'
        );
    }

    public function testMikroTikConnection(
        int $deviceId,
        BackupDeviceResolver $resolver,
        MikroTikRestService $rest
    ): void {
        $device = $resolver->query()->findOrFail($deviceId);

        $result = $rest->testConnection($device);

        if (! $result['ok']) {
            session()->flash(
                'error',
                'اتصال REST ناموفق بود: ' .
                $result['message']
            );

            return;
        }

        $identity = $result['identity']['name']
            ?? 'MikroTik';

        session()->flash(
            'success',
            "اتصال به {$identity} از طریق www-ssl موفق بود."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | تنظیمات عمومی
    |--------------------------------------------------------------------------
    */

    public function openSettings(): void
    {
        $settings = BackupSetting::current();

        $this->enabled = $settings->enabled;
        $this->mikrotik_enabled = $settings->mikrotik_enabled;
        $this->database_enabled = $settings->database_enabled;
        $this->run_at = $settings->run_at ?: '03:00';
        $this->retention_days = $settings->retention_days ?: 30;
        $this->encrypt_files = $settings->encrypt_files;

        $this->settingsModal = true;
    }

    public function saveSettings(): void
    {
        $this->validate([
            'run_at' => [
                'required',
                'regex:/^\d{2}:\d{2}$/',
            ],
            'retention_days' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],
        ]);

        BackupSetting::current()->update([
            'enabled' => $this->enabled,
            'mikrotik_enabled' => $this->mikrotik_enabled,
            'database_enabled' => $this->database_enabled,
            'run_at' => $this->run_at,
            'retention_days' => $this->retention_days,
            'encrypt_files' => $this->encrypt_files,
        ]);

        $this->settingsModal = false;

        session()->flash(
            'success',
            'تنظیمات عمومی ذخیره شد.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | تنظیمات هاست دانلود
    |--------------------------------------------------------------------------
    */

    public function openStorageSettings(): void
    {
        $settings = BackupSetting::current();

        $this->storageDriver = $settings->storage_driver ?: 'sftp';
        $this->storageHost = $settings->storage_host;
        $this->storagePort = $settings->storage_port ?: (
        $this->storageDriver === 'sftp' ? 22 : 21
        );
        $this->storageUsername = $settings->storage_username;
        $this->storagePassword = null;
        $this->storagePrivateKey = null;
        $this->storageRoot = $settings->storage_root ?: '/';
        $this->storageFolderRoot = $settings->storage_folder_root
            ?: 'cmorgh-backups';
        $this->storageTimeout = $settings->storage_timeout ?: 120;

        $this->connectionTestResult = null;
        $this->connectionTestStatus = false;
        $this->storageModal = true;
    }

    protected function storageConfigFromForm(): array
    {
        $config = [
            'driver' => $this->storageDriver,
            'host' => $this->storageHost,
            'username' => $this->storageUsername,
            'password' => $this->storagePassword,
            'port' => $this->storagePort,
            'root' => $this->storageRoot ?: '/',
            'timeout' => $this->storageTimeout,
        ];

        if ($this->storageDriver === 'sftp') {
            $config['privateKey'] = $this->storagePrivateKey;
        }

        if ($this->storageDriver === 'ftp') {
            $config['passive'] = true;
            $config['ssl'] = false;
            $config['ignorePassiveAddress'] = true;
        }

        return $config;
    }

    public function testStorageConnection(): void
    {
        $this->validate([
            'storageDriver' => [
                'required',
                'in:sftp,ftp',
            ],
            'storageHost' => [
                'required',
                'string',
                'max:255',
            ],
            'storageUsername' => [
                'required',
                'string',
                'max:255',
            ],
            'storageTimeout' => [
                'required',
                'integer',
                'min:10',
                'max:600',
            ],
        ]);

        $result = app(BackupStorageService::class)
            ->testConnection(
                $this->storageConfigFromForm()
            );

        $this->connectionTestStatus = $result['ok'];
        $this->connectionTestResult = $result['message'];
    }

    public function saveStorageSettings(): void
    {
        $this->validate([
            'storageDriver' => [
                'required',
                'in:sftp,ftp',
            ],
            'storageHost' => [
                'required',
                'string',
                'max:255',
            ],
            'storagePort' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
            ],
            'storageUsername' => [
                'required',
                'string',
                'max:255',
            ],
            'storageTimeout' => [
                'required',
                'integer',
                'min:10',
                'max:600',
            ],
            'storageFolderRoot' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $settings = BackupSetting::current();

        $data = [
            'storage_driver' => $this->storageDriver,
            'storage_host' => $this->storageHost,
            'storage_port' => $this->storagePort,
            'storage_username' => $this->storageUsername,
            'storage_root' => $this->storageRoot ?: '/',
            'storage_folder_root' => trim(
                $this->storageFolderRoot,
                '/'
            ),
            'storage_timeout' => $this->storageTimeout,
        ];

        /*
        | رمز خالی یعنی مقدار قبلی حفظ شود
        */
        if (
            $this->storagePassword !== null &&
            $this->storagePassword !== ''
        ) {
            $data['storage_password'] = $this->storagePassword;
        }

        if (
            $this->storagePrivateKey !== null &&
            $this->storagePrivateKey !== ''
        ) {
            $data['storage_private_key'] =
                $this->storagePrivateKey;
        }

        $settings->update($data);

        app(BackupStorageService::class)->reset();

        $this->storageModal = false;

        session()->flash(
            'success',
            'تنظیمات هاست دانلود ذخیره شد.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | تنظیمات هر روتر
    |--------------------------------------------------------------------------
    */

    public function openDeviceSettings(int $deviceId): void
    {
        $setting = DeviceBackupSetting::for($deviceId);

        $this->editingDeviceId = $deviceId;
        $this->deviceEnabled = $setting->enabled;
        $this->deviceFrequency = $setting->frequency;
        $this->deviceRunAt = $setting->run_at ?: '03:00';
        $this->deviceWeekday = $setting->weekday ?? 6;
        $this->deviceDayOfMonth = $setting->day_of_month ?? 1;
        $this->deviceRetentionDays = $setting->retention_days;

        $this->deviceEncrypt = is_null($setting->encrypt)
            ? 'inherit'
            : ($setting->encrypt ? 'on' : 'off');

        $this->deviceModal = true;
    }

    public function saveDeviceSettings(): void
    {
        $this->validate([
            'deviceFrequency' => [
                'required',
                'in:every_6_hours,every_12_hours,daily,weekly,monthly',
            ],
            'deviceRunAt' => [
                'required',
                'regex:/^\d{2}:\d{2}$/',
            ],
            'deviceWeekday' => [
                'required',
                'integer',
                'between:0,6',
            ],
            'deviceDayOfMonth' => [
                'required',
                'integer',
                'between:1,28',
            ],
            'deviceRetentionDays' => [
                'nullable',
                'integer',
                'min:1',
                'max:365',
            ],
        ]);

        $setting = DeviceBackupSetting::for(
            $this->editingDeviceId
        );

        $setting->fill([
            'enabled' => $this->deviceEnabled,
            'frequency' => $this->deviceFrequency,
            'run_at' => $this->deviceRunAt,
            'weekday' => $this->deviceFrequency === 'weekly'
                ? $this->deviceWeekday
                : null,
            'day_of_month' => $this->deviceFrequency === 'monthly'
                ? $this->deviceDayOfMonth
                : null,
            'retention_days' => $this->deviceRetentionDays ?: null,
            'encrypt' => match ($this->deviceEncrypt) {
                'on' => true,
                'off' => false,
                default => null,
            },
        ]);

        $setting->next_run_at = $setting->enabled
            ? $setting->calculateNextRun()
            : null;

        $setting->save();

        $this->deviceModal = false;

        session()->flash(
            'success',
            'تنظیمات بکاپ روتر ذخیره شد.'
        );
    }

    public function toggleDeviceBackup(int $deviceId): void
    {
        $setting = DeviceBackupSetting::for($deviceId);

        $enabled = ! $setting->enabled;

        $setting->update([
            'enabled' => $enabled,
            'next_run_at' => $enabled
                ? $setting->calculateNextRun()
                : null,
        ]);

        session()->flash(
            'success',
            $enabled
                ? 'بکاپ روتر فعال شد.'
                : 'بکاپ روتر غیرفعال شد.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | تاریخچه
    |--------------------------------------------------------------------------
    */

    public function cleanupNow(): void
    {
        CleanupOldBackupsJob::dispatch()
            ->onQueue('backups');

        session()->flash(
            'success',
            'پاکسازی بکاپ‌های قدیمی در صف قرار گرفت.'
        );
    }

    public function deleteBackup(
        int $id,
        BackupStorageService $storage
    ): void {
        $backup = Backup::findOrFail($id);

        try {
            $storage->delete($backup->path);
        } catch (\Throwable) {
        }

        $backup->delete();

        session()->flash(
            'success',
            'بکاپ حذف شد.'
        );
    }

    public function render(BackupDeviceResolver $resolver)
    {
        $configError = null;
        $devices = collect();

        try {
            $allDevices = $resolver->all();

            $settings = DeviceBackupSetting::query()
                ->whereIn(
                    'device_id',
                    $allDevices->pluck('id')
                )
                ->get()
                ->keyBy('device_id');

            $devices = $allDevices->map(
                function ($device) use ($resolver, $settings) {
                    $setting = $settings[$device->id]
                        ?? DeviceBackupSetting::for($device->id);

                    $lastBackup = Backup::query()
                        ->where('device_id', $device->id)
                        ->latest('id')
                        ->first();

                    return [
                        'id' => $device->id,
                        'name' => $resolver->nameOf($device),
                        'host' => $resolver->hostOf($device),
                        'last_backup' => $lastBackup,
                        'backup_enabled' => $setting->enabled,
                        'next_run_at' => $setting->next_run_at,
                        'schedule_label' => $setting->scheduleSummary(),
                        'last_status' => $setting->last_status,
                    ];
                }
            );
        } catch (\Throwable $e) {
            $configError = $e->getMessage();
        }

        $backups = Backup::query()
            ->when(
                $this->typeFilter,
                fn ($query) => $query->where(
                    'type',
                    $this->typeFilter
                )
            )
            ->when(
                $this->statusFilter,
                fn ($query) => $query->where(
                    'status',
                    $this->statusFilter
                )
            )
            ->latest('id')
            ->paginate(15);

        $stats = [
            'total' => Backup::count(),
            'success' => Backup::where(
                'status',
                'success'
            )->count(),
            'failed' => Backup::where(
                'status',
                'failed'
            )->count(),
            'size' => (int) Backup::where(
                'status',
                'success'
            )->sum('size'),
            'last_success' => Backup::where(
                'status',
                'success'
            )->latest('finished_at')->value('finished_at'),
            'next_run' => BackupSetting::current()->run_at,
        ];

        return view(
            'livewire.admin.backup-manager',
            compact(
                'devices',
                'backups',
                'stats',
                'configError'
            )
        )->title('مرکز پشتیبان‌گیری');
    }

    public function paginationView(): string
    {
        return 'livewire::tailwind';
    }
}
