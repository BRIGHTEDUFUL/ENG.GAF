<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyServiceRemark extends Model
{
    protected $fillable = [
        'daily_aircraft_state_id',
        'remark_number',
        'description',
        'due_hours',
        'service_location',
    ];

    protected $casts = [
        'due_hours' => 'decimal:2',
    ];

    public function dailyAircraftState(): BelongsTo
    {
        return $this->belongsTo(DailyAircraftState::class);
    }

    public function getRomanNumeralAttribute(): string
    {
        $map = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X'];
        return $map[$this->remark_number] ?? (string) $this->remark_number;
    }
}
