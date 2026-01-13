<?php

namespace App\Traits;

use App\Models\Invoice;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

trait GeneratesInvoiceNumber
{
    protected function generateInvoiceNumber(int $branchId): string
    {
        return DB::transaction(function () use ($branchId) {

            $branch = Branch::findOrFail($branchId);
            $branchCode = strtoupper($branch->airport->iata ?? $branch->id);

            $period = now()->format('Ym');

            $last = Invoice::where('branch_id', $branchId)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $next = $last
                ? (int) substr($last->invoice_number, -6) + 1
                : 1;

            return sprintf(
                'INV-%s-%s-%06d',
                $branchCode,
                $period,
                $next
            );
        });
    }
}
