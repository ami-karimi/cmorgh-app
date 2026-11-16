<?php

namespace App\Http\Controllers;

use App\Models\DownloadItem;
use Illuminate\Support\Facades\Storage;

class DownloadCenterController
{
    public function show(string $slug)
    {
        $item = DownloadItem::published()->with('category')->where('slug', $slug)->firstOrFail();
        return view('downloads.show', compact('item'));
    }

    public function download(string $slug)
    {
        $item = DownloadItem::published()->where('slug', $slug)->firstOrFail();
        abort_unless(str_starts_with($item->file_path, 'downloads/files/') && !str_contains($item->file_path, '..'), 404);
        abort_unless(Storage::disk('local')->exists($item->file_path), 404, 'فایل در دسترس نیست.');
        $name = basename(str_replace('\\', '/', $item->file_name));
        $response = Storage::disk('local')->download($item->file_path, $name, [
            'Content-Type' => 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
        $item->increment('downloads');
        return $response;
    }
}
