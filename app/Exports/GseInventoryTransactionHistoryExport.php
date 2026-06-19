<?php

namespace App\Exports;

use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GseInventoryTransactionHistoryExport implements FromView
{
    public function __construct(
        private readonly ?string $dateFrom = '',
        private readonly ?string $dateTo = '',
        private readonly ?string $branchId = '',
        private readonly ?string $subCategoryId = '',
        private readonly ?string $itemId = '',
        private readonly ?string $branchName = null,
        private readonly ?string $subCategoryLabel = null,
        private readonly ?string $itemLabel = null,
    ) {
    }

    public function view(): View
    {
        $from = $this->dateFrom ? Carbon::parse($this->dateFrom, 'Asia/Jakarta')->startOfDay() : null;
        $to = $this->dateTo ? Carbon::parse($this->dateTo, 'Asia/Jakarta')->endOfDay() : null;

        $movements = StockMovement::query()
            ->with([
                'branch:id,name',
                'creator:id,name',
                'gseEquipment:gse_equipment_id,equipment_code,name',
                'item' => fn ($query) => $query
                    ->withTrashed()
                    ->with([
                        'unit:unit_id,unit_name,unit_symbol',
                        'subCategory' => fn ($subQuery) => $subQuery
                            ->withTrashed()
                            ->with('category:category_id,category_name'),
                    ]),
            ])
            ->when(filled($this->branchId), fn ($query) => $query->where('branch_id', $this->branchId))
            ->when(filled($this->itemId), fn ($query) => $query->where('item_id', $this->itemId))
            ->when(filled($this->subCategoryId), function ($query): void {
                $query->whereHas('item', fn ($itemQuery) => $itemQuery
                    ->withTrashed()
                    ->where('sub_category_id', $this->subCategoryId));
            })
            ->when($from && $to, fn ($query) => $query->whereBetween('movement_date', [$from, $to]))
            ->when($from && ! $to, fn ($query) => $query->where('movement_date', '>=', $from))
            ->when(! $from && $to, fn ($query) => $query->where('movement_date', '<=', $to))
            ->orderByDesc('movement_date')
            ->orderByDesc('movement_id')
            ->get();

        return view('exports.gse-inventory-transaction-history', [
            'movements' => $movements,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'branchName' => $this->branchName,
            'subCategoryLabel' => $this->subCategoryLabel,
            'itemLabel' => $this->itemLabel,
        ]);
    }
}
