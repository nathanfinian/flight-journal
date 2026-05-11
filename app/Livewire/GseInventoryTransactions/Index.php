<?php

namespace App\Livewire\GseInventoryTransactions;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Item;
use App\Models\SubCategory;
use App\Models\StockMovement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public string $category_id = '';
    public string $sub_category_id = '';
    public string $branch_id = '';
    public string $search = '';

    public function openEdit(int $id)
    {
        return $this->redirectRoute('gsetransactions.edit', ['id' => $id], navigate: true);
    }

    public function updatedCategoryId(): void
    {
        $this->sub_category_id = '';
        $this->resetPage();
    }

    public function updatedSubCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedBranchId(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['category_id', 'sub_category_id', 'branch_id', 'search']);
        $this->resetPage();
    }

    public function render()
    {
        $branches = $this->branches();
        $items = $this->items();

        return view('livewire.gse-inventory-transactions.index', [
            'branches' => $branches,
            'categories' => $this->categories(),
            'subCategories' => $this->subCategories(),
            'visibleBranches' => $this->visibleBranches($branches),
            'items' => $items,
            'recentMovements' => $this->recentMovementsFor(collect($items->items())->pluck('item_id')),
        ]);
    }

    private function branches(): Collection
    {
        return Branch::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function visibleBranches(Collection $branches): Collection
    {
        if (blank($this->branch_id)) {
            return $branches;
        }

        return $branches
            ->where('id', (int) $this->branch_id)
            ->values();
    }

    private function categories(): Collection
    {
        return Category::query()
            ->where('status', 'ACTIVE')
            ->orderBy('category_name')
            ->get(['category_id', 'category_name']);
    }

    private function subCategories(): Collection
    {
        return SubCategory::query()
            ->where('sub_categories.status', 'ACTIVE')
            ->when(filled($this->category_id), fn ($query) => $query->where('category_id', $this->category_id))
            ->orderBy('sub_category_name')
            ->get(['sub_category_id', 'category_id', 'sub_category_name']);
    }

    private function items(): LengthAwarePaginator
    {
        return Item::query()
            ->select('items.*')
            ->with([
                'subCategory.category:category_id,category_name',
                'unit:unit_id,unit_name,unit_symbol',
                'stocks' => fn ($query) => $query
                    ->when(filled($this->branch_id), fn ($stockQuery) => $stockQuery->where('branch_id', $this->branch_id))
                    ->with('branch:id,name'),
            ])
            ->join('sub_categories', 'sub_categories.sub_category_id', '=', 'items.sub_category_id')
            ->join('categories', 'categories.category_id', '=', 'sub_categories.category_id')
            ->when(filled($this->category_id), fn ($query) => $query->where('sub_categories.category_id', $this->category_id))
            ->when(filled($this->sub_category_id), fn ($query) => $query->where('items.sub_category_id', $this->sub_category_id))
            ->when(filled($this->branch_id), fn ($query) => $query->whereHas('stocks', fn ($stockQuery) => $stockQuery->where('branch_id', $this->branch_id)))
            ->when(filled($this->search), fn ($query) => $query->where('items.name', 'like', '%' . $this->search . '%'))
            ->orderBy('categories.category_name')
            ->orderBy('sub_categories.sub_category_name')
            ->orderBy('items.name')
            ->paginate($this->perPage);
    }

    private function recentMovementsFor(Collection $itemIds): Collection
    {
        if ($itemIds->isEmpty()) {
            return collect();
        }

        $rankedMovements = StockMovement::query()
            ->select('movement_id')
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY item_id ORDER BY movement_date DESC, movement_id DESC) as movement_rank')
            ->whereIn('item_id', $itemIds);

        return StockMovement::query()
            ->with(['branch:id,name', 'creator:id,name'])
            ->joinSub($rankedMovements, 'ranked_movements', function ($join): void {
                $join->on('ranked_movements.movement_id', '=', 'stock_movements.movement_id');
            })
            ->where('ranked_movements.movement_rank', '<=', 5)
            ->orderBy('stock_movements.item_id')
            ->orderByDesc('stock_movements.movement_date')
            ->orderByDesc('stock_movements.movement_id')
            ->get(['stock_movements.*'])
            ->groupBy('item_id');
    }
}
