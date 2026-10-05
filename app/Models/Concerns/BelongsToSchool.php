<?php

namespace App\Models\Concerns;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToSchool
{
    public static function bootBelongsToSchool(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (Auth::check() && Auth::user()->school_id) {
                $builder->where($builder->getModel()->getTable().'.school_id', Auth::user()->school_id);
            }
        });

        static::creating(function ($model) {
            if (empty($model->school_id) && Auth::check() && Auth::user()->school_id) {
                $model->school_id = Auth::user()->school_id;
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
