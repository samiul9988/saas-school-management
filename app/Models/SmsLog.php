<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = ['school_id', 'to', 'message', 'context', 'status'];
}
