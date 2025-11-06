<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Airline;
use Livewire\Component;
use App\Models\ScheduledFlights;
use Illuminate\Support\Facades\DB;

class FlightJournal extends Component
{
    public $flights;

    public $branches = [];
    public $airlines = [];

    public ?int $selectedBranch = null;
    public ?int $selectedAirline = null;

    public string $activeTab = 'scheduled';

    public string $hari;

    public $indoDays = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    public function mount()
    {
        $this->hari = $this->indoDays[Carbon::now()->dayOfWeekIso];
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
        $today = Carbon::today('Asia/Jakarta');
        
        $this->flights = ScheduledFlights::query()
            ->with([
                'branch:id,name',
                'equipment:id,registration',
                'airlineRoute.airline:id,name',
                'airlineRoute.airportRoute.origin:id,iata',
                'airlineRoute.airportRoute.destination:id,iata',
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
            ->whereHas(
                'days',
                fn($d) =>
                $d->where('dow', $today->dayOfWeekIso)
            )
            ->whereNotExists(function ($sq) use ($today) {
                $sq->select(DB::raw(1))
                    ->from('flights')
                    ->whereColumn('flights.flight_no', 'scheduled_flights.flight_no')
                    // add this if you want same route too:
                    // ->whereColumn('flights.airline_route_id', 'scheduled_flights.airline_route_id')
                    ->whereDate('flights.service_date', $today);
            })
            ->orderBy('branch_id')
            ->orderBy('sched_dep')
            ->get();
    }

    public function openEdit(int $id)
    {
        return $this->redirectRoute('flight-journal.edit', ['actual' => $id], navigate: true);
    }

    public function render()
    {
        return view('livewire.flight-journal');
    }
}
