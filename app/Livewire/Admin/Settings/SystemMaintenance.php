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
use App\Services\System\Checkers\WireGuardIntegrityChecker;

class SystemMaintenance extends Component
{
    use WithPagination;

    public $activeSubTab = 'health';

    public $logsInfo = [];
    public $isCleaningLogs = false;

    public $isLoadingExpired = false;
    public $selectedUsers = [];
    public $perPage = 20;
    public $stats = [];

    public $healthResults = [];
    public $isRunningHealthCheck = false;
    public $healthIssues = [];
    public $jobStatus = null;

    // متغیرهای مربوط به مودال تأیید پاکسازی کلی
    public $showBulkConfirmModal = false;
    public $bulkTotalCount = 0;

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

    public function loadExpiredUsers()
    {
        $this->resetPage(); // ریست صفحه‌بندی
        $this->loadStats(); // به‌روزرسانی آمار
        // داده‌ها از طریق render() دریافت می‌شوند
    }

    // متد برای باز کردن مودال تأیید پاکسازی کلی
    public function openBulkDeleteConfirm()
    {
        $cleaner = new SystemCleaner();
        $query = $cleaner->getExpiredUsersQuery();
        $this->bulkTotalCount = $query->count();

        if ($this->bulkTotalCount == 0) {
            session()->flash('maintenance_error', 'هیچ کاربر قابل پاکسازی‌ای وجود ندارد.');
            return;
        }

        $this->showBulkConfirmModal = true;
    }


    public function deleteWireguardOrphan($issueId)
    {
        $issue = SystemHealthIssue::find($issueId);
        if (!$issue || $issue->service !== 'wireguard' || $issue->issue_type !== 'orphan') {
            session()->flash('maintenance_error', 'Issue معتبر نیست.');
            return;
        }

        $checker = new \App\Services\System\Checkers\WireGuardIntegrityChecker();
        $result = $checker->deleteOrphanPeer($issue->server_id, $issue->username);

        if ($result['status']) {
            $issue->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'resolved_by' => Auth::id()
            ]);
            session()->flash('maintenance_message', 'Peer Orphan با موفقیت حذف شد.');
        } else {
            session()->flash('maintenance_error', 'خطا در حذف: ' . $result['message']);
        }
        $this->loadHealthIssues();
    }

    public function deleteAllWireguardOrphans()
    {
        $checker = new \App\Services\System\Checkers\WireGuardIntegrityChecker();
        $result = $checker->deleteAllOrphansAllServers();

        if ($result['status']) {
            session()->flash('maintenance_message', $result['message']);
            // بستن تمام issues مربوطه
            SystemHealthIssue::where('service', 'wireguard')
                ->where('issue_type', 'orphan')
                ->where('status', 'open')
                ->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                    'resolved_by' => Auth::id()
                ]);
        } else {
            session()->flash('maintenance_error', 'خطا در حذف Orphan‌ها: ' . $result['message']);
        }
        $this->loadHealthIssues();
    }

    // متد اجرای پاکسازی کلی (همه کاربران)
    public function bulkDeleteAll()
    {
        $cleaner = new SystemCleaner();
        $query = $cleaner->getExpiredUsersQuery();
        $allUserIds = $query->pluck('id')->toArray();

        if (empty($allUserIds)) {
            session()->flash('maintenance_error', 'هیچ کاربر قابل پاکسازی‌ای وجود ندارد.');
            $this->showBulkConfirmModal = false;
            return;
        }

        // ارسال همه IDها به Job
        DeleteExpiredUsersJob::dispatch($allUserIds, Auth::id());

        $this->jobStatus = 'در حال پردازش...';
        session()->flash('maintenance_message', 'عملیات پاکسازی کلی (' . count($allUserIds) . ' کاربر) به صف ارسال شد. نتیجه به زودی اعلام می‌شود.');
        $this->selectedUsers = [];
        $this->showBulkConfirmModal = false;
        $this->loadStats();
    }

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
            $facade = new \App\Services\System\SystemHealthFacade();
            $this->healthResults = $facade->runFullCheck();

            // بارگذاری مجدد issues
            $this->loadHealthIssues();

            // پیام موفقیت با جزئیات
            $totalIssues = collect($this->healthResults)->sum('issues_count');
            session()->flash('maintenance_message', "بررسی سلامت سیستم با موفقیت انجام شد. تعداد کل مغایرت‌ها: {$totalIssues}");

        } catch (\Exception $e) {
            Log::error('Health check error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
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
        $cleaner = new SystemCleaner();
        $expiredUsers = $cleaner->getExpiredUsersQuery()->paginate($this->perPage);

        return view('livewire.admin.settings.system-maintenance', [
            'expiredUsers' => $expiredUsers,
        ]);
    }
}
