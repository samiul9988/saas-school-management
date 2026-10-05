<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'user_id', 'name', 'email', 'phone',
        'designation', 'photo', 'joining_date', 'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salaryStructure(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SalaryStructure::class);
    }
}
