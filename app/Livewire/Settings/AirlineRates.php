<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\AirlineRate;
use Livewire\WithPagination;

class AirlineRates extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('settings.airlineRates.edit', ['airlineRate' => $id], navigate: true);
    }

    public function render()
    {
        $airlineRates = AirlineRate::query()
            ->with(['airline:id,name'])
            ->orderBy('charge_name', 'asc')
            ->paginate($this->perPage); // change to ->get() to disable pagination

        return view('livewire.settings.airline-rates', [
            'airlineRates' => $airlineRates
        ]);
    }
}
