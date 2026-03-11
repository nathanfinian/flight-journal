<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GseGpuDetail extends Model
{
    protected $table = 'gse_gpu_details';

    protected $fillable = [
        'gse_invoice_id',
        'start_time',
        'end_time',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice_gse::class, 'gse_invoice_id');
    }
}
