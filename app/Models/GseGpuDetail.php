<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GseGpuDetail extends Model
{
    protected $table = 'gse_gpu_details';

    protected $fillable = [
        'gse_recap_id',
        'start_time',
        'end_time',
    ];

    public function recap(): BelongsTo
    {
        return $this->belongsTo(GseRecap::class, 'gse_recap_id');
    }
}
