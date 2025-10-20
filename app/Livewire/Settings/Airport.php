<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Airport as AirportModel;

class Airport extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('settings.airport.edit', ['airport' => $id], navigate: true);
    }

    public function render()
    {
        $airports = AirportModel::query()
            ->orderBy('city', 'asc')
            ->paginate($this->perPage); // change to ->get() to disable pagination

        return view('livewire.settings.airport', compact('airports'));
    }
}
