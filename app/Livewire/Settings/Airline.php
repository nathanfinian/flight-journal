<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Airline as AirlineModel;

class Airline extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('settings.airline.edit', ['airline' => $id], navigate: true);
    }

    public function render()
    {
        $airline = AirlineModel::query()
            ->orderBy('name', 'asc')
            ->paginate($this->perPage); // change to ->get() to disable pagination

        return view('livewire.settings.airline', compact('airline'));
    }
}
