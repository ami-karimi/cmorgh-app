<?php
namespace App\Observers;
use App\Models\Accounts;
use App\Services\NotificationService;
class AccountObserver
{
    public function created(Accounts $account): void { $this->notify($account, 'اکانت جدید ساخته شد', "اکانت «{$account->username}» با موفقیت ساخته شد.", 'account.created'); }
    public function updated(Accounts $account): void
    {
        // فقط اعلان‌های مشخص‌شده برای چرخه عمر اکانت ارسال می‌شوند.
        $expired = $account->expire_date && now()->greaterThanOrEqualTo($account->expire_date);
        $expirationChanged = $account->wasChanged(['expire_date', 'expired', 'is_enabled']);

        if ($account->wasChanged('is_enabled') && !($expired && !$account->is_enabled)) {
            $status = $account->is_enabled ? 'فعال' : 'غیرفعال';
            $this->notify($account, "اکانت {$status} شد", "اکانت «{$account->username}» {$status} شد.", 'account.status');
        }

        if ($account->wasChanged('username')) {
            $this->notify($account, 'نام کاربری تغییر کرد', "نام کاربری اکانت به «{$account->username}» تغییر کرد.", 'account.username');
        }

        if ($account->wasChanged('password')) {
            $this->notify($account, 'کلمه عبور تغییر کرد', "کلمه عبور اکانت «{$account->username}» تغییر کرد.", 'account.password');
        }

        $usageFinished = (int) ($account->max_usage ?? 0) > 0
            && (int) ($account->usage ?? 0) >= (int) $account->max_usage;
        if ($usageFinished && ($account->wasChanged(['usage', 'max_usage']) || $account->wasRecentlyCreated)) {
            $this->notify($account, 'حجم اکانت تمام شد', "حجم مصرفی اکانت «{$account->username}» به پایان رسید.", 'account.volume_finished');
        }

        if ($expired && $expirationChanged) {
            $this->notify($account, 'اعتبار اکانت تمام شد', "تاریخ اعتبار اکانت «{$account->username}» به پایان رسید.", 'account.expired');
        }
    }
    private function notify(Accounts $account, string $title, string $message, string $type): void
    {
        $owner = $account->users()->first();
        NotificationService::admins($title, $message, $type, route('admin.accounts.show', ['id' => $account->id]), ['account_id' => $account->id]);
        NotificationService::send($owner, $title, $message, $type, $owner?->role === 'customer' ? route('customer.dashboard') : route('reseller.accounts.index'), ['account_id' => $account->id]);
        NotificationService::send($account->creatorUser, $title, $message, $type, route('reseller.accounts.show', ['id' => $account->id]), ['account_id' => $account->id]);
    }
}
