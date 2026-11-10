<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamShortCode extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = ['school_id', 'name', 'note', 'serial', 'default_total', 'default_pass_mark', 'default_acceptance'];
}
