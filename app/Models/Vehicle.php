<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = ['school_id', 'name', 'number_plate', 'capacity', 'driver_name', 'driver_phone'];

    public function routes(): HasMany
    {
        return $this->hasMany(TransportRoute::class);
    }
}
