<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Airline;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ScheduledFlights;
use Illuminate\Support\Facades\Auth;

class FlightSchedule extends Component
{
    use WithPagination;

    private int $perPage = 10;

    public $branches = [];
    public $airlines = [];

    public ?string $selectedBranch = '';
    public ?string $selectedAirline = '';

    public function mount()
    {
        // Load filters
        $this->branches = Branch::orderBy('name')->get(['id', 'name']);
        $this->airlines = Airline::orderBy('name')->get(['id', 'name']);

        $this->selectedBranch = Auth::user()->branch_id;
    }

    public function updatedSelectedBranch($value)
    {
        $this->resetPage(); // reset pagination when filtering
    }

    public function updatedSelectedAirline($value)
    {
        $this->resetPage(); // reset pagination when filtering
    }

    public function openEdit(int $id)
    {
        return $this->redirectRoute('flight-schedule.edit', ['scheduled' => $id], navigate: true);
    }

    public function render()
    {
        $flights = ScheduledFlights::query()
            ->with([
                'branch:id,name',
                'equipment:id,registration',
                'originAirlineRoute.airline:id,name',
                'originAirportRoute.origin:id,iata',
                'originAirportRoute.destination:id,iata',
                'departureAirportRoute.origin:id,iata',
                'departureAirportRoute.destination:id,iata',
                'days:id,day_name',
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
            ->orderBy('branch_id')
            ->orderBy('sched_arr', 'asc')
            ->paginate($this->perPage);
        
        return view('livewire.flight-schedule', [
            'flights' => $flights,
        ]);
    }
}
