<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class SubCategory extends Model
{
    use SoftDeletes;

    protected $table = 'sub_categories';

    protected $primaryKey = 'sub_category_id';

    protected $fillable = [
        'category_id',
        'sub_category_name',
        'status',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
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
