<?php
namespace App\Observers;
use App\Models\User;
use App\Services\NotificationService;
class UserObserver
{
    public function created(User $user): void
    {
        if (in_array($user->role, ['admin', 'manager'], true)) return;
        $role = $user->role === 'customer' ? 'مشتری' : 'نماینده';
        NotificationService::admins('کاربر جدید ساخته شد', "{$role} «{$user->name}" . ($user->username ? " / {$user->username}" : '') . ' ثبت شد.', 'user.created', $user->role === 'customer' ? route('admin.users.index') : route('admin.managers.list'), ['user_id' => $user->id]);
        if ($user->role === 'agent' || $user->role === 'sub_agent') NotificationService::send($user, 'حساب نمایندگی شما ایجاد شد', 'حساب نمایندگی شما با موفقیت ایجاد شد.', 'user.created', route('reseller.dashboard'));
    }
}
