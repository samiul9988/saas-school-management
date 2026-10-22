<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'teacher_id', 'month', 'year',
        'basic_salary', 'allowances', 'deductions', 'net_salary', 'status', 'paid_on',
        'method', 'payment_account_id', 'journal_entry_id',
    ];

    protected $casts = [
        'paid_on' => 'date',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'payment_account_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
