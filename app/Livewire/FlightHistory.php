<?php

namespace App\Livewire;

use Livewire\Component;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Flight;
use App\Models\Airline;

class FlightHistory extends Component
{
    public ?string $dateFrom = ''; // 'YYYY-MM-DD'
    public ?string $dateTo   = '';

    public $actualFlights;

    public $branches = [];
    public $airlines = [];

    public ?string $selectedBranch = '';
    public ?string $selectedAirline = '';

    public function mount()
    {
        $today = today('Asia/Jakarta')->toDateString(); // "2025-11-09"

        $this->dateFrom = $today;
        $this->dateTo = $today;
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

    public function updatedDateFrom($value)
    {
        $this->loadActualFlights();
    }

    public function updatedDateTo($value)
    {
        $this->loadActualFlights();
    }

    protected function loadActualFlights()
    {
        [$from, $to] = $this->normalizedDates();

        $this->actualFlights = Flight::query()
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
            // dates
            ->when(
                $from && $to,
                fn($q) =>
                $q->whereBetween('service_date', [$from, $to])
            )
            ->when(
                $from && !$to,
                fn($q) =>
                $q->whereDate('service_date', '>=', $from)
            )
            ->when(
                !$from && $to,
                fn($q) =>
                $q->whereDate('service_date', '<=', $to)
            )
            ->orderBy('branch_id')
            ->orderBy('sched_dep')
            ->get();
    }

    protected function normalizedDates(): array
    {
        $from = $this->dateFrom ? Carbon::parse($this->dateFrom, 'Asia/Jakarta')->toDateString() : null;
        $to   = $this->dateTo   ? Carbon::parse($this->dateTo,   'Asia/Jakarta')->toDateString() : null;

        if ($from && $to && $to < $from) {
            [$from, $to] = [$to, $from]; // auto-fix reversed input
        }

        return [$from, $to];
    }

    public function clearDates(): void
    {
        $this->reset(['dateFrom', 'dateTo']);
    }

    public function openEdit(int $id)
    {
        //Change edit routes
        return $this->redirectRoute('flight-journal.edit', ['id' => $id, 'type' => 'history'], navigate: true);
    }

    public function render()
    {
        return view('livewire.flight-history');
    }
}
