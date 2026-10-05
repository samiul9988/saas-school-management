<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = ['school_id', 'category', 'amount', 'date', 'description'];

    protected $casts = [
        'date' => 'date',
    ];
}
