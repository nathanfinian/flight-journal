<?php

namespace App\Livewire\Deposit;

use App\Models\Branch;
use App\Models\Deposit;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    public ?string $dateFrom = ''; // 'YYYY-MM-DD'
    public ?string $dateTo   = '';

    public $deposits;

    public $branches = [];

    public ?string $branchName = '';
    public ?string $selectedBranch = '';

    public function mount()
    {
        $start = now('Asia/Jakarta');
        $this->dateFrom = $start->startOfMonth()->toDateString();

        $today = today('Asia/Jakarta')->toDateString(); // "2025-11-09"
        $this->dateTo = $today;

        // Load filters
        $this->branches = Branch::orderBy('name')->get(['id', 'name']);

        $this->loadDeposits();
    }

    public function openEdit(int $id){
        //Change edit routes
        return $this->redirectRoute('deposit.edit', ['id' => $id], navigate: true);
    }

    public function generateInvoice()
    {
        //Set session data for invoice setup
        session([
            'deposit.branch'  => $this->selectedBranch,
        ]);

        return redirect()->route('deposit.create');
    }

    public function updatedSelectedBranch($value)
    {
        $this->loadDeposits();
    }

    public function updatedDateFrom($value)
    {
        $this->loadDeposits();
    }

    public function updatedDateTo($value)
    {
        $this->loadDeposits();
    }

    public function loadDeposits(){

        [$from, $to] = $this->normalizedDates();

        $this->deposits = Deposit::query()
            ->with([
                'branch:id,name',
            ])
            ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
            // dates
            ->when(
                $from && $to,
                fn($q) =>
                $q->whereBetween('receipt_date', [$from, $to])
            )
            ->when(
                $from && !$to,
                fn($q) =>
                $q->whereDate('receipt_date', '>=', $from)
            )
            ->when(
                !$from && $to,
                fn($q) =>
                $q->whereDate('receipt_date', '<=', $to)
            )
            ->orderBy('receipt_date', 'asc')
            ->get();

        $this->branchName = Branch::where('id', $this->selectedBranch)
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

    public function render()
    {
        return view('livewire.deposit.index');
    }
}
