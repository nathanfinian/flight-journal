<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Flight extends Model
{
    use SoftDeletes;

    protected $table = 'actual_flights';

    protected $guarded = ['id'];

    protected $fillable = [
        'origin_flight_no',
        'departure_flight_no',
        'flight_type_id',
        'origin_route_id',
        'departure_route_id',
        'origin_equipment_id',
        'departure_equipment_id',
        'branch_id',
        'service_date',
        'sched_dep',
        'sched_arr',
        'actual_dep',
        'actual_arr',
        'pax',
        'ground_time',
        'pic',
        'notes',
    ];

    protected $casts = ['service_date' => 'date'];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    public function originAirlineRoute()
    {
        return $this->belongsTo(AirlineRoute::class, 'origin_route_id');
    }

    public function departureAirlineRoute()
    {
        return $this->belongsTo(AirlineRoute::class, 'departure_route_id');
    }

    public function originEquipment()
    {
        return $this->belongsTo(Equipment::class, 'origin_equipment_id');
    }

    public function departureEquipment()
    {
        return $this->belongsTo(Equipment::class, 'departure_equipment_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function flightType()
    {
        return $this->belongsTo(FlightType::class, 'flight_type_id');
    }

    public function airline()
    {
        return $this->hasOneThrough(
            Airline::class,
            AirlineRoute::class,
            'id',                // Foreign key on airline_routes
            'id',                // Foreign key on airlines
            'origin_route_id',  // Local key on scheduled_flights
            'airline_id'         // Local key on airline_routes
        );
    }

    // Optional: Shortcut to airport route via pivot
    public function originAirportRoute()
    {
        return $this->hasOneThrough(
            AirportRoute::class,
            AirlineRoute::class,
            'id',
            'id',
            'origin_route_id',
            'airport_route_id'
        );
    }

    public function departureAirportRoute()
    {
        return $this->hasOneThrough(
            AirportRoute::class,
            AirlineRoute::class,
            'id',
            'id',
            'departure_route_id',
            'airport_route_id'
        );
    }
}
