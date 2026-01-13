<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'status',
        'address',
        'phone_number',
        'email',
    ];

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

    public function airport()
    {
        return $this->belongsTo(Airport::class);
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
