<?php

namespace App\Livewire\GseInventoryUnits;

use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('gseunits.edit', ['id' => $id], navigate: true);
    }

    public function render()
    {
        $units = Unit::query()
            ->withCount('items')
            ->orderBy('unit_name')
            ->paginate($this->perPage);

        return view('livewire.gse-inventory-units.index', compact('units'));
    }
}
