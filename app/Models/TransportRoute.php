<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportRoute extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = ['school_id', 'vehicle_id', 'name'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function stops(): HasMany
    {
        return $this->hasMany(RouteStop::class);
    }

    public function studentTransports(): HasMany
    {
        return $this->hasMany(StudentTransport::class);
    }
}
