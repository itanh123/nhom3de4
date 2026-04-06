<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public const ROLE_ADMIN = 'admin';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_STUDENT = 'student';

    public static function roles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_TEACHER,
            self::ROLE_STUDENT,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class, 'created_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function aiConfigsCreated(): HasMany
    {
        return $this->hasMany(AiConfig::class, 'created_by');
    }

    public function aiConfigsUpdated(): HasMany
    {
        return $this->hasMany(AiConfig::class, 'updated_by');
    }

    public function importHistories(): HasMany
    {
        return $this->hasMany(ImportHistory::class);
    }
}
