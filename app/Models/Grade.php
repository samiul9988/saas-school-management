<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use BelongsToSchool, HasFactory;

    /**
     * The standard grade bands offered when setting up a class's grading
     * scale, matching the reference product's fixed A+..F preset.
     *
     * @var array<string, array{min: float, max: float, point: float}>
     */
    public const STANDARD_GRADES = [
        'A+' => ['min' => 80, 'max' => 100, 'point' => 5],
        'A' => ['min' => 70, 'max' => 79, 'point' => 4],
        'A-' => ['min' => 60, 'max' => 69, 'point' => 3.5],
        'B' => ['min' => 50, 'max' => 59, 'point' => 3],
        'C' => ['min' => 40, 'max' => 49, 'point' => 2],
        'D' => ['min' => 33, 'max' => 39, 'point' => 1],
        'F' => ['min' => 0, 'max' => 32, 'point' => 0],
    ];

    protected $fillable = ['school_id', 'school_class_id', 'name', 'min_percent', 'max_percent', 'grade_point'];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Grade for $percent, preferring a scale configured for $schoolClassId
     * and falling back to the school's class-agnostic (global) scale when
     * that class has no grades of its own configured yet.
     */
    public static function forPercent(float $percent, ?int $schoolClassId = null): ?self
    {
        if ($schoolClassId) {
            $classGrade = static::where('school_class_id', $schoolClassId)
                ->where('min_percent', '<=', $percent)
                ->where('max_percent', '>=', $percent)
                ->first();

            if ($classGrade) {
                return $classGrade;
            }
        }

        return static::whereNull('school_class_id')
            ->where('min_percent', '<=', $percent)
            ->where('max_percent', '>=', $percent)
            ->first();
    }
}
