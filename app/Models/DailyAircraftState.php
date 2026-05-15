<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class DailyAircraftState extends Model
{
    protected $fillable = [
        'report_date', 'aircraft_id', 'wing_id',
        'daily_flight_hrs', 'total_flight_hrs',
        'daily_landings', 'total_landings',
        'state', 'notes', 'created_by',
    ];

    protected $casts = [
        'report_date'      => 'date',
        'daily_flight_hrs' => 'decimal:2',
        'total_flight_hrs' => 'decimal:2',
        'is_critical'      => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────
    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function wing(): BelongsTo
    {
        return $this->belongsTo(Wing::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function defects(): HasMany
    {
        return $this->hasMany(DailyDefect::class)->orderBy('defect_number');
    }

    public function serviceRemarks(): HasMany
    {
        return $this->hasMany(DailyServiceRemark::class)->orderBy('remark_number');
    }

    // ── Scopes ────────────────────────────────────────────
    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('report_date', $date);
    }

    public function scopeForWing(Builder $query, int $wingId): Builder
    {
        return $query->where('wing_id', $wingId);
    }

    public function scopeServiceable(Builder $query): Builder
    {
        return $query->where('state', 'S');
    }

    public function scopeUnserviceable(Builder $query): Builder
    {
        return $query->where('state', 'U/S');
    }

    // ── Accessors ─────────────────────────────────────────
    public function getStateBadgeColorAttribute(): string
    {
        return match($this->state) {
            'S'       => 'bg-green-100 text-green-700 border-green-200',
            'U/S'     => 'bg-red-100 text-red-700 border-red-200',
            'grounded'=> 'bg-gray-100 text-gray-600 border-gray-200',
            default   => 'bg-gray-100 text-gray-600',
        };
    }

    public function getDisplayStateAttribute(): string
    {
        return match($this->state) {
            'S'       => 'S',
            'U/S'     => 'U/S',
            'grounded'=> 'GND',
            default   => '—',
        };
    }

    public function getHasCriticalDefectAttribute(): bool
    {
        return $this->defects->contains('is_critical', true);
    }
}
