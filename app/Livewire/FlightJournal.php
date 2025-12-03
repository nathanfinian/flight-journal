<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Airline;
use Livewire\Component;
use App\Models\ScheduledFlights;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FlightJournal extends Component
{
    public $flights;

    public $branches = [];
    public $airlines = [];

    public ?string $selectedBranch = '';
    public ?string $selectedAirline = '';

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

        $this->selectedBranch = Auth::user()->branch_id;

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
                'originAirlineRoute.airline:id,name',
                'originAirportRoute.origin:id,iata',
                'originAirportRoute.destination:id,iata',
                'departureAirportRoute.origin:id,iata',
                'departureAirportRoute.destination:id,iata',
            ])
            ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
            ->when(
                $this->selectedAirline,
                fn($q) =>
                $q->whereHas(
                    'originAirlineRoute',
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
                    ->from('actual_flights')
                    ->whereColumn('actual_flights.origin_flight_no', 'scheduled_flights.origin_flight_no')
                    ->whereDate('actual_flights.service_date', $today);
            })
            ->orderBy('branch_id')
            ->orderBy('sched_arr', 'asc')
            ->get();
    }

    public function openEdit(int $id)
    {
        return $this->redirectRoute('flight-journal.edit', ['id' => $id, 'type' => 'scheduled'], navigate: true);
    }

    public function render()
    {
        return view('livewire.flight-journal');
    }
}
