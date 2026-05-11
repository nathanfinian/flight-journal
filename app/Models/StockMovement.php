<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $table = 'stock_movements';

    protected $primaryKey = 'movement_id';

    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'branch_id',
        'gse_equipment_id',
        'movement_type',
        'quantity',
        'movement_date',
        'reference_no',
        'notes',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function gseEquipment(): BelongsTo
    {
        return $this->belongsTo(GseEquipment::class, 'gse_equipment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
