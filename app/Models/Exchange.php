<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exchange extends Model
{
    protected $fillable = [
        'code', 'customer_id', 'from_currency_id', 'to_currency_id',
        'amount_from', 'amount_to', 'rate', 'profit', 'profit_account_id',
        'journal_entry_id', 'created_by_id', 'status'
    ];

    protected $casts = [
        'amount_from' => 'decimal:4',
        'amount_to' => 'decimal:4',
        'rate' => 'decimal:6',
        'profit' => 'decimal:4',
        'status' => 'string',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    public function profitAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'profit_account_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}