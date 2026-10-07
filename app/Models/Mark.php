<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mark extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'exam_subject_id', 'student_id', 'marks_obtained', 'is_absent', 'remarks',
    ];

    protected $casts = [
        'is_absent' => 'boolean',
    ];

    public function examSubject(): BelongsTo
    {
        return $this->belongsTo(ExamSubject::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(MarkComponent::class);
    }
}
