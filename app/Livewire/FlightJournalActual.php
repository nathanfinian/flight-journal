<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Flight;
use App\Models\Airline;
use Livewire\Component;

class FlightJournalActual extends Component
{
    public $actualFlights;

    public $branches = [];
    public $airlines = [];

    public ?string $selectedBranch = '';
    public ?string $selectedAirline = '';

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
        $this->loadActualFlights();
    }

    public function updatedSelectedBranch($value)
    {
        $this->loadActualFlights();
    }

    public function updatedSelectedAirline($value)
    {
        $this->loadActualFlights();
    }

    protected function loadActualFlights()
    {
        $today = Carbon::today('Asia/Jakarta');

        $this->actualFlights = Flight::query()
            ->with([
                'branch:id,name',
                'originEquipment:id,registration',
                'departureEquipment:id,registration',
                'originAirlineRoute.airline:id,name',
                'originAirlineRoute.airportRoute.origin:id,iata',
                'originAirlineRoute.airportRoute.destination:id,iata',
                'departureAirlineRoute.airportRoute.origin:id,iata',
                'departureAirlineRoute.airportRoute.destination:id,iata',
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
            ->where('service_date', $today)
            ->orderBy('branch_id')
            ->orderBy('sched_dep')
            ->get();
    }

    public function openEdit(int $id)
    {
        //Change edit routes
        return $this->redirectRoute('flight-journal.edit', ['id' => $id, 'type' => 'actual'], navigate: true);
    }

    public function render()
    {
        return view('livewire.flight-journal-actual');
    }
}


