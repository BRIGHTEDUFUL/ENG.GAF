<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'maintenance_task_id', 'aircraft_id', 'engineer_id',
        'work_performed', 'parts_used', 'hours_spent', 'log_date',
        'status', 'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'log_date'    => 'date',
            'approved_at' => 'datetime',
            'hours_spent' => 'decimal:2',
        ];
    }

    public function maintenanceTask(): BelongsTo
    {
        return $this->belongsTo(MaintenanceTask::class);
    }

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved'  => 'green',
            'submitted' => 'blue',
            'draft'     => 'yellow',
            default     => 'gray',
        };
    }
}
