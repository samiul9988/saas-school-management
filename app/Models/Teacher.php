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
        'staff_serial', 'machine_id', 'gender', 'religion', 'blood_group',
        'father_name', 'mother_name', 'date_of_birth', 'resignation_date', 'address',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'resignation_date' => 'date',
        'date_of_birth' => 'date',
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
