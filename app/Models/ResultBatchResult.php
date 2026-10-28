<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultBatchResult extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'result_batch_id', 'student_id', 'school_class_id', 'section_id', 'group_name',
        'total_obtained', 'total_full', 'percentage', 'gpa', 'grade_id', 'position', 'is_passed', 'failed_subjects_count', 'remarks',
    ];

    protected $casts = [
        'is_passed' => 'boolean',
    ];

    public function resultBatch(): BelongsTo
    {
        return $this->belongsTo(ResultBatch::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
