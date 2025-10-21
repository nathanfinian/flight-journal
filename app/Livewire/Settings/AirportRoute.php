<?php

namespace App\Livewire\Settings;

use App\Models\AirportRoute as AirportRouteModel;
use Livewire\Component;
use Livewire\WithPagination;

class AirportRoute extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('settings.airport-route.edit', ['route' => $id], navigate: true);
    }
    
    public function render()
    {
        $routes = AirportRouteModel::query()
            ->with([
                'origin:id,iata,city',
                'destination:id,iata,city',
            ])
            ->orderBy('id', 'asc')
            ->paginate($this->perPage); // change to ->get() to disable pagination

        return view('livewire.settings.airport-route', compact('routes'));
    }
}
