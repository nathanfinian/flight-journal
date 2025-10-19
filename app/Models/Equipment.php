<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'registration',
        'aircraft_id',
        'airline_id',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'id'; //Assuming you are using 'id' as a unique field
    }
}
