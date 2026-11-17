<?php

namespace App\Services\Backup;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class BackupDeviceResolver
{
    public function modelClass(): string
    {
        $model = config('backup.device_model');

        if (
            ! is_string($model) ||
            $model === '' ||
            ! class_exists($model)
        ) {
            throw new RuntimeException(
                'مدل روتر در config/backup.php معتبر نیست.'
            );
        }

        return $model;
    }

    public function query(): Builder
    {
        $model = $this->modelClass();
        $instance = new $model;

        $activeColumn = config('backup.device_active_column');

        $query = $model::query();

        if (
            $activeColumn &&
            Schema::hasColumn($instance->getTable(), $activeColumn)
        ) {
            $query->where($activeColumn, true);
        }

        return $query;
    }

    public function all(): Collection
    {
        return $this->query()->get();
    }

    public function nameOf(object $device): string
    {
        $column = config('backup.device_name_column', 'name');

        return (string) (
            $device->{$column}
            ?? $device->name
            ?? $device->id
        );
    }

    public function hostOf(object $device): string
    {
        foreach ([
                     'mikrotik_domain',
                     'ipaddress',
                     'address',
                     'server_ip',
                     'ip_address',
                 ] as $column) {
            if (! empty($device->{$column})) {
                return (string) $device->{$column};
            }
        }

        return '—';
    }
}
