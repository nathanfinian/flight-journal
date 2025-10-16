<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aircraft extends Model
{
    protected $table = 'aircrafts';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type_name', 
        'icao_code', 
        'iata_code', 
        'seat_capacity'
    ];

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'seat_capacity' => 'integer'
    ];

    public function getRouteKeyName()
    {
        return 'id'; //Assuming you are using 'id' as a unique field
    }

    // public function equipment(): HasMany
    // {
    //     return $this->hasMany(Equipment::class);
    // }
}
