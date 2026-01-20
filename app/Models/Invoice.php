<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    use SoftDeletes;

    protected $table = 'invoices';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'title',
        'invoice_number',
        'date',
        'dateFrom',
        'dateTo',
        'airline_id',
        'branch_id',
        'airline_rates_id',
        'status',
        'due_date',
        'total_amount',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'date'       => 'date',
        'dateFrom'   => 'date',
        'dateTo'     => 'date',
        'due_date'   => 'date',
        'total_amount' => 'decimal:0',
    ];

    /* =======================
     | Model Events
     ======================= */
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

    /* =======================
     | Relationships
     ======================= */

    public function rate(): BelongsTo
    {
        return $this->belongsTo(AirlineRate::class, 'airline_rates_id');
    }

    public function airline(): BelongsTo
    {
        return $this->belongsTo(Airline::class, 'airline_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
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
}
