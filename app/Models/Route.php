<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'routes';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'origin_id',
        'destination_id',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function airlines()
    {
        return $this->belongsToMany(Airline::class, 'airline_route')->withTimestamps();
    }
}
