<?php

namespace App\Livewire\GseInventoryTransactions;

use App\Models\Branch;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public string $branch_id = '';
    public string $sub_category_id = '';
    public string $item_id = '';
    public ?string $dateFrom = '';
    public ?string $dateTo = null;

    public function mount(): void
    {
        $today = today('Asia/Jakarta');

        $this->dateFrom = $today->copy()->startOfMonth()->toDateString();
        $this->dateTo = $today->toDateString();
    }

    public function updatedBranchId(): void
    {
        $this->resetPage();
    }

    public function updatedSubCategoryId(): void
    {
        $this->item_id = '';
        $this->resetPage();
    }

    public function updatedItemId(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['branch_id', 'sub_category_id', 'item_id']);

        $today = today('Asia/Jakarta');
        $this->dateFrom = $today->copy()->startOfMonth()->toDateString();
        $this->dateTo = $today->toDateString();

        $this->resetPage();
    }

    public function export()
    {
        return $this->redirectRoute('gsetransactions.history.export', [
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'branch_id' => $this->branch_id,
            'sub_category_id' => $this->sub_category_id,
            'item_id' => $this->item_id,
        ]);
    }

    public function printExport()
    {
        return route('gsetransactions.history.print', [
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'branch_id' => $this->branch_id,
            'sub_category_id' => $this->sub_category_id,
            'item_id' => $this->item_id,
        ]);
    }

    public function render()
    {
        return view('livewire.gse-inventory-transactions.history', [
            'branches' => $this->branches(),
            'subCategories' => $this->subCategories(),
            'items' => $this->items(),
            'movements' => $this->movements(),
        ]);
    }

    private function branches(): Collection
    {
        return Branch::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function subCategories(): Collection
    {
        return SubCategory::query()
            ->with('category:category_id,category_name')
            ->where('sub_categories.status', 'ACTIVE')
            ->join('categories', 'categories.category_id', '=', 'sub_categories.category_id')
            ->orderBy('categories.category_name')
            ->orderBy('sub_categories.sub_category_name')
            ->get(['sub_categories.sub_category_id', 'sub_categories.category_id', 'sub_categories.sub_category_name']);
    }

    private function items(): Collection
    {
        return Item::query()
            ->with('subCategory.category:category_id,category_name')
            ->where('items.status', 'ACTIVE')
            ->join('sub_categories', 'sub_categories.sub_category_id', '=', 'items.sub_category_id')
            ->join('categories', 'categories.category_id', '=', 'sub_categories.category_id')
            ->when(filled($this->sub_category_id), fn ($query) => $query->where('items.sub_category_id', $this->sub_category_id))
            ->orderBy('categories.category_name')
            ->orderBy('sub_categories.sub_category_name')
            ->orderBy('items.name')
            ->get(['items.item_id', 'items.name', 'items.sub_category_id', 'sub_categories.sub_category_name', 'categories.category_name']);
    }

    private function movements(): LengthAwarePaginator
    {
        $from = $this->dateFrom ? Carbon::parse($this->dateFrom, 'Asia/Jakarta')->startOfDay() : null;
        $to = $this->dateTo ? Carbon::parse($this->dateTo, 'Asia/Jakarta')->endOfDay() : null;

        return StockMovement::query()
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
            ->when(filled($this->branch_id), fn ($query) => $query->where('branch_id', $this->branch_id))
            ->when(filled($this->item_id), fn ($query) => $query->where('item_id', $this->item_id))
            ->when(filled($this->sub_category_id), function ($query): void {
                $query->whereHas('item', fn ($itemQuery) => $itemQuery
                    ->withTrashed()
                    ->where('sub_category_id', $this->sub_category_id));
            })
            ->when($from && $to, fn ($query) => $query->whereBetween('movement_date', [$from, $to]))
            ->when($from && ! $to, fn ($query) => $query->where('movement_date', '>=', $from))
            ->when(! $from && $to, fn ($query) => $query->where('movement_date', '<=', $to))
            ->orderByDesc('movement_date')
            ->orderByDesc('movement_id')
            ->paginate($this->perPage);
    }
}
