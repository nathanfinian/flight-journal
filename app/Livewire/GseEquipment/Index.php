<?php

namespace App\Livewire\GseEquipment;

use App\Models\GseEquipment;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('gseequipment.edit', ['id' => $id], navigate: true);
    }

    public function render()
    {
        $equipment = GseEquipment::query()
            ->with([
                'gseType:id,type_name',
                'branch:id,name',
            ])
            ->withCount('stockMovements')
            ->orderBy('equipment_code')
            ->paginate($this->perPage);

        return view('livewire.gse-equipment.index', compact('equipment'));
    }
}
