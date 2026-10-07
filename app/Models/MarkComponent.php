<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarkComponent extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = ['school_id', 'mark_id', 'subject_component_id', 'marks_obtained'];

    public function mark(): BelongsTo
    {
        return $this->belongsTo(Mark::class);
    }

    public function subjectComponent(): BelongsTo
    {
        return $this->belongsTo(SubjectComponent::class);
    }
}
