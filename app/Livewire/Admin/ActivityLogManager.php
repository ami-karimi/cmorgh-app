<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\UserActivity;
use Morilog\Jalali\Jalalian;

#[Title('گزارش عملیات و رخدادها | همراه سیمرغ')]
#[Layout('layouts.admin')]
class ActivityLogManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = ''; // خوانده شده، جدید
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $perPage = 25;

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedFilterDateFrom() { $this->resetPage(); }
    public function updatedFilterDateTo() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    // خوانده شدن یک لاگ مشخص
    public function markAsRead($id)
    {
        UserActivity::where('id', $id)->update(['admin_view' => 1]);
    }

    // خوانده شدن تمام لاگ‌های صفحه فعلی یا کل سیستم
    public function markAllAsRead()
    {
        // فقط لاگ‌هایی که وضعیت admin_view آنها 0 است را آپدیت می‌کنیم
        UserActivity::where('admin_view', 0)->update(['admin_view' => 1]);
        session()->flash('message', 'تمام رخدادهای سیستم به عنوان خوانده‌شده علامت‌گذاری شدند.');
    }

    public function render()
    {
        $query = UserActivity::with(['causer', 'account'])->latest('id');

        if ($this->search) {
            $query->where('content', 'like', '%' . $this->search . '%')
                ->orWhereHas('causer', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('account', function($q) {
                    $q->where('username', 'like', '%' . $this->search . '%');
                });
        }

        if ($this->filterStatus === 'unread') {
            $query->where('admin_view', 0);
        } elseif ($this->filterStatus === 'read') {
            $query->where('admin_view', 1);
        }

        if ($this->filterDateFrom) {
            try {
                $gregorianFrom = Jalalian::fromFormat('Y/m/d', $this->filterDateFrom)->toCarbon()->startOfDay();
                $query->where('created_at', '>=', $gregorianFrom);
            } catch (\Exception $e) {}
        }

        if ($this->filterDateTo) {
            try {
                $gregorianTo = Jalalian::fromFormat('Y/m/d', $this->filterDateTo)->toCarbon()->endOfDay();
                $query->where('created_at', '<=', $gregorianTo);
            } catch (\Exception $e) {}
        }

        $logs = $query->paginate($this->perPage);

        // تعداد لاگ‌های جدید برای نمایش در هدر
        $unreadCount = UserActivity::where('admin_view', 0)->count();

        return view('livewire.admin.activity-log-manager', compact('logs', 'unreadCount'));
    }
}
