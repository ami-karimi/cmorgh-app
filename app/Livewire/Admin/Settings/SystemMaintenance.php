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
use App\Models\Nas;


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

    // حذف: public $healthIssues = [];

    public $jobStatus = null;

    public $showBulkConfirmModal = false;
    public $bulkTotalCount = 0;

    // برای Pagination Issues
    public $issuePerPage = 20;

    public $filterService = 'all';
    public $filterIssueType = 'all';
    public $filterSeverity = 'all';
    public $filterStatus = 'open';
    public $filterServerId = 'all';
    public $filterSearch = '';

    public function mount()
    {
        $this->loadLogsInfo();
        $this->loadStats();
    }


    public function updated($property)
    {
        if (in_array($property, ['filterService', 'filterIssueType', 'filterSeverity', 'filterStatus', 'filterServerId', 'filterSearch'])) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->filterService = 'all';
        $this->filterIssueType = 'all';
        $this->filterSeverity = 'all';
        $this->filterStatus = 'open';
        $this->filterServerId = 'all';
        $this->filterSearch = '';
        $this->resetPage();
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

    public function loadExpiredUsers()
    {
        $this->resetPage();
        $this->loadStats();
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

        DeleteExpiredUsersJob::dispatch($allUserIds, Auth::id());

        $this->jobStatus = 'در حال پردازش...';
        session()->flash('maintenance_message', 'عملیات پاکسازی کلی (' . count($allUserIds) . ' کاربر) به صف ارسال شد. نتیجه به زودی اعلام می‌شود.');
        $this->selectedUsers = [];
        $this->showBulkConfirmModal = false;
        $this->loadStats();
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

    // =============================================
    // بررسی سلامت سیستم
    // =============================================
    public function runHealthCheck()
    {
        $this->isRunningHealthCheck = true;

        try {
            Log::info('شروع بررسی سلامت سیستم');

            $facade = new SystemHealthFacade();
            $this->healthResults = $facade->runFullCheck();

            // دیگر نیازی به loadHealthIssues نیست چون render دوباره اجرا می‌شود
            $totalIssues = collect($this->healthResults)->sum('issues_count');
            session()->flash('maintenance_message', "بررسی سلامت سیستم با موفقیت انجام شد. تعداد کل مغایرت‌ها: {$totalIssues}");

        } catch (\Exception $e) {
            Log::error('Health check error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            session()->flash('maintenance_error', 'خطا در بررسی سلامت: ' . $e->getMessage());
        }

        $this->isRunningHealthCheck = false;
    }

    public function updatedIssuePerPage()
    {
        $this->resetPage();
    }

    // =============================================
    // عملیات WireGuard
    // =============================================
    public function handleWireguardAction($issueId, $action)
    {
        $issue = SystemHealthIssue::find($issueId);
        if (!$issue || $issue->service !== 'wireguard' || $issue->status !== 'open') {
            session()->flash('maintenance_error', 'Issue معتبر نیست.');
            return;
        }

        $checker = new \App\Services\System\Checkers\WireGuardIntegrityChecker();
        $result = null;

        switch ($action) {
            case 'create_config_and_peer':
                $result = $checker->createConfigAndPeer($issue->server_id, $issue->username);
                break;
            case 'recreate_peer':
                $result = $checker->recreatePeer($issue->server_id, $issue->username);
                break;
            case 'delete_orphan':
                $result = $checker->deleteOrphan($issue->server_id, $issue->username);
                break;
            case 'create_account':
                $result = $checker->createAccountFromPeer($issue->server_id, $issue->username);
                break;
            case 'sync_speed':
                $result = $checker->syncSpeed($issue->server_id, $issue->username);
                break;
            default:
                session()->flash('maintenance_error', 'عملیات نامعتبر.');
                return;
        }

        if ($result['status']) {
            $issue->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'resolved_by' => Auth::id()
            ]);
            session()->flash('maintenance_message', $result['message']);
        } else {
            session()->flash('maintenance_error', 'خطا: ' . $result['message']);
        }
        // نیازی به loadHealthIssues نیست، render دوباره اجرا می‌شود
    }

    public function deleteAllWireguardOrphans()
    {
        $checker = new \App\Services\System\Checkers\WireGuardIntegrityChecker();
        $result = $checker->deleteAllOrphans();

        if ($result['status']) {
            session()->flash('maintenance_message', $result['message']);
            SystemHealthIssue::where('service', 'wireguard')
                ->whereIn('issue_type', ['orphan_peer_only', 'orphan_full', 'orphan_peer_config'])
                ->where('status', 'open')
                ->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                    'resolved_by' => Auth::id()
                ]);
        } else {
            session()->flash('maintenance_error', 'خطا: ' . $result['message']);
        }
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
            session()->flash('maintenance_message', 'Issue با موفقیت نادیده گرفته شد.');
        }
    }

    // =============================================
    // RENDER
    // =============================================
    public function render()
    {
        $cleaner = new SystemCleaner();
        $expiredUsers = $cleaner->getExpiredUsersQuery()->paginate($this->perPage);

        // ساخت کوئری Issues با فیلترها
        $query = SystemHealthIssue::with('user')
            ->orderBy('created_at', 'desc');

        // فیلتر سرویس
        if ($this->filterService !== 'all') {
            $query->where('service', $this->filterService);
        }

        // فیلتر نوع Issue
        if ($this->filterIssueType !== 'all') {
            $query->where('issue_type', $this->filterIssueType);
        }

        // فیلتر شدت
        if ($this->filterSeverity !== 'all') {
            $query->where('severity', $this->filterSeverity);
        }

        // فیلتر وضعیت
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        } else {
            // اگر 'all' باشد، همه وضعیت‌ها را نمایش بده
            $query->whereIn('status', ['open', 'resolved', 'ignored']);
        }

        // فیلتر سرور
        if ($this->filterServerId !== 'all') {
            $query->where('server_id', $this->filterServerId);
        }

        // فیلتر جستجو (بر اساس username)
        if (!empty($this->filterSearch)) {
            $query->where('username', 'like', '%' . $this->filterSearch . '%');
        }

        $healthIssues = $query->paginate($this->issuePerPage);

        // لیست سرورها برای فیلتر
        $servers = Nas::where('is_enabled', 1)->pluck('name', 'id');

        return view('livewire.admin.settings.system-maintenance', [
            'expiredUsers' => $expiredUsers,
            'healthIssues' => $healthIssues,
            'servers' => $servers,
        ]);
    }

    public function cleanupOrphanQueuesByServer()
    {
        $checker = new \App\Services\System\Checkers\WireGuardIntegrityChecker();
        $orphansByServer = $checker->getOrphansGroupedByServer();

        if (empty($orphansByServer)) {
            session()->flash('maintenance_message', 'هیچ اورفانی برای پاکسازی وجود ندارد.');
            return;
        }

        $totalDeleted = 0;
        $allErrors = [];
        $allUsernames = [];

        foreach ($orphansByServer as $serverId => $usernames) {
            $server = Nas::find($serverId);
            if (!$server) {
                $allErrors[] = "سرور با ID {$serverId} یافت نشد.";
                continue;
            }

            try {
                $wgService = new \App\Services\WireguardService($server);
                $result = $wgService->cleanupOrphanQueuesByIssues($usernames);

                if ($result['status']) {
                    $totalDeleted += $result['deleted'];
                    $allUsernames = array_merge($allUsernames, $usernames);
                    if (!empty($result['errors'])) {
                        $allErrors = array_merge($allErrors, $result['errors']);
                    }
                } else {
                    $allErrors[] = "خطا در سرور {$server->name}: " . ($result['message'] ?? 'نامشخص');
                }
            } catch (\Exception $e) {
                $allErrors[] = "خطا در سرور {$server->name}: " . $e->getMessage();
            }
        }

        // بستن Issues مربوطه
        if ($totalDeleted > 0) {
            SystemHealthIssue::whereIn('issue_type', ['orphan_peer_only', 'orphan_full', 'orphan_peer_config', 'config_without_account'])
                ->whereIn('username', $allUsernames)
                ->where('status', 'open')
                ->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                    'resolved_by' => Auth::id()
                ]);
        }

        if (empty($allErrors)) {
            session()->flash('maintenance_message', "{$totalDeleted} Queue اورفان از تمام سرورها پاکسازی شد.");
        } else {
            $errorMsg = implode(' | ', array_slice($allErrors, 0, 5));
            session()->flash('maintenance_error', "{$totalDeleted} Queue حذف شد. خطاها: {$errorMsg}");
        }
    }

}
