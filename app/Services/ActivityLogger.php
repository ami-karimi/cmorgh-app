<?php
namespace App\Services;

use App\Models\UserActivity;

class ActivityLogger
{
    /**
     * ثبت یک رخداد جدید در سیستم
     */
    public static function log($userId, $content, $adminView = 0, $agentView = 0)
    {
        UserActivity::create([
            'by' => auth()->check() ? auth()->id() : 0,
            'user_id' => $userId,
            'content' => $content,
            'admin_view' => $adminView,
            'agent_view' => $agentView,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
