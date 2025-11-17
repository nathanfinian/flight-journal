<?php

namespace App\Livewire\Settings;

use App\Models\Airline;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Equipment as EquipmentModel;

class Equipment extends Component
{
    use WithPagination;

    private int $perPage = 10;

    public $airlines = [];

    public ?string $selectedAirline = '';

    public function mount()
    {
        // Load filters
        $this->airlines = Airline::orderBy('name')->get(['id', 'name']);
    }

    public function updatedSelectedAirline($value)
    {
        $this->resetPage(); // reset pagination when filtering
    }

    public function openEdit(int $id)
    {
        return $this->redirectRoute('settings.equipment.edit', ['equipment' => $id], navigate: true);
    }

    public function render()
    {
        $equipments = EquipmentModel::query()
            ->with([
                'aircraft:id,type_name',
                'airline:id,name',
            ])
            ->join('airlines', 'equipments.airline_id', '=', 'airlines.id')

            // 🔥 Filter by selected airline
            ->when($this->selectedAirline, function ($q) {
                $q->where('equipments.airline_id', $this->selectedAirline);
            })

            // 🔥 Order by airline name
            ->orderBy('airlines.name', 'asc')

            ->select('equipments.*') // avoid ambiguity
            ->paginate($this->perPage);
            
        return view('livewire.settings.equipment', compact('equipments'));
    }
}
