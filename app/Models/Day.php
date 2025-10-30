<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    public function scheduledFlights()
    {
        return $this->belongsToMany(ScheduledFlights::class, 'operating_patterns', 'day_id', 'scheduled_flight_id');
    }
}
