<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scheduledFlights()
    {
        return $this->hasMany(ScheduledFlights::class, 'airline_route_id');
    }

    public function flights()
    {
        return $this->hasMany(Flight::class, 'airline_route_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    public function getRouteKeyName()
    {
        return 'id'; //Assuming you are using 'id' as a unique field
    }
}
