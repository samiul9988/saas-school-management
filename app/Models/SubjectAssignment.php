<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectAssignment extends Model
{
    use BelongsToSchool, HasFactory;

    public const TYPES = ['Compulsory', 'Optional', 'Fourth Subject'];

    protected $fillable = [
        'school_id', 'school_class_id', 'group_name', 'subject_id', 'subject_type', 'serial', 'merge_id',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
