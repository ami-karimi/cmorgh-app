<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\System\SystemCleaner;
use App\Services\System\SystemHealthFacade;
use App\Models\SystemHealthIssue;
use App\Models\SystemMaintenanceLog;
use App\Jobs\DeleteExpiredUsersJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SystemMaintenance extends Component
{
    use WithPagination;

    public $activeSubTab = 'health';

    public $logsInfo = [];
    public $isCleaningLogs = false;

    // حذف $expiredUsers به عنوان public property (اکنون از طریق render پاس داده می‌شود)
    public $isLoadingExpired = false;
    public $selectedUsers = [];
    public $perPage = 20;
    public $stats = [];

    public $healthResults = [];
    public $isRunningHealthCheck = false;
    public $healthIssues = [];
    public $jobStatus = null;

    public function mount()
    {
        $this->loadLogsInfo();
        $this->loadHealthIssues();
        $this->loadStats();
    }

    public function loadStats()
    {
        $cleaner = new SystemCleaner();
        $this->stats = $cleaner->getExpiredStats();
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

    // متد جدید که Paginator را با مدل‌های Eloquent برمی‌گرداند
    public function getExpiredUsersProperty()
    {
        // این متد به عنوان یک computed property عمل می‌کند
        // اما برای استفاده در view باید آن را در render صدا بزنیم
    }

    // متد برای بارگذاری کاربران منقضی با Pagination
    public function loadExpiredUsers()
    {
        // این متد فقط برای رفرش کردن صفحه استفاده می‌شود
        // اما Paginator از طریق render دریافت می‌شود
        $this->resetPage();
        // هیچ کاری انجام نمی‌دهد، فقط صفحه را ریست می‌کند
    }

    // متد برای دریافت Paginator با استفاده از WithPagination
    public function getExpiredUsersPaginator()
    {
        $cleaner = new SystemCleaner();
        return $cleaner->getExpiredUsersQuery()->paginate($this->perPage);
    }

    public function deleteSelectedUsers()
    {
        if (empty($this->selectedUsers)) {
            session()->flash('maintenance_error', 'هیچ کاربری انتخاب نشده است.');
            return;
        }

        DeleteExpiredUsersJob::dispatch($this->selectedUsers, Auth::id());

        $this->jobStatus = 'در حال پردازش...';
        session()->flash('maintenance_message', 'عملیات حذف به صف ارسال شد. نتیجه به زودی اعلام می‌شود.');
        $this->selectedUsers = [];
        $this->loadStats();
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
        // دریافت Paginator از سرویس
        $cleaner = new SystemCleaner();
        $expiredUsers = $cleaner->getExpiredUsersQuery()->paginate($this->perPage);

        return view('livewire.admin.settings.system-maintenance', [
            'expiredUsers' => $expiredUsers,
        ]);
    }
}
