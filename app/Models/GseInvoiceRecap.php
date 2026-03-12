<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GseInvoiceRecap extends Model
{
    protected $table = 'gse_invoice_recap';

    protected $fillable = [
        'gse_invoice_id',
        'gse_recap_id',
        'gse_type_rate_id',
        'charge_type',
        'service_rate',
        'quantity',
        'amount',
    ];

    protected $casts = [
        'service_rate' => 'decimal:2',
        'quantity' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice_gse::class, 'gse_invoice_id');
    }

    public function recap(): BelongsTo
    {
        return $this->belongsTo(GseRecap::class, 'gse_recap_id');
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(GseTypeRate::class, 'gse_type_rate_id');
    }
}
