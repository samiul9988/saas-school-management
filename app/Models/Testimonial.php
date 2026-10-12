<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'serial', 'student_id', 'student_name', 'address', 'class_name', 'roll', 'gpa',
        'registration_number', 'father_name', 'mother_name', 'session', 'date_of_birth', 'issue_date',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'issue_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
