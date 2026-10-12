<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferCertificate extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'serial', 'student_id', 'student_name', 'address', 'class_name', 'roll', 'gpa',
        'registration_number', 'cause_of_leaving', 'examination_name', 'examination_date',
        'father_name', 'mother_name', 'session', 'date_of_birth', 'gender', 'promoted_class', 'issue_date',
    ];

    protected $casts = [
        'examination_date' => 'date',
        'date_of_birth' => 'date',
        'issue_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
