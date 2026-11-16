<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Announcement;
use App\Models\User;
use App\Services\NotificationService;

class AnnouncementsManager extends Component
{
    public $title, $content, $target = 'all';
    public $delivery = 'announcement';
    public $recipientId = null;
    public $isModalOpen = false;

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target' => 'required|in:all,agents,customers',
            'delivery' => 'required|in:announcement,direct',
            'recipientId' => 'required_if:delivery,direct|nullable|exists:users,id',
        ]);

        if ($this->delivery === 'direct') {
            NotificationService::send(User::find($this->recipientId), $this->title, $this->content, 'admin.message');
            $message = 'اعلان مستقیم برای کاربر ارسال شد.';
        } else {
            Announcement::create(['title' => $this->title, 'content' => $this->content, 'target' => $this->target, 'is_active' => 1]);
            NotificationService::audience($this->target === 'all' ? 'all' : $this->target, $this->title, $this->content, 'announcement');
            $message = 'اطلاعیه برای مخاطبان انتخاب‌شده منتشر شد.';
        }

        $this->reset(['title', 'content', 'target', 'delivery', 'recipientId', 'isModalOpen']);
        $this->target = 'all'; $this->delivery = 'announcement';
        session()->flash('success', $message);
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
        // جدول users این پروژه username ندارد؛ برای انتخاب گیرنده از اطلاعات موجود استفاده می‌کنیم.
        $users = User::whereIn('role', ['agent', 'sub_agent', 'customer'])
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'email', 'role']);
        return view('livewire.admin.announcements-manager', compact('announcements', 'users'))
            ->layout('layouts.admin')->title('مدیریت اطلاعیه‌ها');
    }
}
