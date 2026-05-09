<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class GseType extends Model
{
    protected $table = 'gse_types';

    protected $fillable = [
        'type_name',
        'description',
        'created_by',
        'updated_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            $model->created_by ??= Auth::id();
            $model->updated_by ??= Auth::id();
        });

        static::updating(function (self $model): void {
            $model->updated_by = Auth::id();
        });
    }

    public function recaps(): HasMany
    {
        return $this->hasMany(GseRecap::class, 'gse_type_id');
    }

    public function rates(): HasMany
    {
        return $this->hasMany(GseTypeRate::class, 'gse_type_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice_gse::class, 'gse_type_id');
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(GseEquipment::class, 'gse_type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
