<?php

namespace App\Livewire\User;

use App\Models\DownloadCategory;
use App\Models\DownloadItem;
use Livewire\Component;
use Livewire\WithPagination;

class DownloadLibrary extends Component
{
    use WithPagination;

    public string $search = '';
    public string $category = '';
    public string $sort = 'latest';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedCategory(): void { $this->resetPage(); }
    public function updatedSort(): void { $this->resetPage(); }

    public function render()
    {
        $items = DownloadItem::published()->with('category')
            ->when($this->search !== '', fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')->orWhere('short_description', 'like', '%'.$this->search.'%');
            }))
            ->when($this->category !== '', fn ($q) => $q->where('category_id', $this->category));
        if ($this->sort === 'popular') $items->orderByDesc('downloads');
        else $items->orderByDesc('is_featured');
        $items->latest('id');

        $layout = match (auth()->user()?->role) {
            'agent', 'sub_agent' => 'layouts.agent',
            'customer' => 'layouts.customer',
            'admin', 'manager' => 'layouts.admin',
            default => 'layouts.app',
        };
        return view('livewire.user.download-library', [
            'items' => $items->paginate(12),
            'categories' => DownloadCategory::where('is_active', true)->withCount(['items' => fn ($q) => $q->where('is_published', true)])->orderBy('sort_order')->orderBy('name')->get(),
        ])->layout($layout)->title('مرکز دانلود');
    }
}
