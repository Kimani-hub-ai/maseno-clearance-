<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => UserRole::class,
            'is_active'         => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function departmentOfficer()
    {
        return $this->hasOne(DepartmentOfficer::class);
    }

    /**
     * In-app notifications from the custom notifications table.
     * Named appNotifications() to avoid conflict with Laravel's
     * built-in Notifiable trait which also defines notifications().
     */
    public function appNotifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function dashboardRoute(): string
    {
        return match($this->role) {
            UserRole::Student   => 'student.dashboard',
            UserRole::Officer   => 'department.dashboard',
            UserRole::Registrar => 'registrar.dashboard',
            UserRole::Admin     => 'admin.dashboard',
        };
    }

    public function isStudent(): bool   { return $this->role === UserRole::Student; }
    public function isOfficer(): bool   { return $this->role === UserRole::Officer; }
    public function isRegistrar(): bool { return $this->role === UserRole::Registrar; }
    public function isAdmin(): bool     { return $this->role === UserRole::Admin; }
}