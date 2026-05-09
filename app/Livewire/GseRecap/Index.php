<?php

namespace App\Livewire\GseRecap;

use App\Models\Airline;
use App\Models\Branch;
use App\Models\GseRecap;
use App\Models\GseType;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    public ?string $dateFrom = '';
    public ?string $dateTo = '';

    public $recaps;

    public $branches = [];
    public $airlines = [];
    public $gseTypes = [];

    public ?string $branchName = '';
    public ?string $selectedBranch = '';
    public ?string $airlineName = '';
    public ?string $selectedAirline = '';
    public ?string $selectedGseType = '';

    public function mount()
    {
        $start = now('Asia/Jakarta');
        $this->dateFrom = $start->startOfMonth()->toDateString();
        $this->dateTo = today('Asia/Jakarta')->toDateString();

        $this->branches = Branch::orderBy('name')->get(['id', 'name']);
        $this->airlines = Airline::orderBy('name')->get(['id', 'name']);
        $this->gseTypes = GseType::orderBy('type_name')->get(['id', 'type_name']);

        $this->loadInvoices();
    }

    public function updatedSelectedBranch($value)
    {
        $this->loadInvoices();
    }

    public function updatedSelectedAirline($value)
    {
        $this->loadInvoices();
    }

    public function updatedSelectedGseType($value)
    {
        $this->loadInvoices();
    }

    public function updatedDateFrom($value)
    {
        $this->loadInvoices();
    }

    public function updatedDateTo($value)
    {
        $this->loadInvoices();
    }

    protected function loadInvoices()
    {
        [$from, $to] = $this->normalizedDates();

        $this->recaps = GseRecap::query()
            ->select([
                'id',
                'gse_type_id',
                'branch_id',
                'airline_id',
                'equipment_id',
                'service_date',
                'flight_number',
                'er_number',
                'operator_name',
            ])
            ->with([
                'gseType:id,type_name',
                'branch:id,name',
                'airline:id,name',
                'equipment:id,registration,aircraft_id',
                'equipment.aircraft:id,type_name',
            ])
            ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
            ->when($this->selectedAirline, fn($q) => $q->where('airline_id', $this->selectedAirline))
            ->when($this->selectedGseType, fn($q) => $q->where('gse_type_id', $this->selectedGseType))
            ->when($from && $to, fn($q) => $q->whereBetween('service_date', [$from, $to]))
            ->when($from && ! $to, fn($q) => $q->whereDate('service_date', '>=', $from))
            ->when(! $from && $to, fn($q) => $q->whereDate('service_date', '<=', $to))
            ->orderByDesc('er_number')
            ->get();

        $this->branchName = Branch::where('id', $this->selectedBranch)->value('name');
        $this->airlineName = Airline::where('id', $this->selectedAirline)->value('name');
    }

    protected function normalizedDates(): array
    {
        $from = $this->dateFrom ? Carbon::parse($this->dateFrom, 'Asia/Jakarta')->toDateString() : null;
        $to = $this->dateTo ? Carbon::parse($this->dateTo, 'Asia/Jakarta')->toDateString() : null;

        if ($from && $to && $to < $from) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }

    public function clearDates(): void
    {
        $this->reset(['dateFrom', 'dateTo']);
    }

    public function generateInvoice()
    {
        return redirect()->route('rekapgse.create');
    }

    public function openEdit(int $id)
    {
        return $this->redirectRoute('rekapgse.edit', ['id' => $id], navigate: true);
    }

    public function render()
    {
        return view('livewire.gse-recap.index');
    }
}
