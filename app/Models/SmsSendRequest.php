<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class SmsSendRequest extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id', 'idempotency_key', 'recipients', 'message', 'context',
        'segments', 'credits_required', 'status', 'provider_reference', 'error', 'created_by',
    ];

    protected $casts = [
        'recipients' => 'array',
    ];
}
