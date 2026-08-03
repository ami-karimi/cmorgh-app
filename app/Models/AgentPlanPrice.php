<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentPlanPrice extends Model
{
    protected $fillable = ['agent_id', 'group_id', 'selling_price'];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
