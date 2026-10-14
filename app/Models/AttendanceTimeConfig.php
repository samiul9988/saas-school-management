<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceTimeConfig extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'school_class_id', 'section_id', 'period_id',
        'start_time', 'end_time', 'delay_time', 'process_time', 'send_sms_status',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }
}
