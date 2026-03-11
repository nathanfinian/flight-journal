<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice_gse extends Model
{
    protected $table = 'gse_invoices';

    protected $fillable = [
        'gse_type_id',
        'service_date',
        'equipment_id',
        'invoice_number',
        'flight_number',
        'er_number',
        'operator_name',
        'remarks',
    ];

    protected $casts = [
        'service_date' => 'date',
    ];

    public function gseType(): BelongsTo
    {
        return $this->belongsTo(GseType::class, 'gse_type_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function gpuDetail(): HasOne
    {
        return $this->hasOne(GseGpuDetail::class, 'gse_invoice_id');
    }

    public function pushbackDetail(): HasOne
    {
        return $this->hasOne(GsePushbackDetail::class, 'gse_invoice_id');
    }
}
