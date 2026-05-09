<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class GseEquipment extends Model
{
    use SoftDeletes;

    protected $table = 'gse_equipment';

    protected $primaryKey = 'gse_equipment_id';

    protected $fillable = [
        'equipment_code',
        'gse_type_id',
        'name',
        'serial_number',
        'asset_number',
        'branch_id',
        'manufacture_year',
        'purchase_date',
        'total_hours_used',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_hours_used' => 'decimal:2',
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

    public function gseType(): BelongsTo
    {
        return $this->belongsTo(GseType::class, 'gse_type_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'gse_equipment_id');
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
