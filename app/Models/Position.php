<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use BelongsToSchool;

    /**
     * Modules a position's permissions can be granted against, keyed by the
     * value stored in position_permissions.module, mapped to a display label.
     *
     * @var array<string, string>
     */
    public const MODULES = [
        'students' => 'Students',
        'hr' => 'HR / Staff',
        'hr_attendance' => 'HR Attendance',
        'student_attendance' => 'Student Attendance',
        'academics' => 'Academics',
        'exams' => 'Exams & Results',
        'finance' => 'Finance / Cashier',
        'payroll' => 'Payroll',
        'sms' => 'SMS',
        'library' => 'Library',
        'inventory' => 'Inventory',
        'transport' => 'Transport',
        'institute_setting' => 'Institute Setting',
    ];

    protected $fillable = ['school_id', 'name'];

    public function permissions(): HasMany
    {
        return $this->hasMany(PositionPermission::class);
    }
}
