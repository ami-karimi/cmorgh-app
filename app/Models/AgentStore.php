<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AgentStore extends Model
{
    protected $fillable = ['user_id', 'is_active', 'title', 'support_id'];

    public function agent() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
