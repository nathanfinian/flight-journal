<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    protected $table = 'airlines';

    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'icao_code',
        'iata_code',
        'country',
        'callsign',
        'created_by',
        'updated_by'
    ];

    public function getRouteKeyName()
    {
        return 'id'; //Assuming you are using 'id' as a unique field
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'airline_route')->withTimestamps();
    }
}
