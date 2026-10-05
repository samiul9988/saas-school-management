<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryStructure extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = ['school_id', 'teacher_id', 'basic_salary', 'allowances', 'deductions'];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function netSalary(): float
    {
        return (float) $this->basic_salary + (float) $this->allowances - (float) $this->deductions;
    }
}
