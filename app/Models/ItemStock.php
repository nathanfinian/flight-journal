<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemStock extends Model
{
    protected $table = 'item_stocks';

    protected $primaryKey = 'item_stock_id';

    public const CREATED_AT = null;

    public const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'item_id',
        'branch_id',
        'quantity',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
