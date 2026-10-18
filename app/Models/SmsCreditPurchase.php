<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsCreditPurchase extends Model
{
    protected $fillable = [
        'credits', 'cost', 'provider_reference', 'purchased_by', 'purchased_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    public function purchasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchased_by');
    }
}
