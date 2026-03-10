<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AirlineRate extends Model
{
    protected $table = 'airline_rates';

    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'airline_id',
        'branch_id',
        'charge_name',
        'charge_code',
        'date_from',
        'date_to',
        'ground_fee',
        'cargo_fee',
        'ppn_rate',
        'pph_rate',
        'konsesi_rate',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
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

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'airline_rates_id');
    }

    public function flightTypes()
    {
        return $this->belongsToMany(FlightType::class,'airline_rate_flight_type')
            ->withPivot(['percentage'])
            ->withTimestamps();
    }

    public function getRouteKeyName()
    {
        return 'id'; //Assuming you are using 'id' as a unique field
    }
}
