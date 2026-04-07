<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['code', 'name', 'email', 'phone', 'preferred_currency_id', 'status', 'notes'];

    protected $casts = [
        'status' => 'string',
    ];

    public function preferredCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'preferred_currency_id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'sender_customer_id');
    }

    public function exchanges(): HasMany
    {
        return $this->hasMany(Exchange::class);
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(CustomerLedger::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}