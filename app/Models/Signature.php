<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Signature extends Model
{
    use BelongsToSchool;

    protected $fillable = ['school_id', 'title', 'position', 'image_path', 'status'];

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }
}
