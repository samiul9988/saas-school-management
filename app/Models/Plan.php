<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    public const FEATURES = [
        'students' => 'Student Management',
        'teachers' => 'Teacher Management',
        'attendance' => 'Attendance',
        'exams' => 'Exams',
        'results' => 'Results & Marksheet',
        'basic_fees' => 'Basic Fees',
        'parent_portal' => 'Parent Portal',
        'sms' => 'SMS Notifications',
        'library' => 'Library',
        'payroll' => 'Payroll / HR',
        'advanced_reports' => 'Advanced Reports',
        'transport' => 'Transport',
        'inventory' => 'Inventory',
        'online_payment' => 'Online Payment (bKash/Nagad)',
        'multi_branch' => 'Multi-Branch',
        'advanced_accounting' => 'Advanced Accounting',
        'hostel' => 'Hostel',
    ];

    protected $fillable = [
        'name', 'slug', 'description', 'monthly_price', 'yearly_price',
        'max_students', 'max_teachers', 'max_branches', 'features',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
    ];

    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }
}
