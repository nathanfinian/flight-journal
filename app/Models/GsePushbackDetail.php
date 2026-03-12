<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GsePushbackDetail extends Model
{
    protected $table = 'gse_pushback_details';

    protected $fillable = [
        'gse_recap_id',
        'start_ps',
        'end_ps',
        'ata',
        'atd',
    ];

    public function recap(): BelongsTo
    {
        return $this->belongsTo(GseRecap::class, 'gse_recap_id');
    }
}
