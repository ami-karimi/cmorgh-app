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

    // فیلترهای پیشرفته
    public $statusFilter = 'all';
    public $expireFilter = 'all';
    public $onlineFilter = 'all';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingExpireFilter() { $this->resetPage(); }
    public function updatingOnlineFilter() { $this->resetPage(); }

    public function toggleStatus($accountId)
    {
        // امن نگه داشتن تغییر وضعیت فقط برای زیرمجموعه‌های خودش
        $account = Accounts::findOrFail($accountId);
        $account->is_enabled = !$account->is_enabled;
        $account->save();
        session()->flash('success', 'وضعیت اکانت ' . $account->username . ' تغییر کرد.');
    }

    public function render()
    {
        $agent = Auth::user();

        // 🧠 پیدا کردن تمام زیرمجموعه‌های این نماینده (شبکه آبشاری کامل)
        // ۱. مشتریان و زیرنمایندگان مستقیم
        $directIds = User::where('creator', $agent->id)->pluck('id')->toArray();
        // ۲. مشتریانِ متعلق به زیرنمایندگان
        $subCustomerIds = User::whereIn('creator', $directIds)->pluck('id')->toArray();
        // ترکیب همه آیدی‌های مجاز
        $allowedIds = array_merge([$agent->id], $directIds, $subCustomerIds);

        // 👈 فراخوانی اکانت‌ها به همراه روابط تودرتو (گروه + مشتری + سازنده مشتری)
        $query = Accounts::whereIn('creator', $allowedIds)->with(['group', 'panelUser.parentAgent'])->latest();

        // 🔍 فیلتر جستجو
        if (!empty($this->search)) {
            $query->where('username', 'like', '%' . $this->search . '%');
        }

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
            $query->whereNotNull('expire_date')->whereBetween('expire_date', [$now, clone $now->addDays(5)]);
        } elseif ($this->expireFilter === 'expired_week_ago') {
            $query->whereNotNull('expire_date')->where('expire_date', '<', clone $now->subDays(7));
        }

        // 🟢 فیلتر آنلاین/آفلاین
        if ($this->onlineFilter === 'online') {
            $query->where('is_online', 1);
        } elseif ($this->onlineFilter === 'offline') {
            $query->where('is_online', 0);
        }

        $accounts = $query->paginate(15);

        return view('livewire.agent.account-manager', [
            'accounts' => $accounts
        ]);
    }
}
