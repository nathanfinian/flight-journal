<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AirportRoute extends Model
{
    use HasFactory;
    
    protected $table = 'airport_routes';

    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'origin_id',
        'destination_id',
        'status',
        'created_by',
        'updated_by'
    ];

    public function origin()
    {
        return $this->belongsTo(Airport::class, 'origin_id');
    }

    public function destination()
    {
        return $this->belongsTo(Airport::class, 'destination_id');
    }

    public function airlines()
    {
        return $this->belongsToMany(Airline::class, 'airline_route')->withTimestamps();
    }

    // Optional: nice label like "CGK → DPS"
    public function getCodePairAttribute(): string
    {
        return ($this->origin?->iata ?? $this->origin?->icao ?? '—')
            . ' → '
            . ($this->destination?->iata ?? $this->destination?->icao ?? '—');
    }

    // Optional: nice label like "CGK → DPS"
    public function getCityPairAttribute(): string
    {
        return ($this->origin?->city ?? $this->origin?->iata ?? '—')
            . ' → '
            . ($this->destination?->city ?? $this->destination?->iata ?? '—');
    }
}
