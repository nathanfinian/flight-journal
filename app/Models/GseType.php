<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GseType extends Model
{
    protected $table = 'gse_types';

    protected $fillable = [
        'service_name',
        'service_rate',
        'charge_type',
    ];

    protected $casts = [
        'service_rate' => 'decimal:2',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice_gse::class, 'gse_type_id');
    }
}
