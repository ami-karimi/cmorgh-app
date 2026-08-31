<?php
// app/Livewire/Admin/Settings/SystemMaintenance.php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Services\System\SystemCleaner;
use App\Services\System\SystemHealthFacade;
use App\Models\SystemHealthIssue;
use App\Models\SystemMaintenanceLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SystemMaintenance extends Component
{
    public $activeSubTab = 'health'; // health, cleanup, logs
    public $perPage = 50;
    // پاکسازی لاگ‌ها
    public $logsInfo = [];
    public $isCleaningLogs = false;

    // پاکسازی کاربران
    public $expiredUsers = [];
    public $isLoadingExpired = false;
    public $selectedUsers = [];

    // سلامت
    public $healthResults = [];
    public $isRunningHealthCheck = false;
    public $healthIssues = [];

    public function mount()
    {
        $this->loadLogsInfo();
        $this->loadHealthIssues();
    }

    public function loadLogsInfo()
    {
        $cleaner = new SystemCleaner();
        $this->logsInfo = $cleaner->getLogsSize();
    }

    public function cleanLogs()
    {
        $this->isCleaningLogs = true;
        try {
            $cleaner = new SystemCleaner();
            $result = $cleaner->cleanLogs();

            SystemMaintenanceLog::create([
                'admin_id' => Auth::id(),
                'action' => 'clean_logs',
                'status' => 'success',
                'message' => "{$result['deleted_count']} فایل لاگ با حجم {$result['deleted_size_human']} پاکسازی شد.",
                'started_at' => now(),
                'finished_at' => now(),
            ]);

            session()->flash('maintenance_message', 'لاگ‌ها با موفقیت پاکسازی شدند.');
            $this->loadLogsInfo();
        } catch (\Exception $e) {
            Log::error('Clean logs error: ' . $e->getMessage());
            session()->flash('maintenance_error', 'خطا در پاکسازی لاگ‌ها: ' . $e->getMessage());
        }
        $this->isCleaningLogs = false;
    }

    public function loadExpiredUsers()
    {
        $this->isLoadingExpired = true;
        try {
            $cleaner = new SystemCleaner();
            // استفاده از paginate برای بارگذاری تدریجی
            $this->expiredUsers = $cleaner->findExpiredUsersPaginated(15, $this->perPage);
        } catch (\Exception $e) {
            Log::error('Load expired users error: ' . $e->getMessage());
            session()->flash('maintenance_error', 'خطا در بارگذاری کاربران منقضی: ' . $e->getMessage());
        }
        $this->isLoadingExpired = false;
    }

    public function deleteSelectedUsers()
    {
        if (empty($this->selectedUsers)) {
            session()->flash('maintenance_error', 'هیچ کاربری انتخاب نشده است.');
            return;
        }

        // در اینجا عملیات حذف واقعی انجام می‌شود
        // باید ابتدا از سرورهای خارجی حذف شود

        // ثبت لاگ
        SystemMaintenanceLog::create([
            'admin_id' => Auth::id(),
            'action' => 'delete_expired_users',
            'target' => implode(', ', $this->selectedUsers),
            'status' => 'success',
            'message' => count($this->selectedUsers) . ' کاربر منقضی حذف شدند.',
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        session()->flash('maintenance_message', count($this->selectedUsers) . ' کاربر با موفقیت حذف شدند.');
        $this->selectedUsers = [];
        $this->loadExpiredUsers();
    }

    public function runHealthCheck()
    {
        $this->isRunningHealthCheck = true;
        try {
            $facade = new SystemHealthFacade();
            $this->healthResults = $facade->runFullCheck();
            $this->loadHealthIssues();
            session()->flash('maintenance_message', 'بررسی سلامت سیستم با موفقیت انجام شد.');
        } catch (\Exception $e) {
            Log::error('Health check error: ' . $e->getMessage());
            session()->flash('maintenance_error', 'خطا در بررسی سلامت: ' . $e->getMessage());
        }
        $this->isRunningHealthCheck = false;
    }

    public function loadHealthIssues()
    {
        $facade = new SystemHealthFacade();
        $this->healthIssues = $facade->getLatestIssues(50);
    }

    public function ignoreIssue($issueId)
    {
        $issue = SystemHealthIssue::find($issueId);
        if ($issue && $issue->status === 'open') {
            $issue->update([
                'status' => 'ignored',
                'resolved_at' => now(),
                'resolved_by' => Auth::id(),
            ]);
            $this->loadHealthIssues();
            session()->flash('maintenance_message', 'Issue با موفقیت نادیده گرفته شد.');
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.system-maintenance');
    }
}
