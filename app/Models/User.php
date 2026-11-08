<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'password', 'role', 'school_id', 'position_id', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_SCHOOL_ADMIN = 'school_admin';

    public const ROLE_SCHOOL_OPERATOR = 'school_operator';

    public const ROLE_SCHOOL_TEACHER_STAFF = 'school_teacher_staff';

    public const ROLE_PARENT = 'parent';

    public const SCHOOL_STAFF_ROLES = [
        self::ROLE_SCHOOL_ADMIN,
        self::ROLE_SCHOOL_OPERATOR,
        self::ROLE_SCHOOL_TEACHER_STAFF,
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function portalStudent(): HasOne
    {
        return $this->hasOne(Student::class, 'portal_user_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isSchoolAdmin(): bool
    {
        return $this->role === self::ROLE_SCHOOL_ADMIN;
    }

    public function isParent(): bool
    {
        return $this->role === self::ROLE_PARENT;
    }

    public function isSchoolStaff(): bool
    {
        return in_array($this->role, self::SCHOOL_STAFF_ROLES, true);
    }

    /**
     * Whether this user may perform $action ('view'|'create'|'edit'|'delete')
     * on $module. School admins (and super admins) always have full access;
     * every other school-staff user needs their Position to grant it.
     */
    public function hasModuleAccess(string $module, string $action): bool
    {
        if ($this->isSuperAdmin() || $this->isSchoolAdmin()) {
            return true;
        }

        $permission = $this->position?->permissions->firstWhere('module', $module);

        if (! $permission) {
            return false;
        }

        return (bool) $permission->{"can_{$action}"};
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
