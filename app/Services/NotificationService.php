<?php
namespace App\Services;
use App\Models\User;
use App\Models\UserNotification;
class NotificationService
{
    public static function send(?User $user, string $title, string $message, string $type = 'system', ?string $url = null, array $data = []): ?UserNotification
    {
        if (!$user) return null;
        return UserNotification::create(['user_id' => $user->id, 'type' => $type, 'title' => $title, 'message' => $message, 'action_url' => $url, 'data' => $data]);
    }
    public static function audience(string $audience, string $title, string $message, string $type = 'announcement', ?string $url = null, array $data = []): UserNotification
    {
        return UserNotification::create(['audience' => $audience, 'type' => $type, 'title' => $title, 'message' => $message, 'action_url' => $url, 'data' => $data]);
    }
    public static function admins(string $title, string $message, string $type = 'system', ?string $url = null, array $data = []): void
    {
        self::audience('admin', $title, $message, $type, $url, $data);
    }
    public static function event(?User $user, string $title, string $message, string $type = 'system', ?string $url = null, array $data = []): void
    {
        self::send($user, $title, $message, $type, $url, $data);
        self::admins($title, $message, $type, $url, $data);
    }
}
