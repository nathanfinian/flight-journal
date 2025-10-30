<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ScheduledFlights;

class FlightSchedule extends Component
{
    public $flights;

    public function mount()
    {
        $this->flights = ScheduledFlights::query()
            ->with([
                'branch:id,name',
                'airlineRoute.airline:id,name',
                'airlineRoute.airportRoute.origin:id,iata',
                'airlineRoute.airportRoute.destination:id,iata',
                'days:id,day_name'
            ])
            ->orderBy('branch_id')
            ->orderBy('sched_dep')
            ->get();
    }

    public function openEdit(int $id)
    {
        return $this->redirectRoute('flight-schedule.edit', ['scheduled' => $id], navigate: true);
    }

    public function render()
    {
        return view('livewire.flight-schedule');
    }
}
