<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class GseRecap extends Model
{
    use SoftDeletes;

    protected $table = 'gse_recaps';

    protected $fillable = [
        'gse_type_id',
        'branch_id',
        'airline_id',
        'service_date',
        'equipment_id',
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function airline(): BelongsTo
    {
        return $this->belongsTo(Airline::class, 'airline_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function gpuDetail(): HasOne
    {
        return $this->hasOne(GseGpuDetail::class, 'gse_recap_id');
    }

    public function pushbackDetail(): HasOne
    {
        return $this->hasOne(GsePushbackDetail::class, 'gse_recap_id');
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice_gse::class, 'gse_invoice_recap', 'gse_recap_id', 'gse_invoice_id')
            ->withPivot(['id', 'gse_type_rate_id', 'charge_type', 'service_rate', 'quantity', 'amount'])
            ->withTimestamps();
    }

    public function invoiceRecaps()
    {
        return $this->hasMany(GseInvoiceRecap::class, 'gse_recap_id');
    }
}
