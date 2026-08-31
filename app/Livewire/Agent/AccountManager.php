<?php

namespace App\Livewire\Agent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Accounts;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Title('مدیریت اکانت ها | همراه سیمرغ')]
#[Layout('layouts.agent')]
class AccountManager extends Component
{
    use WithPagination;

    public $search = '';

    // متغیرهای عملیات گروهی
    public $selectedAccounts = [];
    public $selectAll = false;

    // فیلترهای پیشرفته
    public $statusFilter = 'all';
    public $expireFilter = 'all';
    public $onlineFilter = 'all';
    public $quickFilter = 'all';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingExpireFilter() { $this->resetPage(); }
    public function updatingOnlineFilter() { $this->resetPage(); }
    public function updatingQuickFilter() { $this->resetPage(); }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->expireFilter = 'all';
        $this->onlineFilter = 'all';
        $this->quickFilter = 'all';
        $this->resetPage();
    }

    public function toggleStatus($accountId)
    {
        // امن نگه داشتن تغییر وضعیت فقط برای زیرمجموعه‌های خودش
        $account = Accounts::findOrFail($accountId);
        $account->is_enabled = !$account->is_enabled;
        $account->save();
        session()->flash('success', 'وضعیت اکانت ' . $account->username . ' تغییر کرد.');
    }

    // --- توابع مربوط به عملیات گروهی ---
    public function updatedSelectAll($value)
    {
        if ($value) {
            // برای سادگی، آیدی اکانت‌های صفحه فعلی یا کل جستجو را انتخاب می‌کنیم.
            // اینجا آیدی‌های کوئری فعلی را می‌گیریم (تا سقف 100 تا برای جلوگیری از مشکل پرفورمنس)
            $this->selectedAccounts = $this->buildQuery()->take(100)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedAccounts = [];
        }
    }

    public function bulkEnable()
    {
        if (count($this->selectedAccounts) > 0) {
            Accounts::whereIn('id', $this->selectedAccounts)->update(['is_enabled' => 1]);
            $this->selectedAccounts = [];
            $this->selectAll = false;
            session()->flash('success', 'اکانت‌های انتخاب شده با موفقیت فعال شدند.');
        }
    }

    public function bulkDisable()
    {
        if (count($this->selectedAccounts) > 0) {
            Accounts::whereIn('id', $this->selectedAccounts)->update(['is_enabled' => 0]);
            $this->selectedAccounts = [];
            $this->selectAll = false;
            session()->flash('success', 'اکانت‌های انتخاب شده با موفقیت مسدود شدند.');
        }
    }
    // -------------------------------------

    private function getAllowedIds()
    {
        $agent = Auth::user();
        $directIds = User::where('creator', $agent->id)->pluck('id')->toArray();
        $subCustomerIds = User::whereIn('creator', $directIds)->pluck('id')->toArray();
        return array_merge([$agent->id], $directIds, $subCustomerIds);
    }

    private function buildQuery()
    {
        $query = Accounts::whereIn('creator', $this->getAllowedIds())->with(['group', 'panelUser.parentAgent'])->latest();

        // 🔍 جستجو
        if (!empty($this->search)) {
            $query->where('username', 'like', '%' . $this->search . '%');
        }

        // 🎯 اعمال Quick Filter
        $this->applyQuickFilter($query);

        // 🎛 فیلتر وضعیت فعال/غیرفعال
        if ($this->statusFilter === 'active') {
            $query->where('is_enabled', 1);
        } elseif ($this->statusFilter === 'disabled') {
            $query->where('is_enabled', 0);
        }

        // ⏳ فیلتر تاریخ انقضا
        $now = now();
        if ($this->expireFilter === 'expired') {
            $query->whereNotNull('expire_date')->where('expire_date', '<', $now);
        } elseif ($this->expireFilter === 'expiring_5_days') {
            $query->whereNotNull('expire_date')->whereBetween('expire_date', [$now, (clone $now)->addDays(5)]);
        } elseif ($this->expireFilter === 'expired_week_ago') {
            $query->whereNotNull('expire_date')->where('expire_date', '<', (clone $now)->subDays(7));
        }

        // 🟢 فیلتر آنلاین/آفلاین
        if ($this->onlineFilter === 'online') {
            $query->where('is_online', 1);
        } elseif ($this->onlineFilter === 'offline') {
            $query->where('is_online', 0);
        }

        return $query;
    }

    public function render()
    {
        $allowedIds = $this->getAllowedIds();
        $accounts = $this->buildQuery()->paginate(15);

        // ✅ محاسبه آمار برای KPIها
        $totalAccounts = Accounts::whereIn('creator', $allowedIds)->count();
        $activeAccounts = Accounts::whereIn('creator', $allowedIds)->where('is_enabled', 1)->count();
        $onlineAccounts = Accounts::whereIn('creator', $allowedIds)->where('is_online', 1)->count();
        $expiringAccountsCount = Accounts::whereIn('creator', $allowedIds)
            ->whereNotNull('expire_date')
            ->whereBetween('expire_date', [now(), (clone now())->addDays(7)])
            ->count();
        $expiredAccountsCount = Accounts::whereIn('creator', $allowedIds)
            ->whereNotNull('expire_date')
            ->where('expire_date', '<', now())
            ->count();
        $disabledAccounts = Accounts::whereIn('creator', $allowedIds)->where('is_enabled', 0)->count();

        return view('livewire.agent.account-manager', [
            'accounts' => $accounts,
            'totalAccounts' => $totalAccounts,
            'activeAccounts' => $activeAccounts,
            'onlineAccounts' => $onlineAccounts,
            'expiringAccountsCount' => $expiringAccountsCount,
            'expiredAccountsCount' => $expiredAccountsCount,
            'disabledAccounts' => $disabledAccounts,
        ]);
    }

    private function applyQuickFilter($query)
    {
        switch ($this->quickFilter) {
            case 'active':
                $query->where('is_enabled', 1);
                break;
            case 'online':
                $query->where('is_online', 1);
                break;
            case 'expiring':
                $query->whereNotNull('expire_date')
                    ->whereBetween('expire_date', [now(), (clone now())->addDays(7)]);
                break;
            case 'expired':
                $query->whereNotNull('expire_date')->where('expire_date', '<', now());
                break;
            case 'disabled':
                $query->where('is_enabled', 0);
                break;
            case 'all':
            default:
                break;
        }
    }
}
