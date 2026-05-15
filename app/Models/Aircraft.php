<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aircraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'tail_number', 'model', 'manufacturer', 'year_manufactured',
        'wing_id', 'status', 'last_maintenance_date', 'total_flight_hours', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'last_maintenance_date' => 'date',
            'total_flight_hours'    => 'decimal:2',
        ];
    }

    public function wing(): BelongsTo
    {
        return $this->belongsTo(Wing::class);
    }

    public function maintenanceTasks(): HasMany
    {
        return $this->hasMany(MaintenanceTask::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function flightLogs(): HasMany
    {
        return $this->hasMany(FlightLog::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active'      => 'green',
            'maintenance' => 'yellow',
            'grounded'    => 'red',
            'retired'     => 'gray',
            default       => 'gray',
        };
    }
}
