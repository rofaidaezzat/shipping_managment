<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role', 'is_super_admin'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('user')
            ->setDescriptionForEvent(fn(string $eventName) => "User '{$this->name}' was {$eventName}");
    }

    /**
     * Predefined user roles.
     */
    public const ROLES = [
        'Super Admin',
        'Admin',
        'Sales',
        'Accountant',
        'Employee',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_super_admin'    => 'boolean',
        ];
    }

    /**
     * Auto-sync is_super_admin flag with role on saving.
     */
    protected static function booted()
    {
        static::saving(function ($user) {
            if ($user->role === 'Super Admin') {
                $user->is_super_admin = true;
            } elseif ($user->isDirty('role') && $user->role !== 'Super Admin') {
                $user->is_super_admin = false;
            }
        });
    }

    /**
     * Determine if the user has super admin privileges.
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin 
            || in_array(strtolower(str_replace(' ', '', $this->role ?? '')), ['superadmin', 'admin']);
    }

    /**
     * Badge classes matching the user role.
     */
    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'Super Admin' => 'bg-purple-100 text-purple-800 border-purple-200',
            'Admin'       => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'Sales'       => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'Accountant'  => 'bg-amber-100 text-amber-800 border-amber-200',
            'Employee'    => 'bg-slate-100 text-slate-700 border-slate-200',
            default       => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    /**
     * Role icon color & descriptor.
     */
    public function getRoleColorAttribute(): string
    {
        return match ($this->role) {
            'Super Admin' => 'purple',
            'Admin'       => 'indigo',
            'Sales'       => 'emerald',
            'Accountant'  => 'amber',
            'Employee'    => 'slate',
            default       => 'slate',
        };
    }

    /**
     * Scope query to search by name or email.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('role', 'like', "%{$term}%");
        });
    }

    /**
     * Scope query by role.
     */
    public function scopeRole($query, ?string $role)
    {
        if (!empty($role)) {
            return $query->where('role', $role);
        }

        return $query;
    }
}
