<?php

// app/Models/TopupRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopupRequest extends Model
{
    protected $fillable = ['user_id', 'requested_amount', 'unique_amount', 'payable_amount', 'status','matched_bank_message_id', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '>', now());
    }

    public function matchedBankMessage()
    {
        return $this->belongsTo(BankMessage::class, 'matched_bank_message_id');
    }


}
