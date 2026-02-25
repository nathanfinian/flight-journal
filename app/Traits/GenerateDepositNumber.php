<?php

namespace App\Traits;

use App\Models\Branch;
use App\Models\Deposit;
use Illuminate\Support\Facades\DB;

trait GenerateDepositNumber
{
    protected function generateDepositNumber(int $branchId): string
    {
        return DB::transaction(function () use ($branchId) {

            $branch = Branch::findOrFail($branchId);
            $branchCode = strtoupper($branch->airport->iata ?? $branch->id);

            $period = now()->format('Ym');

            $last = Deposit::where('branch_id', $branchId)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $next = $last
                ? (int) substr($last->receipt_number, -6) + 1
                : 1;

            return sprintf(
                'DT-%s-%s-%06d',
                $branchCode,
                $period,
                $next
            );
        });
    }
}