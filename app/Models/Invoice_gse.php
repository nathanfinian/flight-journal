<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Invoice_gse extends Model
{
    use SoftDeletes;

    protected $table = 'gse_invoices';

    protected $fillable = [
        'gse_type_id',
        'branch_id',
        'airline_id',
        'toWhom',
        'toTitle',
        'toCompany',
        'signer_name',
        'invoice_number',
        'dateFrom',
        'dateTo',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'dateFrom' => 'date',
        'dateTo' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

        static::deleting(function ($model) {
            if (! $model->isForceDeleting()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });
    }

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function recaps(): BelongsToMany
    {
        return $this->belongsToMany(GseRecap::class, 'gse_invoice_recap', 'gse_invoice_id', 'gse_recap_id')
            ->withPivot(['id', 'gse_type_rate_id', 'charge_type', 'service_rate', 'quantity', 'amount'])
            ->withTimestamps();
    }

    public function invoiceRecaps()
    {
        return $this->hasMany(GseInvoiceRecap::class, 'gse_invoice_id');
    }

    protected function dateRange(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->dateFrom || ! $this->dateTo) {
                    return null;
                }

                if ($this->dateFrom->isSameDay($this->dateTo)) {
                    return $this->dateFrom->format('j F Y');
                }

                return $this->dateFrom->format('j F Y') . ' - ' . $this->dateTo->format('j F Y');
            }
        );
    }
}
