<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'reference',
        'amount',
        'currency',
        'status',
        'signature',
        'request_payload',
        'response_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function callbacks(): HasMany
    {
        return $this->hasMany(Callback::class);
    }
}
