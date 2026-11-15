<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserNotificationRead extends Model
{
    public $timestamps = false;
    protected $table = 'user_notification_reads';
    protected $guarded = ['id'];
    protected $casts = ['read_at' => 'datetime'];
}
