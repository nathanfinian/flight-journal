<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    protected $table = 'airports';

    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'icao',
        'iata',
        'city',
        'country',
        'tz',
        'created_by',
        'updated_by'
    ];

    public function getRouteKeyName()
    {
        return 'id'; //Assuming you are using 'id' as a unique field
    }
}
