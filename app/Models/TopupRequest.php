<?php

// app/Models/TopupRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopupRequest extends Model
{
    protected $fillable = ['user_id', 'requested_amount', 'unique_amount', 'payable_amount', 'status','matched_bank_message_id', 'expires_at', 'matched_at', 'match_type', 'settled_at', 'rejection_reason'];

    protected $casts = ['expires_at' => 'datetime', 'matched_at' => 'datetime', 'settled_at' => 'datetime', 'requested_amount' => 'integer', 'payable_amount' => 'integer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '>', now());
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'entity_id')->where('entity', 'topup_request')->orderByDesc('id');
    }

    public function matchedBankMessage()
    {
        return $this->belongsTo(BankMessage::class, 'matched_bank_message_id');
    }


}
