<?php

namespace App\Livewire\Invoice;

use App\Livewire\Forms\InvoiceForm;
use App\Models\Airline;
use App\Models\AirlineRate;
use App\Models\Branch;
use App\Models\Invoice;
use App\Traits\GeneratesInvoiceNumber;
use Livewire\Component;

class Create extends Component
{
    use GeneratesInvoiceNumber;

    public InvoiceForm $form;

    public $ground_fee;
    public $cargo_fee;

    public $invoice;

    public $branches;
    public $airlines;
    public $flightTypesPercent;
    public $rates;

    public bool $isEdit = false;

    /* =======================
     | Lifecycle
     ======================= */
    public function mount(?int $id = null)
    {
        if (session()->has('invoice')) {
            $this->form->branch_id  = session('invoice.branch', 1);
            $this->form->dateFrom   = session('invoice.from');
            $this->form->dateTo     = session('invoice.to');

            //Use trait to automatically generate invoice number
            if (empty($this->form->invoice_number) && $this->form->branch_id != null) {
                $this->form->invoice_number = $this->generateInvoiceNumber((int) $this->form->branch_id);
            }
            
            // 🔥 Clear session after extraction
            session()->forget('invoice');
        }

        //Form Setup
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

        //Check for Edit Mode or not
        if ($id == null) {
            $this->form->date = now()->format('Y-m-d');

            return; // go to create, when no data retrieved
        } else {
            $this->isEdit = true;
            $this->invoice = Invoice::findOrFail($id);

            $this->form->setInvoice($this->invoice);

            $this->updatedFormAirlineRatesId($this->invoice->airline_rates_id);
        }
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
        if ($this->form->record) {
            $invoice = $this->form->update()->invoice_number;
        } else {
            $invoice = $this->form->store()->invoice_number;
        }

        session()->flash('notify', [
            'content' => $invoice . ' berhasil disave',
            'type'    => 'success',
        ]);

        return $this->redirectRoute('invoice', navigate: true);
    }

    /* =======================
     | Delete
     ======================= */
    public function delete()
    {
        $this->form->delete();

        session()->flash('notify', [
            'content' => 'Invoice berhasil dihapus!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('invoice');
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

        $counter = 0;

        foreach ($rate->flightTypes as $type) {
            $this->flightTypesPercent[$counter] = [
                'flight_type_id' => $type->id,
                'typeName' => $type->name,
                'percentage' => number_format($type->pivot->percentage),
            ];
            $counter++;
        }

        $this->ground_fee = number_format($rate->ground_fee); //Ground handling fee
        $this->cargo_fee  = number_format($rate->cargo_fee);  //Cargo handling fee

        if ($rate) {
            $this->form->airline_id = (string) $rate->airline_id;
        }
    }

    /* =======================
     | Recall Trait on Branch Change
     ======================= */
    public function updatedFormBranchId($value): void
    {
        $this->form->invoice_number = $this->generateInvoiceNumber((int) $this->form->branch_id);
    }

    public function render()
    {
        return view('livewire.invoice.create');
    }
}
