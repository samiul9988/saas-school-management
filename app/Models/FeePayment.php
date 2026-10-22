<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePayment extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'student_fee_id', 'student_id', 'receipt_number',
        'amount', 'method', 'transaction_id', 'paid_on', 'received_by',
        'payment_account_id', 'journal_entry_id',
    ];

    protected $casts = [
        'paid_on' => 'date',
    ];

    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'payment_account_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public static function generateReceiptNumber(int $schoolId): string
    {
        return 'RCPT-'.$schoolId.'-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -6));
    }
}
