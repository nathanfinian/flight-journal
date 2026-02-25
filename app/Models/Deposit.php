<?php

namespace App\Models;

use App\Traits\Terbilang;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Deposit extends Model
{
    use SoftDeletes;
    use Terbilang;
    
    protected $table = 'deposit_receipts';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'received_from_company',
        'receipt_number',
        'branch_id',
        'signer_name',
        'receipt_date',    
        'description',    
        'value',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'receipt_date'  => 'date',
        'value'         => 'decimal:0',
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

    public function getTerbilangAttribute(): string
    {
        return $this->formatTerbilang($this->value) . ' Rupiah';
    }

    protected function dateFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->receipt_date?->format('d F Y')
        );
    }
}
