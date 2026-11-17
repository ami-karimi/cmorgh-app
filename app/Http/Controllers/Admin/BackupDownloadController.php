<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Services\Backup\BackupEncryptionService;
use App\Services\Backup\BackupStorageService;

class BackupDownloadController extends Controller
{
    public function __invoke(Backup $backup, BackupStorageService $storage)
    {
        abort_unless(in_array(auth()->user()?->role, ['admin', 'manager'], true), 403);
        abort_unless($storage->exists($backup->path), 404, 'فایل بکاپ پیدا نشد.');

        $contents = $storage->disk()->get($backup->path);

        abort_if($contents === null, 404, 'خواندن فایل بکاپ ناموفق بود.');

        if ($backup->encrypted) {
            $contents = app(BackupEncryptionService::class)->decrypt($contents);
        }

        $downloadName = str_replace('.enc', '', $backup->filename);

        return response()->streamDownload(
            fn () => print($contents),
            $downloadName,
            ['Content-Type' => 'application/octet-stream']
        );
    }
}
