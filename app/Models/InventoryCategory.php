<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryCategory extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = ['school_id', 'name'];

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }
}
