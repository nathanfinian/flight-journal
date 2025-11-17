<?php

namespace App\Models;

use App\Models\AirlineRoute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledFlights extends Model
{
    use SoftDeletes;

    protected $table = 'scheduled_flights';

    protected $guarded = ['id'];

    protected $fillable = [
        'flight_no',
        'airline_route_id',
        'equipment_id',
        'branch_id',
        'sched_dep',
        'sched_arr',
        'created_by',
        'updated_by',
    ];

    public function airlineRoute()
    {
        return $this->belongsTo(AirlineRoute::class, 'airline_route_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function days()
    {
        return $this->belongsToMany(Day::class, 'operating_patterns', 'scheduled_flight_id', 'day_id')->orderBy('days.id'); // ✅ ensures consistent ordering
    }

    // Optional: Shortcut to airline via pivot
    public function airline()
    {
        return $this->hasOneThrough(
            Airline::class,
            AirlineRoute::class,
            'id',                // Foreign key on airline_routes
            'id',                // Foreign key on airlines
            'airline_route_id',  // Local key on scheduled_flights
            'airline_id'         // Local key on airline_routes
        );
    }

    // Optional: Shortcut to airport route via pivot
    public function airportRoute()
    {
        return $this->hasOneThrough(
            AirportRoute::class,
            AirlineRoute::class,
            'id',
            'id',
            'airline_route_id',
            'airport_route_id'
        );
    }
}
