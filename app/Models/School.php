<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'admin_name', 'slug', 'email', 'phone', 'contact_number', 'address', 'logo',
        'admit_card_template', 'id_card_template', 'hr_id_card_template', 'plan_id', 'academic_year_id',
        'status', 'trial_ends_at', 'subscription_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function activeSubscription(): HasMany
    {
        return $this->subscriptions()->where('status', 'active');
    }

    public function smsWallet(): HasOne
    {
        return $this->hasOne(SchoolSmsWallet::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(Signature::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' || $this->status === 'trial';
    }
}
