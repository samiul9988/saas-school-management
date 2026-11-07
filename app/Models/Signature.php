<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Signature extends Model
{
    use BelongsToSchool;

    public const POSITIONS = [
        'Student ID Card',
        'HR ID Card',
        'Mark Sheet Right',
        'Mark Sheet Middle',
        'Mark Sheet Left',
        'Admit Card Right',
        'Admit Card Left',
        'Student Fee Receipt Bottom',
        'Testimonial Right',
        'Testimonial Left',
        'TC Left',
        'TC Right',
        'Class Test Mark Sheet Middle',
        'Class Test Mark Sheet Right',
    ];

    protected $fillable = ['school_id', 'title', 'position', 'image_path', 'status'];

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    public static function activeForPosition(string $position): ?self
    {
        return static::where('position', $position)
            ->where('status', 'active')
            ->latest()
            ->first();
    }
}
