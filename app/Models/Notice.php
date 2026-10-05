<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notice extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = ['school_id', 'created_by', 'title', 'body', 'audience', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeVisibleTo($query, string $audience)
    {
        return $query->whereIn('audience', ['all', $audience])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
