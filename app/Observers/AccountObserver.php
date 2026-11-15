<?php
namespace App\Observers;
use App\Models\Accounts;
use App\Services\NotificationService;
class AccountObserver
{
    public function created(Accounts $account): void { $this->notify($account, 'اکانت جدید ساخته شد', "اکانت «{$account->username}» با موفقیت ساخته شد.", 'account.created'); }
    public function updated(Accounts $account): void
    {
        if ($account->wasChanged('is_enabled')) {
            $status = $account->is_enabled ? 'فعال' : 'غیرفعال';
            $this->notify($account, "اکانت {$status} شد", "اکانت «{$account->username}» {$status} شد.", 'account.status');
        }
        elseif ($account->wasChanged(['expire_date', 'usage', 'max_usage'])) $this->notify($account, 'اطلاعات اکانت به‌روزرسانی شد', "اطلاعات اکانت «{$account->username}» به‌روزرسانی شد.", 'account.updated');
    }
    private function notify(Accounts $account, string $title, string $message, string $type): void
    {
        $owner = $account->users()->first();
        NotificationService::admins($title, $message, $type, route('admin.accounts.show', ['id' => $account->id]), ['account_id' => $account->id]);
        NotificationService::send($owner, $title, $message, $type, $owner?->role === 'customer' ? route('customer.dashboard') : route('reseller.accounts.index'), ['account_id' => $account->id]);
        NotificationService::send($account->creatorUser, $title, $message, $type, route('reseller.accounts.show', ['id' => $account->id]), ['account_id' => $account->id]);
    }
}
