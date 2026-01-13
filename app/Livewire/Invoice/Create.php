<?php

namespace App\Livewire\Invoice;

use App\Traits\GeneratesInvoiceNumber;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Airline;
use App\Models\AirlineRate;
use App\Livewire\Forms\InvoiceForm;

class Create extends Component
{
    use GeneratesInvoiceNumber;

    public InvoiceForm $form;

    public $ground_fee;
    public $cargo_fee;

    public $branches;
    public $airlines;
    public $rates;

    /* =======================
     | Lifecycle
     ======================= */
    public function mount(): void
    {
        if (session()->has('invoice')) {
            $this->form->branch_id  = session('invoice.branch', 1);
            $this->form->dateFrom   = session('invoice.from');
            $this->form->dateTo     = session('invoice.to');

            if (empty($this->form->invoice_number) && $this->form->branch_id != null) {
                $this->form->invoice_number = $this->generateInvoiceNumber((int) $this->form->branch_id);
            }
            
            // 🔥 Clear session after extraction
            session()->forget('invoice');
        }

        $this->form->date = now()->format('Y-m-d');

        $this->branches = Branch::query()
            ->where('status', 'ACTIVE')
            ->orderBy('name')
            ->get();

        $this->airlines = Airline::query()
            ->where('status', 'ACTIVE')
            ->orderBy('name')
            ->get();

        $this->rates = AirlineRate::query()
            ->orderBy('charge_name')
            ->get();
        //Make helper to automatically generate invoice number
    }

    /* =======================
     | Validation
     ======================= */
    public function updated($field): void
    {
        $this->form->validateOnly($field);
    }

    /* =======================
     | Persist
     ======================= */
    public function saveChanges()
    {
        $invoice = $this->form->store()->invoice_number;

        session()->flash('notify', [
            'content' => $invoice . ' berhasil disave',
            'type'    => 'success',
        ]);

        return $this->redirectRoute('flight-history', navigate: true);
    }

    /* =======================
     | Reactive logic
     ======================= */
    public function updatedFormAirlineRatesId($value): void
    {
        if (! $value) {
            $this->form->airline_id = '';
            return;
        }

        $rate = AirlineRate::find($value);

        $this->ground_fee = number_format($rate->ground_fee); // Ground handling fee
        $this->cargo_fee  = number_format($rate->cargo_fee);  // Cargo handling fee


        if ($rate) {
            $this->form->airline_id = (string) $rate->airline_id;
        }
    }

    public function updatedFormBranchId($value): void
    {
        $this->form->invoice_number = $this->generateInvoiceNumber((int) $this->form->branch_id);
    }

    public function render()
    {
        return view('livewire.invoice.create');
    }
}
