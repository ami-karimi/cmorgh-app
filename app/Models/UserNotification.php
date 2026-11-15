<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class UserNotification extends Model
{
    protected $table = 'user_notifications';
    protected $guarded = ['id'];
    protected $casts = ['data' => 'array', 'read_at' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function reads() { return $this->hasMany(UserNotificationRead::class, 'notification_id'); }
    public function scopeVisibleTo($query, ?User $user)
    {
        if (!$user) return $query->whereRaw('1 = 0');
        $audience = match ($user->role) {
            'admin', 'manager' => 'admin',
            'agent', 'sub_agent' => 'agents',
            default => 'customers',
        };
        return $query->where(function ($q) use ($user, $audience) {
            $q->where('user_id', $user->id)->orWhereIn('audience', ['all', $audience]);
        });
    }
}
