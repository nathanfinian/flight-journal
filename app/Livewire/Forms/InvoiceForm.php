<?php

namespace App\Livewire\Forms;

use App\Models\Invoice;
use Livewire\Attributes\Validate;
use Livewire\Form;

class InvoiceForm extends Form
{
    public ?Invoice $record = null;

    #[Validate('required|string|min:3|max:80')]
    public string $title = '';

    #[Validate('required|string|min:3|max:80')]
    public string $invoice_number = '';

    #[Validate('required|exists:airline_rates,id')]
    public string $airline_rates_id = '';

    #[Validate('required|exists:airlines,id')]
    public string $airline_id = '';

    #[Validate('required|exists:branches,id')]
    public string $branch_id = '';

    #[Validate('required|date')]
    public string $date = '';

    #[Validate('required|date')]
    public string $due_date = '';

    #[Validate('required|date')]
    public string $dateFrom = '';

    #[Validate('required|date|after_or_equal:dateFrom')]
    public string $dateTo = '';

    #[Validate('nullable|numeric|min:0')]
    public string $total_amount = '';

    /* =======================
     | Fill form for edit
     ======================= */
    public function setInvoice(Invoice $invoice): void
    {
        $this->record         = $invoice;
        $this->title          = $invoice->title;
        $this->invoice_number = $invoice->invoice_number;
        $this->airline_rates_id = (string) $invoice->airline_rates_id;
        $this->airline_id     = (string) $invoice->airline_id;
        $this->branch_id      = (string) $invoice->branch_id;
        $this->date           = $invoice->date?->format('Y-m-d');
        $this->due_date       = $invoice->due_date?->format('Y-m-d');
        $this->dateFrom       = $invoice->dateFrom?->format('Y-m-d');
        $this->dateTo         = $invoice->dateTo?->format('Y-m-d');
        $this->total_amount   = (string) $invoice->total_amount;
    }

    /* =======================
     | Create invoice
     ======================= */
    public function store(): Invoice
    {
        $this->validate();

        return Invoice::create([
            'title'          => $this->title,
            'invoice_number' => $this->invoice_number,
            'airline_rates_id'=> $this->airline_rates_id,
            'airline_id'     => $this->airline_id,
            'branch_id'      => $this->branch_id,
            'date'           => $this->date,
            'due_date'       => $this->due_date,
            'dateFrom'       => $this->dateFrom,
            'dateTo'         => $this->dateTo,
            'total_amount'   => $this->total_amount ?: 0,
        ]);
    }

    /* =======================
     | Update invoice
     ======================= */
    public function update(): Invoice
    {
        if (! $this->record) {
            throw new \RuntimeException('Invoice record not set');
        }

        // $this->validate();
        $this->validate([
            'invoice_number' => 'required|string|min:3|max:80|unique:invoices,invoice_number,' . $this->record->id,
        ]);

        $this->record->update([
            'title'          => $this->title,
            'invoice_number' => $this->invoice_number,
            'airline_rates_id'=> $this->airline_rates_id,
            'airline_id'     => $this->airline_id,
            'branch_id'      => $this->branch_id,
            'date'           => $this->date,
            'due_date'       => $this->due_date,
            'dateFrom'       => $this->dateFrom,
            'dateTo'         => $this->dateTo,
            'total_amount'   => $this->total_amount ?: 0,
        ]);

        return $this->record;
    }

    /* =======================
    | Delete record
    ======================= */
    public function delete(): void
    {
        if (!$this->record) {
            throw new \Exception('No record selected for deletion.');
        }

        $this->record->delete();

        $this->reset();
    }

    /* =======================
     | Reset form
     ======================= */
    public function resetForm(): void
    {
        $this->reset([
            'record',
            'title',
            'invoice_number',
            'airline_rates_id',
            'airline_id',
            'branch_id',
            'date',
            'due_date',
            'dateFrom',
            'dateTo',
            'total_amount',
        ]);
    }
}
