<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResultBatch extends Model
{
    use BelongsToSchool, HasFactory;

    public const TYPE_EXAM = 'exam';

    public const TYPE_GRAND_FINAL = 'grand_final';

    public const MERIT_TOTAL_MARK = 'total_mark';

    public const MERIT_GRADE_POINT = 'grade_point';

    protected $fillable = ['school_id', 'type', 'name', 'exam_id', 'school_class_id', 'merit_process_type'];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(ResultBatchComponent::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(ResultBatchResult::class);
    }
}
