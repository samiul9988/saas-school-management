<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookCategory extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = ['school_id', 'name'];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
