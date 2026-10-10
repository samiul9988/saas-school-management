<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolClass extends Model
{
    use BelongsToSchool, HasFactory;

    protected $table = 'school_classes';

    protected $fillable = ['school_id', 'name', 'sort_order'];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function grandFinal(): HasOne
    {
        return $this->hasOne(ResultBatch::class, 'school_class_id')->where('type', ResultBatch::TYPE_GRAND_FINAL);
    }
}
