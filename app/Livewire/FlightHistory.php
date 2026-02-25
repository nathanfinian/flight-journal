<?php

namespace App\Livewire;

use Carbon\Carbon;

use App\Models\Branch;
use App\Models\Flight;
use App\Models\Airline;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class FlightHistory extends Component
{
    public ?string $dateFrom = ''; // 'YYYY-MM-DD'
    public ?string $dateTo   = '';

    public $actualFlights;

    public $branches = [];
    public $airlines = [];

    public ?string $branchName = '';
    public ?string $selectedBranch = '';
    public ?string $airlineName = '';
    public ?string $selectedAirline = '';
    public ?string $flightNo = '';

    public function mount()
    {
        //Date start from the start of the month
        $start = now('Asia/Jakarta');
        $this->dateFrom = $start->startOfMonth()->toDateString();

        $today = today('Asia/Jakarta')->toDateString(); // "2025-11-09"
        $this->dateTo = $today;

        // Load filters
        $this->branches = Branch::orderBy('name')->get(['id', 'name']);
        $this->airlines = Airline::orderBy('name')->get(['id', 'name']);

        $this->selectedBranch = Auth::user()->branch_id;

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

    public function updatedFlightNo($value)
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
            ->when($this->flightNo, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('origin_flight_no', 'like', '%' . $this->flightNo . '%')
                        ->orWhere('departure_flight_no', 'like', '%' . $this->flightNo . '%');
                });
            })
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
            ->orderBy('service_date', 'asc')
            ->orderBy('actual_arr', 'asc')
            ->get();

            $this->branchName = Branch::where('id', $this->selectedBranch)
                                ->value('name');
            $this->airlineName = Airline::where('id', $this->selectedAirline)
                                ->value('name');
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

    public function generateInvoice()
    {
        //Set session data for invoice setup
        session([
            'invoice.branch'  => $this->selectedBranch,
            'invoice.from'    => $this->dateFrom,
            'invoice.to'      => $this->dateTo,
        ]);

        return redirect()->route('invoice.create');
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
