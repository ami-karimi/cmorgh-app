<?php
namespace App\Observers;
use App\Models\Financial;
use App\Services\NotificationService;
class FinancialObserver
{
    public function created(Financial $financial): void { $this->notify($financial, 'ثبت تراکنش مالی', 'یک تراکنش مالی جدید ثبت شد.'); }
    public function updated(Financial $financial): void
    {
        if ($financial->wasChanged('approved') || $financial->wasChanged('type')) $this->notify($financial, $financial->approved ? 'تراکنش تأیید شد' : 'وضعیت تراکنش تغییر کرد', $financial->approved ? 'تراکنش شما تأیید و در موجودی اعمال شد.' : 'وضعیت تراکنش شما تغییر کرد.');
    }
    private function notify(Financial $financial, string $adminTitle, string $userMessage): void
    {
        $user = $financial->forUser;
        NotificationService::admins($adminTitle, "مبلغ {$financial->price} برای کاربر #{$financial->for} ثبت/به‌روزرسانی شد.", 'financial', route('admin.financial'), ['financial_id' => $financial->id]);
        NotificationService::send($user, $financial->type === 'plus' ? 'شارژ کیف پول تأیید شد' : 'برداشت از کیف پول ثبت شد', $userMessage, 'financial', $user?->role === 'customer' ? route('customer.wallet') : route('reseller.financial'), ['financial_id' => $financial->id]);
    }
}
