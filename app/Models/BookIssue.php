<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookIssue extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'book_id', 'student_id', 'teacher_id',
        'issued_on', 'due_on', 'returned_on', 'fine_amount',
    ];

    protected $casts = [
        'issued_on' => 'date',
        'due_on' => 'date',
        'returned_on' => 'date',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function isOverdue(): bool
    {
        return ! $this->returned_on && $this->due_on->isPast();
    }
}
