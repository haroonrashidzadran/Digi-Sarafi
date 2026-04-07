<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerLedger extends Model
{
    protected $fillable = ['partner_id', 'journal_entry_id', 'amount', 'currency_id', 'direction', 'description'];

    protected $casts = [
        'amount' => 'decimal:4',
        'direction' => 'string',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}