<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use BelongsToSchool;

    public const CATEGORIES = ['guardian', 'hr', 'result', 'due_fee', 'attendance', 'birthday', 'dynamic', 'admission'];

    protected $fillable = ['school_id', 'category', 'title', 'content', 'is_default'];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public static function defaultContentFor(string $category): string
    {
        return static::where('category', $category)->where('is_default', true)->value('content') ?? '';
    }
}
