<?php

namespace App\Livewire\GseInventoryItems;

use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('gseitems.edit', ['id' => $id], navigate: true);
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
            ->orderBy('code')
            ->paginate($this->perPage);

        return view('livewire.gse-inventory-items.index', compact('items'));
    }
}
