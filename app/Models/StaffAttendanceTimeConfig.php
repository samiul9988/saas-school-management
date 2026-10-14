<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAttendanceTimeConfig extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'teacher_id', 'in_time', 'out_time',
        'delay_time', 'input_process_time', 'output_process_time', 'days',
    ];

    protected $casts = [
        'days' => 'array',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
