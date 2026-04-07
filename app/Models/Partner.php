<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    protected $fillable = ['code', 'name', 'city', 'country', 'phone', 'trust_level', 'notes'];

    protected $casts = [
        'trust_level' => 'string',
    ];

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(PartnerLedger::class);
    }

    public function scopeActive($query)
    {
        return $query->where('trust_level', '!=', 'low');
    }
}