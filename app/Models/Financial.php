<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;
class Financial extends Model
{
    protected $table = 'financial';
    protected $guarded = ['id'];

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
