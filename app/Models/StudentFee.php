<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentFee extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'student_id', 'fee_type_id', 'academic_year_id',
        'amount', 'paid_amount', 'due_date', 'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function dueAmount(): float
    {
        return (float) $this->amount - (float) $this->paid_amount;
    }

    public function recalculateStatus(): void
    {
        if ($this->paid_amount <= 0) {
            $this->status = 'unpaid';
        } elseif ($this->paid_amount < $this->amount) {
            $this->status = 'partial';
        } else {
            $this->status = 'paid';
        }

        $this->save();
    }
}
