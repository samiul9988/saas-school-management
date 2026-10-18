<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsGatewaySetting extends Model
{
    protected $fillable = [
        'provider', 'api_key', 'sender_id', 'api_base_url',
        'price_per_sms', 'low_balance_threshold', 'central_balance',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
        'price_per_sms' => 'decimal:4',
        'central_balance' => 'integer',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate([]);
    }
}
