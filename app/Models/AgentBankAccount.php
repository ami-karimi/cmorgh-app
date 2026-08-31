<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AgentBankAccount extends Model
{
    protected $fillable = ['user_id', 'bank_name', 'account_name', 'card_number', 'sheba_number'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
