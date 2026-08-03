<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Announcement;

class AnnouncementsManager extends Component
{
    public $title, $content, $target = 'all';
    public $isModalOpen = false;

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target' => 'required|in:all,agents,customers',
        ]);

        Announcement::create([
            'title' => $this->title,
            'content' => $this->content,
            'target' => $this->target,
            'is_active' => 1,
        ]);

        $this->reset(['title', 'content', 'target', 'isModalOpen']);
        session()->flash('success', 'اطلاعیه با موفقیت ثبت و منتشر شد.');
    }

    public function delete($id)
    {
        Announcement::find($id)->delete();
    }

    public function toggleActive($id)
    {
        $announcement = Announcement::find($id);
        $announcement->update(['is_active' => !$announcement->is_active]);
    }

    public function render()
    {
        $announcements = Announcement::latest()->get();
        return view('livewire.admin.announcements-manager', compact('announcements'))
            ->layout('layouts.admin')->title('مدیریت اطلاعیه‌ها');
    }
}
