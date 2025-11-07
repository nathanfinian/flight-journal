<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirlineRoute extends Model
{
    protected $table = 'airline_routes';

    protected $fillable = [
        'airport_route_id',
        'airline_id',
        'created_at',
        'updated_at',
    ];

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function airportRoute()
    {
        return $this->belongsTo(AirportRoute::class, 'airport_route_id');
    }

    public function scheduledFlights()
    {
        return $this->hasMany(ScheduledFlights::class, 'airline_route_id');
    }

    public function flights()
    {
        return $this->hasMany(Flight::class, 'airline_route_id');
    }
}
