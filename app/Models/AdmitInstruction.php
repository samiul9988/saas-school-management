<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmitInstruction extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'school_class_id', 'exam_id',
        'instruction_1', 'instruction_2', 'instruction_3', 'instruction_4',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function lines(): array
    {
        return array_values(array_filter([
            $this->instruction_1, $this->instruction_2, $this->instruction_3, $this->instruction_4,
        ]));
    }
}
