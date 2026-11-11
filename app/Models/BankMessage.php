<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankMessage extends Model
{
    protected $fillable = [
        'account_number',
        'deposit_amount',
        'balance',
        'transaction_datetime',
        'raw_message',
        'processed',
        'processed_at',
        'deduplication_key',
        'processing_error',
    ];

    protected $casts = [
        'transaction_datetime' => 'datetime',
        'processed_at' => 'datetime',
        'deposit_amount' => 'integer',
        'balance' => 'integer',
        'processed' => 'boolean',
    ];

    public function scopeUnprocessed($query)
    {
        return $query->where('processed', false);
    }

    public function matchedRequest()
    {
        return $this->hasOne(TopupRequest::class, 'matched_bank_message_id');
    }

}
