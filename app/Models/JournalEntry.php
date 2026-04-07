<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    protected $fillable = ['date', 'description', 'status', 'created_by_id', 'approved_by_id', 'reference_type', 'reference_id'];

    protected $casts = [
        'date' => 'date',
        'status' => 'string',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function customerLedgers(): HasMany
    {
        return $this->hasMany(CustomerLedger::class);
    }

    public function partnerLedgers(): HasMany
    {
        return $this->hasMany(PartnerLedger::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function isBalanced(): bool
    {
        $totalDebit = $this->lines()->sum('debit');
        $totalCredit = $this->lines()->sum('credit');
        return bccomp($totalDebit, $totalCredit, 4) === 0;
    }
}