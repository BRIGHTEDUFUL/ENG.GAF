<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use HasFactory, SoftDeletes;

        'title', 'description', 'aircraft_id', 'reported_by',
        'assigned_investigator_id', 'severity', 'status',
        'incident_date', 'location', 'resolution_notes', 'resolved_at',

    protected function casts(): array
    {
        return [
            'incident_date' => 'datetime',
            'resolved_at'   => 'datetime',
        ];
    }

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function investigator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_investigator_id');
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'red',
            'high'     => 'orange',
            'medium'   => 'yellow',
            'low'      => 'green',
            default    => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'resolved'             => 'green',
            'closed'               => 'gray',
            'under-investigation'  => 'blue',
            'open'                 => 'yellow',
            default                => 'gray',
        };
    }
}
