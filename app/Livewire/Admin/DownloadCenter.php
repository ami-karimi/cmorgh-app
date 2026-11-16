<?php

namespace App\Livewire\Admin;

use App\Models\DownloadCategory;
use App\Models\DownloadItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('مرکز دانلود | پنل مدیریت')]
#[Layout('layouts.admin')]
class DownloadCenter extends Component
{
    use WithFileUploads;

    public string $tab = 'items';
    public bool $modal = false, $categoryModal = false;
    #[Locked] public ?int $editingId = null;
    #[Locked] public ?int $categoryEditingId = null;
    public $title = '', $category_id = '', $short_description = '', $description = '', $version = '', $platform = 'عمومی';
    public bool $is_featured = false, $is_published = true;
    public $file, $cover;
    public string $categoryName = '', $categoryDescription = '', $categoryIcon = '';

    public function boot(): void
    {
        abort_unless(auth()->check() && in_array(auth()->user()->role, ['admin', 'manager'], true), 403);
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'title', 'category_id', 'short_description', 'description', 'version', 'platform', 'is_featured', 'is_published', 'file', 'cover']);
        $this->resetValidation();
        $this->modal = true;
    }

    public function edit(int $id): void
    {
        $item = DownloadItem::findOrFail($id);
        $this->editingId = $id;
        foreach (['title', 'category_id', 'short_description', 'description', 'version', 'platform', 'is_featured', 'is_published'] as $field) $this->$field = $item->$field;
        $this->reset(['file', 'cover']);
        $this->resetValidation();
        $this->modal = true;
    }

    public function saveItem(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255', 'category_id' => 'required|exists:download_categories,id',
            'short_description' => 'nullable|string|max:500', 'description' => 'nullable|string|max:50000',
            'version' => 'nullable|string|max:50', 'platform' => 'nullable|string|max:80',
            'is_featured' => 'boolean', 'is_published' => 'boolean',
            'file' => ($this->editingId ? 'nullable' : 'required').'|file|max:102400|extensions:apk,xapk,exe,msi,dmg,pkg,deb,rpm,zip,rar,7z,tar,gz,pdf,txt,conf,ovpn,doc,docx,mp4',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [], ['title'=>'عنوان', 'category_id'=>'دسته‌بندی', 'file'=>'فایل اصلی', 'cover'=>'تصویر کاور']);
        $old = $this->editingId ? DownloadItem::findOrFail($this->editingId) : null;
        unset($validated['file'], $validated['cover']);
        $newFile = $newCover = null;
        try {
            if ($this->file) {
                $newFile = $this->file->store('downloads/files', 'local');
                if (!$newFile) throw new \RuntimeException('Download storage failed.');
                $validated += ['file_path'=>$newFile, 'file_name'=>basename(str_replace('\\', '/', $this->file->getClientOriginalName())), 'file_mime'=>$this->file->getMimeType(), 'file_size'=>$this->file->getSize()];
            }
            if ($this->cover) {
                $newCover = $this->cover->store('downloads/covers', 'public');
                if (!$newCover) throw new \RuntimeException('Cover storage failed.');
                $validated['cover_path'] = $newCover;
            }
            if ($old) $old->update($validated);
            else DownloadItem::create($validated + ['slug'=>Str::slug($this->title).'-'.Str::lower(Str::random(8))]);
        } catch (\Throwable $e) {
            if ($newFile) Storage::disk('local')->delete($newFile);
            if ($newCover) Storage::disk('public')->delete($newCover);
            report($e);
            $this->addError('file', 'ذخیره انجام نشد؛ فایل قبلی حفظ شده است. فضای ذخیره‌سازی و گزارش سرور را بررسی کنید.');
            return;
        }
        // فایل‌های جایگزین‌شده برای جلوگیری از حذف اشتباهی حفظ می‌شوند؛ پاک‌سازی جداگانه انجام می‌شود.
        $this->modal = false;
        $this->reset(['file', 'cover']);
        session()->flash('success', 'فایل مرکز دانلود با موفقیت ذخیره شد.');
    }

    public function openCategory(): void
    {
        $this->reset(['categoryEditingId', 'categoryName', 'categoryDescription', 'categoryIcon']);
        $this->resetValidation();
        $this->categoryModal = true;
    }

    public function saveCategory(): void
    {
        $this->validate(['categoryName'=>'required|string|max:120', 'categoryDescription'=>'nullable|string|max:500', 'categoryIcon'=>'nullable|string|max:50']);
        $data = ['name'=>$this->categoryName, 'description'=>$this->categoryDescription, 'icon'=>$this->categoryIcon];
        if ($this->categoryEditingId) DownloadCategory::findOrFail($this->categoryEditingId)->update($data);
        else DownloadCategory::create($data + ['slug'=>Str::slug($this->categoryName).'-'.Str::lower(Str::random(8))]);
        $this->categoryModal = false;
        session()->flash('success', 'دسته‌بندی ذخیره شد.');
    }

    public function editCategory(int $id): void
    {
        $category = DownloadCategory::findOrFail($id);
        $this->categoryEditingId = $id;
        $this->categoryName = $category->name;
        $this->categoryDescription = $category->description ?? '';
        $this->categoryIcon = $category->icon ?? '';
        $this->resetValidation();
        $this->categoryModal = true;
    }

    public function toggleCategory(int $id): void
    {
        $category = DownloadCategory::findOrFail($id);
        $category->update(['is_active'=>!$category->is_active]);
    }

    public function toggle(int $id, string $field): void
    {
        abort_unless(in_array($field, ['is_published', 'is_featured'], true), 403);
        $item = DownloadItem::findOrFail($id);
        $item->update([$field=>!$item->$field]);
    }

    public function deleteItem(int $id): void
    {
        $item = DownloadItem::findOrFail($id);
        $file = $item->file_path;
        $cover = $item->cover_path;
        $item->delete();
        Storage::disk('local')->delete($file);
        if ($cover) Storage::disk('public')->delete($cover);
        session()->flash('success', 'فایل حذف شد.');
    }

    public function render()
    {
        return view('livewire.admin.download-center', [
            'items'=>DownloadItem::with('category')->latest()->get(),
            'categories'=>DownloadCategory::withCount('items')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }
}
