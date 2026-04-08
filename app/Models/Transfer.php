<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Transfer extends Model
{
    protected $fillable = [
        'code', 'sender_customer_id', 'receiver_name', 'receiver_phone',
        'partner_id', 'amount', 'currency_id', 'fee', 'status',
        'otp_code', 'otp_expires_at', 'journal_entry_id', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'fee' => 'decimal:4',
        'otp_expires_at' => 'datetime',
        'status' => 'string',
    ];

    public function senderCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'sender_customer_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function partners(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class, 'transfer_partners');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['sent', 'paid', 'settled']);
    }
}