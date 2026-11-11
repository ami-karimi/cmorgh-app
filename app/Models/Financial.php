<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;
class Financial extends Model
{
    protected $table = 'financial';
    protected $guarded = ['id'];

    /** Current locking reads, not a potentially stale repeatable-read aggregate snapshot. Caller must hold the user's row lock. */
    public static function lockedBalance(int $userId): int
    {
        return (int) static::where('for',$userId)->where('approved',1)->whereIn('type',['plus','minus'])
            ->lockForUpdate()->get(['type','price'])->sum(fn ($row) => $row->type === 'plus' ? (int) $row->price : -(int) $row->price);
    }

    public function getShamsiDateAttribute()
    {
        return Jalalian::fromCarbon($this->created_at)->format('Y/m/d H:i');
    }

    public function forUser()
    {
        return $this->belongsTo(User::class, 'for', 'id');
    }

    /**
     * ارتباط با ایفاگر / ایجادکننده تراکنش (نماینده یا ادمین)
     */
    public function creatorUser()
    {
        return $this->belongsTo(User::class, 'creator', 'id');
    }
}
