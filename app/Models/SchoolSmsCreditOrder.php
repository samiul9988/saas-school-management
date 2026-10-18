<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolSmsCreditOrder extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id', 'credits', 'price_per_sms', 'amount', 'method', 'transaction_id',
        'status', 'requested_by', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
