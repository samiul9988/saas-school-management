<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class SchoolSmsWalletTransaction extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id', 'type', 'credits', 'balance_after', 'reference_type', 'reference_id', 'note', 'created_by',
    ];
}
