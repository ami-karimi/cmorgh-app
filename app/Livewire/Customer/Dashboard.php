<?php

namespace App\Livewire\Customer;

    use Livewire\Component;
    use Livewire\WithFileUploads;
    use App\Models\StoreOrder;
    use App\Models\Financial;
    use Illuminate\Support\Facades\Auth;
    use App\Models\Announcement;
    use App\Models\ServiceStatus;
    use Illuminate\Support\Str;
    use App\Models\Accounts;
class Dashboard extends Component
{
    use WithFileUploads;

    // متغیرهای مودال شارژ کیف پول
    public $isRechargeModalOpen = false;
    public $amount;
    public $receipt;
    public $description;

    public $isAccRechargeModalOpen = false;
    public $selectedAccountId;
    public $selectedGroupId;

    public $isChangePasswordModalOpen = false;
    public $changingPasswordAccountId = null;
    public $newPassword = '';

    public $showTutorialModal = false;
    public $selectedAccount = null;
    public $serverAddress = '';
    public $accountTutorials = [];

    public function openTutorialModal($accountId)
    {
        $this->selectedAccount = \App\Models\Accounts::findOrFail($accountId);

        // ۱. دریافت آدرس سرور ست‌شده (بر اساس Nas یا گروه)
        $this->serverAddress = '';
        if ($this->selectedAccount->service_group === 'wireguard' && $this->selectedAccount->wg_server_id) {
            $server = \App\Models\Nas::find($this->selectedAccount->wg_server_id);
            $this->serverAddress = $server->address ?? $server->ip ?? '';
        } else {
            // برای سایر پروتکل‌ها از گروه یا فیلد اختصاصی آدرس خوانده می‌شود
            $group = \App\Models\Group::find($this->selectedAccount->group_id);
            $this->serverAddress = $group->server_address ?? $this->selectedAccount->server_ip ?? '';
        }

        // ۲. دریافت آموزش‌های مرتبط با این پروتکل/پلتفرم از جدول tutorials
        $protocolName = match($this->selectedAccount->service_group) {
            'wireguard'  => 'WireGuard',
            'l2tp_cisco' => 'L2TP',
            'openvpn'    => 'OpenVPN',
            default      => $this->selectedAccount->service_group,
        };

        $this->accountTutorials = \App\Models\Tutorial::where('is_published', 1)
            ->where(function($q) use ($protocolName) {
                $q->where('protocol', 'like', '%' . $protocolName . '%')
                    ->orWhereNull('protocol');
            })
            ->get();

        $this->showTutorialModal = true;
    }

    public function openChangePasswordModal($accountId)
    {
        $this->changingPasswordAccountId = $accountId;
        $this->newPassword = Str::random(8);
        $this->isChangePasswordModalOpen = true;
    }
    public function generatePassword()
    {
        $this->newPassword = Str::random(8);
    }

    public function changePassword()
    {
        $this->validate([
            'newPassword' => 'required|min:4|max:32'
        ], [
            'newPassword.required' => 'وارد کردن رمز عبور الزامی است.',
            'newPassword.min' => 'رمز عبور باید حداقل ۴ کاراکتر باشد.'
        ]);

        $account = Accounts::where('id', $this->changingPasswordAccountId)->first();

        if ($account) {
            $account->update([
                'password' => $this->newPassword
            ]);
            session()->flash('success', 'رمز عبور اکانت با موفقیت تغییر کرد.');
        }

        $this->isChangePasswordModalOpen = false;
        $this->reset(['newPassword', 'changingPasswordAccountId']);
    }



    public function openAccRechargeModal($accountId)
    {
        $this->selectedAccountId = $accountId;

        // پیدا کردن اکانت و ست کردن گروه پیش‌فرض آن
        $account = \App\Models\Accounts::find($accountId);
        if ($account) {
            $this->selectedGroupId = $account->group_id;
        }

        $this->resetValidation();
        $this->isAccRechargeModalOpen = true;
    }

    public function confirmAccRecharge()
    {
        $this->validate([
            'selectedGroupId' => 'required|exists:groups,id',
            'selectedAccountId' => 'required|exists:accounts,id',
        ]);

        $account = \App\Models\Accounts::findOrFail($this->selectedAccountId);
        $group = \App\Models\Group::findOrFail($this->selectedGroupId);

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
        session()->flash('message', 'اکانت شما با موفقیت تمدید شد.');
    }

    // متد ثبت درخواست افزایش موجودی
    public function requestRecharge()
    {
        $this->validate([
            'amount'      => 'required|numeric|min:5000', // حداقل ۵ هزار تومان
            'receipt'     => 'required|image|max:2048',   // حداکثر ۲ مگابایت
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

        $this->reset(['isRechargeModalOpen', 'amount', 'receipt', 'description']);

        session()->flash('success_recharge', 'درخواست افزایش موجودی با موفقیت ثبت شد و پس از تایید نماینده اعمال خواهد شد.');
    }

    public function render()
    {
        $user = Auth::user();

        $orders = StoreOrder::where('user_id', $user->id)
            ->with('group')
            ->latest()
            ->take(5)
            ->get();

        $accounts = $user->vpnAccounts()->with('group')->latest()->get();

        $transactions = Financial::where('for', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $announcements = Announcement::where('is_active', 1)
            ->whereIn('target', ['all', 'customers'])
            ->latest()
            ->get();

        $services = ServiceStatus::all();
        $hasOutage = $services->where('status', 'outage')->count() > 0;
        $hasDegraded = $services->where('status', 'degraded')->count() > 0;


        $availableGroups = collect();

        if ($this->isAccRechargeModalOpen && $this->selectedAccountId) {
            $selectedAccount = \App\Models\Accounts::find($this->selectedAccountId);

            if ($selectedAccount) {
                $groupQuery = \App\Models\Group::where('is_enabled', 1);

                if ($selectedAccount->service_group === 'wireguard') {
                    $groupQuery->where(function($q) {
                        $q->where('name', 'LIKE', '%وایرگارد%')
                            ->orWhere('name', 'LIKE', '%wireguard%')
                            ->orWhere('name', 'LIKE', '%WireGuard%');
                    });

                } elseif ($selectedAccount->service_group === 'v2ray') {
                    $groupQuery->where(function($q) {
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

        return view('livewire.customer.dashboard', [
            'orders' => $orders,
            'accounts' => $accounts,
            'transactions' => $transactions,
            'announcements' => $announcements,
            'hasOutage' => $hasOutage,
            'availableGroups' => $availableGroups,
            'hasDegraded' => $hasDegraded,
            'services' => $services,
            'balance' => $user->balance,
        ])->layout('layouts.customer');
    }
}
