<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GsePushbackDetail extends Model
{
    protected $table = 'gse_pushback_details';

    protected $fillable = [
        'gse_invoice_id',
        'start_ps',
        'end_ps',
        'ata',
        'atd',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice_gse::class, 'gse_invoice_id');
    }
}
