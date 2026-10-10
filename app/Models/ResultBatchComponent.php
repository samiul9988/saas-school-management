<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultBatchComponent extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = ['school_id', 'result_batch_id', 'exam_id', 'weight'];

    public function resultBatch(): BelongsTo
    {
        return $this->belongsTo(ResultBatch::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
