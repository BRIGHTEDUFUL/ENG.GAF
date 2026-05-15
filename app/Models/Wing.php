<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wing extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'base_location',
        'commander_id',
        'status',
        'established_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'established_date' => 'date',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function commander(): BelongsTo
    {
        return $this->belongsTo(User::class, 'commander_id');
    }

    public function aircraft(): HasMany
    {
        return $this->hasMany(Aircraft::class);
    }

    public function personnel(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getAircraftCountAttribute(): int
    {
        return $this->aircraft()->count();
    }

    public function getPersonnelCountAttribute(): int
    {
        return $this->personnel()->count();
    }
}
