<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'rank',
        'wing_id',
        'failed_attempts',
        'last_failed_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'last_failed_login'  => 'datetime',
            'failed_attempts'    => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function wing(): BelongsTo
    {
        return $this->belongsTo(Wing::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class, 'engineer_id');
    }

    public function assignedMaintenanceTasks(): HasMany
    {
        return $this->hasMany(MaintenanceTask::class, 'assigned_to');
    }

    public function createdMaintenanceTasks(): HasMany
    {
        return $this->hasMany(MaintenanceTask::class, 'created_by');
    }

    public function flightLogs(): HasMany
    {
        return $this->hasMany(FlightLog::class, 'pilot_id');
    }

    public function reportedIncidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'reported_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // -------------------------------------------------------------------------
    // Role helpers
    // -------------------------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCommander(): bool
    {
        return $this->role === 'commander';
    }

    public function isEngineer(): bool
    {
        return $this->role === 'engineer';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isAuditor(): bool
    {
        return $this->role === 'auditor';
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role, $roles, true);
    }

    public function canManage(): bool
    {
        return $this->hasRole(['admin', 'commander', 'supervisor']);
    }

    /**
     * Get display name with rank.
     */
    public function getFullTitleAttribute(): string
    {
        return $this->rank ? "{$this->rank} {$this->name}" : $this->name;
    }
}
