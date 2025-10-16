<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aircraft extends Model
{
    protected $table = 'aircraft';
    protected $fillable = ['type_name', 'icao_code', 'iata_code', 'seat_capacity'];

    // public function equipment(): HasMany
    // {
    //     return $this->hasMany(Equipment::class);
    // }
}
