<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    use BelongsToSchool, HasFactory;

    protected $fillable = [
        'school_id', 'academic_year_id', 'school_class_id', 'section_id',
        'group_name', 'category', 'admission_number', 'roll', 'name', 'date_of_birth', 'gender',
        'religion', 'blood_group', 'guardian_name', 'father_name', 'mother_name', 'guardian_phone',
        'address', 'village', 'post_office', 'upazila_thana', 'district', 'photo', 'status', 'portal_user_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }

    public function portalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'portal_user_id');
    }

    public function transport(): HasOne
    {
        return $this->hasOne(StudentTransport::class);
    }

    /**
     * Order by roll numerically rather than lexicographically ("2" before "10"),
     * since `roll` is stored as a string. Falls back to string order for ties
     * (e.g. non-numeric rolls) so nothing is silently dropped from the sort.
     */
    public function scopeOrderByRoll(Builder $query): Builder
    {
        $driver = DB::connection()->getDriverName();
        $castType = $driver === 'sqlite' ? 'INTEGER' : 'UNSIGNED';

        return $query->orderByRaw("CAST(roll AS {$castType}), roll");
    }
}
