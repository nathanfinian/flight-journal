<?php

namespace App\Http\Controllers\Export;

use App\Exports\GseInventoryTransactionHistoryExport;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GseInventoryTransactionHistoryExportController extends Controller
{
    public function export(Request $request)
    {
        $filters = $this->filters($request);

        return Excel::download(
            new GseInventoryTransactionHistoryExport(
                $request->dateFrom,
                $request->dateTo,
                $request->branch_id,
                $request->sub_category_id,
                $request->item_id,
                $filters['branchName'],
                $filters['subCategoryLabel'],
                $filters['itemLabel'],
            ),
            $this->documentName($request, $filters['branchName'], $filters['subCategoryLabel'], $filters['itemLabel'])
        );
    }

    public function print(Request $request)
    {
        return view('print.gse-inventory-transaction-history', $this->filters($request) + [
            'movements' => $this->movements($request),
            'dateFrom' => $request->dateFrom,
            'dateTo' => $request->dateTo,
        ]);
    }

    private function filters(Request $request): array
    {
        $branchName = Branch::query()
            ->whereKey($request->branch_id)
            ->value('name');

        $subCategory = SubCategory::query()
            ->with('category:category_id,category_name')
            ->whereKey($request->sub_category_id)
            ->first();

        $subCategoryLabel = $subCategory
            ? ($subCategory->category?->category_name ?? '-') . ' - ' . $subCategory->sub_category_name
            : null;

        $item = Item::query()
            ->with([
                'subCategory' => fn ($query) => $query
                    ->withTrashed()
                    ->with('category:category_id,category_name'),
            ])
            ->whereKey($request->item_id)
            ->first();

        $itemLabel = $item
            ? ($item->subCategory?->category?->category_name ?? '-') . ' - ' . ($item->subCategory?->sub_category_name ?? '-') . ' - ' . $item->name
            : null;

        return [
            'branchName' => $branchName,
            'subCategoryLabel' => $subCategoryLabel,
            'itemLabel' => $itemLabel,
        ];
    }

    private function movements(Request $request)
    {
        $from = $request->dateFrom ? Carbon::parse($request->dateFrom, 'Asia/Jakarta')->startOfDay() : null;
        $to = $request->dateTo ? Carbon::parse($request->dateTo, 'Asia/Jakarta')->endOfDay() : null;

        return StockMovement::query()
            ->with([
                'branch:id,name',
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
            ->when(filled($request->branch_id), fn ($query) => $query->where('branch_id', $request->branch_id))
            ->when(filled($request->item_id), fn ($query) => $query->where('item_id', $request->item_id))
            ->when(filled($request->sub_category_id), function ($query) use ($request): void {
                $query->whereHas('item', fn ($itemQuery) => $itemQuery
                    ->withTrashed()
                    ->where('sub_category_id', $request->sub_category_id));
            })
            ->when($from && $to, fn ($query) => $query->whereBetween('movement_date', [$from, $to]))
            ->when($from && ! $to, fn ($query) => $query->where('movement_date', '>=', $from))
            ->when(! $from && $to, fn ($query) => $query->where('movement_date', '<=', $to))
            ->orderByDesc('movement_date')
            ->orderByDesc('movement_id')
            ->get();
    }

    private function documentName(Request $request, ?string $branchName, ?string $subCategoryLabel, ?string $itemLabel): string
    {
        $name = implode(' ', array_filter([
            'GSE Inventory Transaction History',
            $branchName,
            $subCategoryLabel,
            $itemLabel,
            $this->dateRange($request),
        ]));

        return preg_replace('/[\\\\\/:*?"<>|]+/', '-', $name . '.xlsx') ?? 'GSE Inventory Transaction History.xlsx';
    }

    private function dateRange(Request $request): ?string
    {
        if ($request->dateFrom && $request->dateTo) {
            return $request->dateFrom . ' sampai ' . $request->dateTo;
        }

        if ($request->dateFrom) {
            return 'Mulai ' . $request->dateFrom;
        }

        if ($request->dateTo) {
            return 'Sampai ' . $request->dateTo;
        }

        return null;
    }
}
