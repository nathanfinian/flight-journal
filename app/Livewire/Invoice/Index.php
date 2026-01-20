<?php

namespace App\Livewire\Invoice;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Airline;
use App\Models\Invoice;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public ?string $dateFrom = ''; // 'YYYY-MM-DD'
    public ?string $dateTo   = '';

    public $invoices;

    public $branches = [];
    public $airlines = [];

    public ?string $branchName = '';
    public ?string $selectedBranch = '';
    public ?string $airlineName = '';
    public ?string $selectedAirline = '';

    public function mount()
    {
        $today = today('Asia/Jakarta')->toDateString(); // "2025-11-09"

        $this->dateFrom = $today;
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

        $this->invoices = Invoice::query()
            ->with([
                'branch:id,name',
                'airline:id,callsign',
                'rate:id,charge_name',
            ])
            ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
            // dates
            ->when(
                $from && $to,
                fn($q) =>
                $q->whereBetween('date', [$from, $to])
            )
            ->when(
                $from && !$to,
                fn($q) =>
                $q->whereDate('date', '>=', $from)
            )
            ->when(
                !$from && $to,
                fn($q) =>
                $q->whereDate('date', '<=', $to)
            )
            ->orderBy('date', 'asc')
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

    public function render()
    {
        return view('livewire.invoice.index');
    }
}
