<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GseTypeRate extends Model
{
    protected $table = 'gse_type_rates';

    protected $fillable = [
        'airline_id',
        'branch_id',
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

    public function airline(): BelongsTo
    {
        return $this->belongsTo(Airline::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeForRecap(Builder $query, GseRecap $recap): Builder
    {
        return $query
            ->whereNotNull('airline_id')
            ->whereNotNull('branch_id')
            ->where('airline_id', $recap->airline_id)
            ->where('branch_id', $recap->branch_id)
            ->where('gse_type_id', $recap->gse_type_id)
            ->whereDate('effective_from', '<=', $recap->service_date)
            ->where(fn (Builder $query) => $query->whereNull('effective_to')
                ->orWhereDate('effective_to', '>=', $recap->service_date));
    }

    public function scopeOverlappingContract(Builder $query, array $contract): Builder
    {
        return $query
            ->where('gse_type_id', $contract['gse_type_id'])
            ->where('airline_id', $contract['airline_id'])
            ->where('branch_id', $contract['branch_id'])
            ->when($contract['effective_to'], fn (Builder $query) =>
                $query->whereDate('effective_from', '<=', $contract['effective_to']))
            ->where(fn (Builder $query) => $query->whereNull('effective_to')
                ->orWhereDate('effective_to', '>=', $contract['effective_from']));
    }
}
