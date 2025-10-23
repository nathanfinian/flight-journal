<?php

namespace App\Livewire;

use App\Models\Branch;
use Livewire\Component;
use App\Models\Equipment;
use App\Models\AirlineRoute;

class FlightScheduleModify extends Component
{
    public $branches;
    public $equipments;
    public $flightRoute;

    public function mount()
    {
        // Load choices
        $this->branches = Branch::query()
            ->where('status', 'ACTIVE')   // filter by status
            ->orderBy('name')     // or ->orderBy('name')
            ->get(['id', 'name']);

        // Load choices
        $this->equipments = Equipment::query()
            ->with('airline:id,name')
            ->where('status', 'ACTIVE')   // filter by status
            ->orderBy('airline_id', 'asc')
            ->get(['id', 'registration', 'airline_id']);

        // Load airline routes with related data
        $airlineRoutes = AirlineRoute::query()
            ->with([
                'airline:id,name',
                'airportRoute.origin:id,iata',
                'airportRoute.destination:id,iata',
            ])
            ->get(['id', 'airline_id', 'airport_route_id']);

        // Map to flight route options
        $this->flightRoute = $airlineRoutes->mapWithKeys(function ($ar) {
            $airlineName = $ar->airline->name ?? 'Unknown Airline';
            $originIata = $ar->airportRoute->origin->iata ?? '---';
            $destIata = $ar->airportRoute->destination->iata ?? '---';

            return [
                $ar->id => "{$originIata} ➜ {$destIata} - {$airlineName}",
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.flight-schedule-modify');
    }
}
