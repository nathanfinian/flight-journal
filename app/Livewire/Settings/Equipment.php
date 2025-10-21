<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Equipment as EquipmentModel;

class Equipment extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('settings.equipment.edit', ['equipment' => $id], navigate: true);
    }

    public function render()
    {
        $equipment = EquipmentModel::query()
            ->with([
                'aircraft:id,type_name',
                'airline:id,name',
            ])
            ->orderBy('id', 'asc')
            ->paginate($this->perPage); // change to ->get() to disable pagination
            
        return view('livewire.settings.equipment', compact('equipment'));
    }
}
