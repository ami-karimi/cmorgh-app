<?php

namespace App\Livewire\Customer;

use App\Models\AgentBankAccount;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\StoreOrder;
use App\Models\Financial;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\ServiceStatus;
use Illuminate\Support\Str;
use App\Models\Accounts;
use App\Models\Group;
use App\Models\WireGuardUsers;
use App\Models\Tutorial;
use App\Models\Nas;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Dashboard extends Component
{
    use WithFileUploads;

    // متغیرهای مودال شارژ کیف پول
    public $isRechargeModalOpen = false;
    public $amount;
    public $receipt;
    public $description;
    public $tempReceiptPreview = null;

    // متغیرهای مودال تمدید سرویس
    public $isAccRechargeModalOpen = false;
    public $selectedAccountId;
    public $selectedGroupId;

    // متغیرهای مودال تغییر رمز
    public $isChangePasswordModalOpen = false;
    public $changingPasswordAccountId = null;
    public $newPassword = '';

    // متغیرهای مودال آموزش
    public $showTutorialModal = false;
    public $selectedAccount = null;
    public $serverAddress = '';
    public $accountTutorials = [];

    // متغیرهای Toast
    public $toastMessage = null;
    public $toastType = 'success';

    protected $listeners = ['refreshDashboard' => 'refresh'];

    public function refresh()
    {
        // برای به‌روزرسانی صفحه بعد از عملیات‌ها
    }

    /**
     * باز کردن مودال آموزش
     */
    public function openTutorialModal($accountId)
    {
        $this->selectedAccount = Accounts::with('group')->findOrFail($accountId);

        // ۱. دریافت آدرس سرور ست‌شده (بر اساس Nas یا گروه)
        $this->serverAddress = '';
        if ($this->selectedAccount->service_group === 'wireguard' && $this->selectedAccount->wg_server_id) {
            $server = Nas::find($this->selectedAccount->wg_server_id);
            $this->serverAddress = $server->address ?? $server->ip ?? '';
        } else {
            // برای سایر پروتکل‌ها از گروه یا فیلد اختصاصی آدرس خوانده می‌شود
            $group = Group::find($this->selectedAccount->group_id);
            $this->serverAddress = $group->server_address ?? $this->selectedAccount->server_ip ?? '';
        }

        // ۲. دریافت آموزش‌های مرتبط با این پروتکل/پلتفرم از جدول tutorials
        $protocolName = match ($this->selectedAccount->service_group) {
            'wireguard'  => 'WireGuard',
            'l2tp_cisco' => 'L2TP',
            'openvpn'    => 'OpenVPN',
            'v2ray'      => 'V2Ray',
            default      => $this->selectedAccount->service_group,
        };

        $this->accountTutorials = Tutorial::where('is_published', 1)
            ->where(function ($q) use ($protocolName) {
                $q->where('protocol', 'like', '%' . $protocolName . '%')
                    ->orWhereNull('protocol');
            })
            ->orderBy('id', 'asc')
            ->get();

        $this->showTutorialModal = true;
    }

    /**
     * باز کردن مودال تغییر رمز
     */
    public function openChangePasswordModal($accountId)
    {
        $this->changingPasswordAccountId = $accountId;
        $this->newPassword = Str::random(8);
        $this->isChangePasswordModalOpen = true;
    }

    /**
     * تولید رمز جدید
     */
    public function generatePassword()
    {
        $this->newPassword = Str::random(8);
    }

    /**
     * تغییر رمز عبور
     */
    public function changePassword()
    {
        $this->validate([
            'newPassword' => 'required|min:4|max:32'
        ], [
            'newPassword.required' => 'وارد کردن رمز عبور الزامی است.',
            'newPassword.min' => 'رمز عبور باید حداقل ۴ کاراکتر باشد.'
        ]);

        $account = Accounts::find($this->changingPasswordAccountId);

        if ($account) {
            $account->update([
                'password' => $this->newPassword
            ]);
            $this->showToast('رمز عبور اکانت با موفقیت تغییر کرد.', 'success');
        }

        $this->isChangePasswordModalOpen = false;
        $this->reset(['newPassword', 'changingPasswordAccountId']);
    }

    /**
     * باز کردن مودال تمدید سرویس
     */
    public function openAccRechargeModal($accountId)
    {
        $this->selectedAccountId = $accountId;

        $account = Accounts::find($accountId);
        if ($account) {
            $this->selectedGroupId = $account->group_id;
        }

        $this->resetValidation();
        $this->isAccRechargeModalOpen = true;
    }

    /**
     * تایید تمدید سرویس
     */
    public function confirmAccRecharge()
    {
        $this->validate([
            'selectedGroupId' => 'required|exists:groups,id',
            'selectedAccountId' => 'required|exists:accounts,id',
        ]);

        $account = Accounts::findOrFail($this->selectedAccountId);
        $group = Group::findOrFail($this->selectedGroupId);

        $result = \App\Services\VpnManagerService::rechargeAccount(
            $account,
            $group,
            true,
            auth()->id(),
            true
        );

        if (!$result['status']) {
            $this->addError('wallet', $result['message']);
            return;
        }

        $this->isAccRechargeModalOpen = false;
        $this->showToast('اکانت شما با موفقیت تمدید شد.', 'success');
        $this->dispatch('refreshDashboard');
    }

    /**
     * ثبت درخواست افزایش موجودی
     */
    public function requestRecharge()
    {
        $this->validate([
            'amount'      => 'required|numeric|min:5000',
            'receipt'     => 'required|image|max:2048',
            'description' => 'nullable|string|max:255',
        ], [
            'amount.required'  => 'وارد کردن مبلغ الزامی است.',
            'amount.min'       => 'مبلغ شارژ نمی‌تواند کمتر از ۵,۰۰۰ تومان باشد.',
            'receipt.required' => 'لطفاً تصویر فیش واریزی را آپلود کنید.',
            'receipt.image'    => 'فایل آپلودی باید یک تصویر (png, jpg, ...) باشد.',
            'receipt.max'      => 'حجم تصویر نباید بیشتر از ۲ مگابایت باشد.',
        ]);

        $path = $this->receipt->store('attachments/financial', 'public');

        Financial::create([
            'creator'     => Auth::id(),
            'for'         => Auth::id(),
            'type'        => 'plus',
            'price'       => $this->amount,
            'description' => 'درخواست شارژ توسط مشتری ' . ($this->description ? '- ' . $this->description : ''),
            'attachment'  => $path,
            'approved'    => 0,
        ]);

        $this->reset(['isRechargeModalOpen', 'amount', 'receipt', 'description', 'tempReceiptPreview']);

        $this->showToast('درخواست افزایش موجودی با موفقیت ثبت شد و پس از تایید نماینده اعمال خواهد شد.', 'success');
    }

    /**
     * نمایش Toast
     */
    public function showToast($message, $type = 'success')
    {
        $this->toastMessage = $message;
        $this->toastType = $type;
        $this->dispatch('show-toast');
    }

    /**
     * آماده‌سازی داده‌های حساب‌ها
     */
    private function prepareAccountsData($accounts, $wireguardConfigs)
    {
        return $accounts->map(function ($acc) use ($wireguardConfigs) {
            $totalUsageBytes = $acc->usage ?? (($acc->download_usage ?? 0) + ($acc->upload_usage ?? 0));
            $maxGb = $acc->max_usage > 0 ? round($acc->max_usage / 1073741824, 2) : 0;
            $usedGb = round($totalUsageBytes / 1073741824, 2);
            $remGb = $maxGb > 0 ? max(0, round($maxGb - $usedGb, 2)) : null;
            $percent = $maxGb > 0 ? min(100, round(($usedGb / $maxGb) * 100)) : 0;

            $daysLeft = null;
            $isExpired = false;
            if ($acc->expire_date) {
                $expireCarbon = Carbon::parse($acc->expire_date);
                $daysLeft = (int) now()->diffInDays($expireCarbon, false);
                if ($expireCarbon->isPast()) {
                    $isExpired = true;
                }
            }

            $isLowVolume = $maxGb > 0 && ($percent >= 85 || ($remGb !== null && $remGb <= 1 && $maxGb > 1));
            $isLowDays = $daysLeft !== null && $daysLeft <= 4 && !$isExpired;
            $needsRecharge = $isLowVolume || $isLowDays || $isExpired || !$acc->is_enabled;

            $progressColor = $percent >= 90 ? 'bg-rose-500' : ($percent >= 75 ? 'bg-amber-500' : 'bg-emerald-500');
            $isWG = $acc->service_group === 'wireguard';

            // پیکربندی WireGuard
            $wgConfig = null;
            if ($isWG) {
                $wgConfig = $wireguardConfigs->get($acc->id);
            }

            // سرعت
            $speedLimit = $acc->mikrotik_speed;
            if (empty($speedLimit) && $acc->group_id) {
                $accGroup = Group::find($acc->group_id);
                $speedLimit = $accGroup ? $accGroup->mikrotik_speed : null;
            }

            return [
                'account' => $acc,
                'used_gb' => $usedGb,
                'max_gb' => $maxGb,
                'rem_gb' => $remGb,
                'percent' => $percent,
                'days_left' => $daysLeft,
                'is_expired' => $isExpired,
                'is_low_volume' => $isLowVolume,
                'is_low_days' => $isLowDays,
                'needs_recharge' => $needsRecharge,
                'progress_color' => $progressColor,
                'is_wg' => $isWG,
                'wireguard_config' => $wgConfig,
                'speed_limit' => $speedLimit,
                'multi_login' => $acc->multi_login ?? 1,
                'online_count' => $acc->online_count ?? 0,
                'is_online' => ($acc->online_count ?? 0) > 0,
                'health' => $this->getServiceHealth($acc, $isExpired, $isLowVolume, $isLowDays, $needsRecharge),
            ];
        });
    }

    /**
     * دریافت وضعیت سلامت سرویس
     */
    private function getServiceHealth($acc, $isExpired, $isLowVolume, $isLowDays, $needsRecharge)
    {
        if ($isExpired || !$acc->is_enabled) {
            return ['status' => 'critical', 'label' => 'نیاز به تمدید', 'icon' => '🔴', 'color' => 'rose'];
        }

        if ($isLowVolume) {
            return ['status' => 'warning', 'label' => 'حجم رو به اتمام', 'icon' => '⚠️', 'color' => 'amber'];
        }

        if ($isLowDays) {
            return ['status' => 'warning', 'label' => 'نزدیک به انقضا', 'icon' => '⚠️', 'color' => 'amber'];
        }

        return ['status' => 'healthy', 'label' => 'سرویس فعال', 'icon' => '✓', 'color' => 'emerald'];
    }

    /**
     * دریافت آمار سریع
     */
    private function getQuickStats($accountsData)
    {
        $total = $accountsData->count();
        $active = $accountsData->filter(function ($item) {
            return !$item['is_expired'] && $item['account']->is_enabled;
        })->count();

        $needsRenew = $accountsData->filter(function ($item) {
            return $item['needs_recharge'];
        })->count();

        $expiringSoon = $accountsData->filter(function ($item) {
            return !$item['is_expired'] && $item['days_left'] !== null && $item['days_left'] <= 4 && $item['days_left'] > 0;
        })->count();

        return [
            'total' => $total,
            'active' => $active,
            'needs_renew' => $needsRenew,
            'expiring_soon' => $expiringSoon,
        ];
    }

    /**
     * دریافت هشدارهای هوشمند
     */
    private function getSmartAlerts($accountsData)
    {
        $alerts = [];

        // ۱. سرویس‌های منقضی شده
        $expired = $accountsData->filter(function ($item) {
            return $item['is_expired'];
        });
        foreach ($expired as $item) {
            $alerts[] = [
                'type' => 'expired',
                'title' => 'سرویس منقضی شده',
                'description' => "سرویس {$item['account']->username} به پایان رسیده است.",
                'account_id' => $item['account']->id,
                'action' => 'renew',
                'icon' => '🔴',
                'color' => 'rose',
            ];
        }

        // ۲. سرویس‌های نزدیک به انقضا
        $expiring = $accountsData->filter(function ($item) {
            return !$item['is_expired'] && $item['days_left'] !== null && $item['days_left'] <= 4 && $item['days_left'] > 0;
        });
        foreach ($expiring as $item) {
            $alerts[] = [
                'type' => 'expiring',
                'title' => 'نزدیک به انقضا',
                'description' => "سرویس {$item['account']->username} تنها {$item['days_left']} روز دیگر منقضی می‌شود.",
                'account_id' => $item['account']->id,
                'action' => 'renew',
                'icon' => '⚠️',
                'color' => 'amber',
            ];
        }

        // ۳. سرویس‌های با حجم کم
        $lowVolume = $accountsData->filter(function ($item) {
            return !$item['is_expired'] && $item['is_low_volume'] && $item['max_gb'] > 0;
        });
        foreach ($lowVolume as $item) {
            $alerts[] = [
                'type' => 'low_volume',
                'title' => 'حجم رو به اتمام',
                'description' => "سرویس {$item['account']->username} تنها {$item['rem_gb']} GB حجم باقی‌مانده دارد.",
                'account_id' => $item['account']->id,
                'action' => 'renew',
                'icon' => '📉',
                'color' => 'amber',
            ];
        }

        return $alerts;
    }

    private function getBankInfo()
    {
        $user = auth()->user();
        $bankAccount = null;

        if ($user->creator) {
            $bankAccount = AgentBankAccount::where('user_id', $user->creator)->first();
        }

        if (!$bankAccount) {
            $bankAccount = AgentBankAccount::whereHas('user', function($query) {
                $query->where('role', 'manager');
            })
                ->where('is_show', 1)
                ->orderBy('id', 'asc')
                ->first();
        }

        // اگر حسابی پیدا شد، اطلاعات را برگردان، در غیر این صورت مقدار پیش‌فرض
        if ($bankAccount) {
            return [
                'card_number' => $bankAccount->card_number ?? '',
                'owner'       => $bankAccount->account_name ?? '',
                'bank_name'   => $bankAccount->bank_name ?? '',
                'shaba'       => $bankAccount->sheba_number ?? '',
            ];
        }

        // مقدار پیش‌فرض (در صورتی که هیچ حسابی پیدا نشود)
        return [
            'card_number' => '***-****-****-****',
            'owner'       => '****',
            'bank_name'   => '***',
            'shaba'       => '***',
        ];
    }

    public function render()
    {
        $user = Auth::user();

        // دریافت سرویس‌ها با Eager Loading
        $accounts = $user->vpnAccounts()
            ->with('group')
            ->latest()
            ->get();

        // دریافت پیکربندی WireGuard
        $wireguardConfigs = collect();
        if ($accounts->where('service_group', 'wireguard')->isNotEmpty()) {
            $wgUserIds = $accounts->where('service_group', 'wireguard')->pluck('id')->toArray();
            $wireguardConfigs = WireGuardUsers::whereIn('user_id', $wgUserIds)
                ->get()
                ->keyBy('user_id');
        }

        // آماده‌سازی داده‌های حساب‌ها
        $accountsData = $this->prepareAccountsData($accounts, $wireguardConfigs);

        // مرتب‌سازی حساب‌ها بر اساس اولویت
        $sortedAccounts = $accountsData->sortByDesc(function ($item) {
            // اولویت: منقضی > نزدیک انقضا > حجم کم > غیرفعال > سالم
            if ($item['is_expired']) return 5;
            if ($item['is_low_days']) return 4;
            if ($item['is_low_volume']) return 3;
            if (!$item['account']->is_enabled) return 2;
            return 1;
        });

        // دریافت تراکنش‌ها
        $transactions = Financial::where('for', $user->id)
            ->latest()
            ->take(10)
            ->get();

        // دریافت اطلاعیه‌ها
        $announcements = Announcement::where('is_active', 1)
            ->whereIn('target', ['all', 'customers'])
            ->latest()
            ->get();

        // وضعیت شبکه
        $services = ServiceStatus::all();
        $hasOutage = $services->where('status', 'outage')->count() > 0;
        $hasDegraded = $services->where('status', 'degraded')->count() > 0;

        // بسته‌های موجود برای تمدید
        $availableGroups = collect();
        if ($this->isAccRechargeModalOpen && $this->selectedAccountId) {
            $selectedAccount = Accounts::find($this->selectedAccountId);
            if ($selectedAccount) {
                $groupQuery = Group::where('is_enabled', 1);
                if ($selectedAccount->service_group === 'wireguard') {
                    $groupQuery->where(function ($q) {
                        $q->where('name', 'LIKE', '%وایرگارد%')
                            ->orWhere('name', 'LIKE', '%wireguard%')
                            ->orWhere('name', 'LIKE', '%WireGuard%');
                    });
                } elseif ($selectedAccount->service_group === 'v2ray') {
                    $groupQuery->where(function ($q) {
                        $q->where('name', 'LIKE', '%v2ray%')
                            ->orWhere('name', 'LIKE', '%V2ray%');
                    });
                } else {
                    $groupQuery->where('name', 'NOT LIKE', '%وایرگارد%')
                        ->where('name', 'NOT LIKE', '%wireguard%')
                        ->where('name', 'NOT LIKE', '%WireGuard%')
                        ->where('name', 'NOT LIKE', '%v2ray%');
                }
                $availableGroups = $groupQuery->get();
            }
        }

        // آمار سریع
        $quickStats = $this->getQuickStats($accountsData);

        // هشدارهای هوشمند
        $smartAlerts = $this->getSmartAlerts($accountsData);

        // وضعیت کلی برای Summary
        $summaryStatus = 'good';
        $summaryMessage = 'همه سرویس‌های شما در وضعیت مطلوب هستند.';
        if ($quickStats['needs_renew'] > 0) {
            $summaryStatus = 'warning';
            $summaryMessage = 'برخی سرویس‌ها نیاز به تمدید دارند.';
        }
        if ($quickStats['expiring_soon'] > 0) {
            $summaryStatus = 'warning';
            $summaryMessage = 'برخی سرویس‌ها به زودی منقضی می‌شوند.';
        }
        if ($accountsData->where('is_expired', true)->count() > 0) {
            $summaryStatus = 'critical';
            $summaryMessage = 'برخی سرویس‌ها منقضی شده‌اند. لطفاً اقدام کنید.';
        }

        $bankInfo = $this->getBankInfo();


        return view('livewire.customer.dashboard', [
            'accountsData' => $sortedAccounts,
            'transactions' => $transactions,
            'announcements' => $announcements,
            'bankInfo' => $bankInfo,
            'hasOutage' => $hasOutage,
            'hasDegraded' => $hasDegraded,
            'availableGroups' => $availableGroups,
            'balance' => $user->balance,
            'quickStats' => $quickStats,
            'smartAlerts' => $smartAlerts,
            'summaryStatus' => $summaryStatus,
            'summaryMessage' => $summaryMessage,
        ])->layout('layouts.customer');
    }
}
