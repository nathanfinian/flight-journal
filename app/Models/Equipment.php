<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;
    
    protected $table = 'equipments';

    protected $guarded = ['id'];

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

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }
    
    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function getRouteKeyName(): string
    {
        return 'id'; // optional, this is default — but include it for clarity
    }
}
