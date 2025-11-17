<?php

namespace App\Livewire\Settings;

use App\Models\Airport;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AirportRoute as AirportRouteModel;

class AirportRoute extends Component
{
    use WithPagination;

    private int $perPage = 10;

    public $airports = [];

    public ?string $selectedAirport = '';

    public function mount()
    {
        // Load filters
        $this->airports = Airport::orderBy('iata')->get(['id', 'iata', 'city']);
    }

    public function updatedSelectedAirport($value)
    {
        $this->resetPage(); // reset pagination when filtering
    }

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
                'airlines:id,name,icao_code',
        ])
        ->join('airports as o', 'airport_routes.origin_id', '=', 'o.id')
        // 🔥 Filter by selected airline
        ->when($this->selectedAirport, function ($q) {
            $q->where(function ($sub) {
                $sub->where('airport_routes.origin_id', $this->selectedAirport)
                    ->orWhere('airport_routes.destination_id', $this->selectedAirport);
            });
        })
        ->orderBy('o.iata', 'asc')
        ->select('airport_routes.*')
        ->paginate($this->perPage); // change to ->get() to disable pagination

        return view('livewire.settings.airport-route', compact('routes'));
    }
}
