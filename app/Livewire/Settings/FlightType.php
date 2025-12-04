<?php

namespace App\Livewire\Settings;

use App\Models\FlightType as ModelsFlightType;
use Livewire\Component;
use Livewire\WithPagination;

class FlightType extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('settings.flight-type.edit', ['typeId' => $id], navigate: true);
    }

    public function render()
    {
        $types = ModelsFlightType::query()
            ->with('createdBy:id,name')
            ->orderBy('name', 'asc')
            ->paginate($this->perPage); // change to ->get() to disable pagination

        return view('livewire.settings.flight-type', compact('types'));
    }
}

