<?php

namespace App\Livewire\GseInventoryItems;

use App\Models\Item;
use App\Models\SubCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public string $search = '';
    public string $sub_category_id = '';
    public bool $canOpenEditPage = false;

    public function mount(): void
    {
        $this->canOpenEditPage = in_array(Auth::user()?->role?->name, ['admin', 'finance'], true);
    }

    public function openEdit(int $id)
    {
        if (! $this->canOpenEditPage) {
            return;
        }

        return $this->redirectRoute('gseitems.edit', ['id' => $id], navigate: true);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSubCategoryId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'sub_category_id']);
        $this->resetPage();
    }

    public function render()
    {
        $items = Item::query()
            ->with([
                'subCategory.category:category_id,category_name',
                'unit:unit_id,unit_name,unit_symbol',
                'stocks.branch:id,name',
            ])
            ->withSum('stocks', 'quantity')
            ->when(filled($this->search), fn ($query) => $query->where('items.name', 'like', '%' . $this->search . '%'))
            ->when(filled($this->sub_category_id), fn ($query) => $query->where('items.sub_category_id', $this->sub_category_id))
            ->orderBy('code')
            ->paginate($this->perPage);

        return view('livewire.gse-inventory-items.index', [
            'items' => $items,
            'subCategories' => $this->subCategories(),
        ]);
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
}
