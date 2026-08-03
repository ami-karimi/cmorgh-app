<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $table = 'users_activity';
    protected $guarded = [];

    protected $casts = [
        'agent_view' => 'boolean',
        'admin_view' => 'boolean',
    ];

    public function causer()
    {
        return $this->belongsTo(User::class, 'by', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Accounts::class, 'user_id', 'id');
    }
}
