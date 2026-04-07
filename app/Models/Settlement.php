<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settlement extends Model
{
    protected $fillable = ['code', 'partner_id', 'amount', 'currency_id', 'type', 'status', 'description'];

    protected $casts = [
        'amount' => 'decimal:4',
        'status' => 'string',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}