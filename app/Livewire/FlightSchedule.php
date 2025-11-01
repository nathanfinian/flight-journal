<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Airline;
use Livewire\Component;
use App\Models\ScheduledFlights;

class FlightSchedule extends Component
{
    public $flights;

    public $branches = [];
    public $airlines = [];

    public ?int $selectedBranch = null;
    public ?int $selectedAirline = null;

    public function mount()
    {
        // Load filters
        $this->branches = Branch::orderBy('name')->get(['id', 'name']);
        $this->airlines = Airline::orderBy('name')->get(['id', 'name']);

        // Load all flights initially
        $this->loadFlights();
    }

    public function updatedSelectedBranch($value)
    {
        $this->loadFlights();
    }

    public function updatedSelectedAirline($value)
    {
        $this->loadFlights();
    }

    protected function loadFlights()
    {
        $this->flights = ScheduledFlights::query()
            ->with([
                'branch:id,name',
                'equipment:id,registration',
                'airlineRoute.airline:id,name',
                'airlineRoute.airportRoute.origin:id,iata',
                'airlineRoute.airportRoute.destination:id,iata',
                'days:id,day_name',
            ])
            ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
            ->when(
                $this->selectedAirline,
                fn($q) =>
                $q->whereHas(
                    'airlineRoute',
                    fn($r) =>
                    $r->where('airline_id', $this->selectedAirline)
                )
            )
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
