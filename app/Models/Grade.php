<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = ['school_id', 'name', 'min_percent', 'max_percent', 'grade_point'];

    public static function forPercent(float $percent): ?self
    {
        return static::where('min_percent', '<=', $percent)
            ->where('max_percent', '>=', $percent)
            ->first();
    }
}
