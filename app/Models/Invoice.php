<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected function dateFromFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->dateFrom?->format('d F Y')
        );
    }

    protected function dateToFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->dateTo?->format('d F Y')
        );
    }

    protected function flightRange(): Attribute
    {
        return Attribute::make(
            get: function () {

                if (! $this->dateFrom || ! $this->dateTo) {
                    return null;
                }

                $from = Carbon::parse($this->dateFrom);
                $to   = Carbon::parse($this->dateTo);

                // Same day
                if ($from->isSameDay($to)) {
                    return $from->format('j F Y');
                }

                // Same month & year
                if ($from->isSameMonth($to) && $from->isSameYear($to)) {
                    return $from->format('j') . ' – ' .
                        $to->format('j F Y');
                }

                // Same year, different month
                if ($from->isSameYear($to)) {
                    return $from->format('j F') . ' – ' .
                        $to->format('j F Y');
                }

                // Different year
                return $from->format('j F Y') . ' – ' .
                    $to->format('j F Y');
            }
        );
    }
}
