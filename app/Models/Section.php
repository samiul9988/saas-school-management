<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use BelongsToSchool, HasFactory;

    /**
     * A Section's name is defined once (Students > Settings > Section) with
     * no class yet, then assigned to one or more classes from Students >
     * Configuration > Class. Until assigned, school_class_id is null and the
     * row is just a name — hidden from every normal query by this global
     * scope so it never shows up as a usable section elsewhere in the app.
     * Settings' own listing and Configuration's name picker explicitly
     * bypass this scope (withoutGlobalScope('assigned')) to manage names.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('assigned', function (Builder $builder) {
            $builder->whereNotNull($builder->getModel()->getTable().'.school_class_id');
        });
    }

    protected $fillable = ['school_id', 'school_class_id', 'shift_id', 'name', 'capacity', 'serial', 'status'];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
