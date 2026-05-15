<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlightLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'aircraft_id', 'pilot_id', 'co_pilot_id',
        'departure_location', 'arrival_location',
        'departure_time', 'arrival_time', 'flight_duration_minutes',
        'max_altitude_ft', 'max_speed_knots', 'gps_track',
        'mission_type', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'departure_time' => 'datetime',
            'arrival_time'   => 'datetime',
            'gps_track'      => 'array',
        ];
    }

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function pilot(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pilot_id');
    }

    public function coPilot(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_pilot_id');
    }

    public function scopeByMissionType($query, string $type)
    {
        return $query->where('mission_type', $type);
    }

    public function scopeInDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('departure_time', [$from, $to]);
    }

    /**
     * Compute and store flight duration before saving.
     */
    protected static function booted(): void
    {
        static::saving(function (FlightLog $log) {
            if ($log->departure_time && $log->arrival_time) {
                $log->flight_duration_minutes = (int) $log->departure_time
                    ->diffInMinutes($log->arrival_time);
            }
        });
    }

    public function getFlightDurationHoursAttribute(): float
    {
        return round($this->flight_duration_minutes / 60, 2);
    }
}
