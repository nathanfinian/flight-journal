<?php

namespace App\Traits;

use App\Models\GseType;
use App\Models\Invoice_gse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait GeneratesGseInvoiceNumber
{
    protected function generateGseInvoiceNumber(int $gseTypeId): string
    {
        return DB::transaction(function () use ($gseTypeId) {
            $gseType = GseType::query()->findOrFail($gseTypeId);
            $typeCode = $this->makeGseTypeCode($gseType->type_name, $gseType->getKey());
            $period = now()->format('Ym');

            $last = Invoice_gse::query()
                ->where('gse_type_id', $gseTypeId)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $next = $last
                ? ((int) substr($last->invoice_number, -6)) + 1
                : 1;

            return sprintf('INV-GSE-%s-%s-%06d', $typeCode, $period, $next);
        });
    }

    protected function makeGseTypeCode(string $serviceName, int $fallbackId): string
    {
        $words = preg_split('/[^A-Za-z0-9]+/', Str::upper($serviceName), -1, PREG_SPLIT_NO_EMPTY);

        if (! empty($words)) {
            $initials = collect($words)
                ->map(fn (string $word): string => Str::substr($word, 0, 1))
                ->implode('');

            return Str::substr($initials, 0, 6);
        }

        return 'TYPE' . $fallbackId;
    }
}
