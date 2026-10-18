<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentralSmsLedger extends Model
{
    protected $table = 'central_sms_ledger';

    protected $fillable = [
        'type', 'credits', 'balance_after', 'reference_type', 'reference_id', 'note', 'created_by',
    ];
}
