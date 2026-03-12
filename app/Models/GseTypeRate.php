<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GseTypeRate extends Model
{
    protected $table = 'gse_type_rates';

    protected $fillable = [
        'gse_type_id',
        'effective_from',
        'effective_to',
        'charge_type',
        'service_rate',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'service_rate' => 'decimal:2',
    ];

    public function gseType(): BelongsTo
    {
        return $this->belongsTo(GseType::class, 'gse_type_id');
    }

    public function invoiceRecaps(): HasMany
    {
        return $this->hasMany(GseInvoiceRecap::class, 'gse_type_rate_id');
    }
}
