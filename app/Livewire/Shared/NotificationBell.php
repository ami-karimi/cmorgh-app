<?php
namespace App\Livewire\Shared;
use App\Models\UserNotification;
use App\Models\UserNotificationRead;
use Livewire\Component;
class NotificationBell extends Component
{
    public bool $open = false;
    public function markRead(int $id): void
    {
        $notification = UserNotification::visibleTo(auth()->user())->whereKey($id)->first();
        if (!$notification) return;
        if ($notification->user_id) $notification->update(['read_at' => now()]);
        else UserNotificationRead::updateOrCreate(['notification_id' => $notification->id, 'user_id' => auth()->id()], ['read_at' => now()]);
    }
    public function markAllRead(): void
    {
        $notifications = UserNotification::visibleTo(auth()->user())->latest('id')->limit(100)->get();
        foreach ($notifications as $notification) $this->markRead($notification->id);
    }
    public function render()
    {
        $query = UserNotification::visibleTo(auth()->user());
        $notifications = (clone $query)->latest('id')->limit(12)->get();
        $groupReads = UserNotificationRead::where('user_id', auth()->id())->whereIn('notification_id', $notifications->pluck('id'))->pluck('read_at', 'notification_id');
        $notifications->each(function ($notification) use ($groupReads) {
            $notification->viewer_read_at = $notification->user_id ? $notification->read_at : $groupReads->get($notification->id);
        });
        return view('livewire.shared.notification-bell', [
            'notifications' => $notifications,
            'unreadCount' => UserNotification::visibleTo(auth()->user())->where(function ($q) {
                $q->where(function ($direct) { $direct->whereNotNull('user_id')->whereNull('read_at'); })
                  ->orWhere(function ($group) { $group->whereNull('user_id')->whereNotExists(function ($read) { $read->selectRaw('1')->from('user_notification_reads')->whereColumn('user_notification_reads.notification_id', 'user_notifications.id')->where('user_notification_reads.user_id', auth()->id()); }); });
            })->count(),
        ]);
    }
}
