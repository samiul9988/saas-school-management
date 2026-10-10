<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarksheetTemplateConfig extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'school_class_id', 'template_number', 'marksheet_header',
        'mark_sheet_size', 'student_img_status', 'watermark_status',
    ];

    protected $casts = [
        'student_img_status' => 'boolean',
        'watermark_status' => 'boolean',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }
}
