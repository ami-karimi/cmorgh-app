<?php

namespace App\View\Components\Ui;

use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class Announcements extends Component
{
    public Collection $announcements;

    public function __construct()
    {
        $target = in_array(auth()->user()?->role, ['agent', 'sub_agent'], true)
            ? 'agents'
            : 'customers';

        $this->announcements = Announcement::query()
            ->where('is_active', true)
            ->whereIn('target', ['all', $target])
            ->latest('id')
            ->limit(5)
            ->get(['id', 'title', 'content', 'created_at']);
    }

    public function render(): View
    {
        return view('components.ui.announcements');
    }
}
