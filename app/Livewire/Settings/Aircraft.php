<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Aircraft as AircraftModel;

class Aircraft extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('settings.aircraft.edit', ['aircraft' => $id], navigate: true);
    }

    public function render()
    {
        $aircraft = AircraftModel::query()
            ->orderBy('icao_code', 'asc')
            ->paginate($this->perPage); // change to ->get() to disable pagination

        return view('livewire.settings.aircraft', compact('aircraft'));
    }
}
