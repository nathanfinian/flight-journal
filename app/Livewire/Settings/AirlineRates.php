<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\AirlineRate;
use App\Models\Airline;
use App\Models\Branch;
use Livewire\WithPagination;

class AirlineRates extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public $branches = [];
    public $airlines = [];
    public ?string $selectedBranch = '';
    public ?string $selectedAirline = '';

    public function mount(): void
    {
        $this->branches = Branch::orderBy('name')->get(['id', 'name']);
        $this->airlines = Airline::orderBy('name')->get(['id', 'name']);
    }

    public function updatedSelectedBranch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedAirline(): void
    {
        $this->resetPage();
    }

    public function openEdit(int $id)
    {
        return $this->redirectRoute('settings.airlineRates.edit', ['airlineRate' => $id], navigate: true);
    }

    public function render()
    {
        $airlineRates = AirlineRate::query()
            ->with(['airline:id,name'])
            ->when($this->selectedBranch, fn ($query) => $query->where('branch_id', $this->selectedBranch))
            ->when($this->selectedAirline, fn ($query) => $query->where('airline_id', $this->selectedAirline))
            ->orderBy('charge_name', 'asc')
            ->paginate($this->perPage); // change to ->get() to disable pagination

        return view('livewire.settings.airline-rates', [
            'airlineRates' => $airlineRates
        ]);
    }
}
