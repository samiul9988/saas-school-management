<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'book_category_id', 'title', 'author', 'publisher',
        'isbn', 'total_copies', 'available_copies',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class);
    }
}
