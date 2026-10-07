<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubjectComponent extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'subject_id', 'exam_short_code_id', 'total', 'pass_mark', 'acceptance', 'serial',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function shortCode(): BelongsTo
    {
        return $this->belongsTo(ExamShortCode::class, 'exam_short_code_id');
    }

    public function markComponents(): HasMany
    {
        return $this->hasMany(MarkComponent::class);
    }
}
