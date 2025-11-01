<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    protected $table = 'flights';

    protected $guarded = ['id'];

    protected $fillable = [
        'flight_no',
        'airline_route_id',
        'equipment_id',
        'branch_id',
        'service_date',
        'sched_dep',
        'sched_arr',
        'actual_dep',
        'actual_arr',
        'notes',
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
}
