<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class SchoolSmsWallet extends Model
{
    use BelongsToSchool;

    protected $fillable = ['school_id', 'balance', 'total_purchased', 'total_used'];
}
