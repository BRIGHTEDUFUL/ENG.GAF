<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyDefect extends Model
{
    protected $fillable = [
        'daily_aircraft_state_id',
        'defect_number',
        'description',
        'is_critical',
    ];

    protected $casts = [
        'is_critical' => 'boolean',
    ];

    public function dailyAircraftState(): BelongsTo
    {
        return $this->belongsTo(DailyAircraftState::class);
    }

    /**
     * Convert defect_number integer to Roman numeral string.
     */
    public function getRomanNumeralAttribute(): string
    {
        $map = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X'];
        return $map[$this->defect_number] ?? (string) $this->defect_number;
    }
}
