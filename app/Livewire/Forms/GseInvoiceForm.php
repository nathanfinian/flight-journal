<?php

namespace App\Livewire\Forms;

use App\Models\Invoice_gse as GseInvoice;
use Livewire\Form;

class GseInvoiceForm extends Form
{
    public ?GseInvoice $record = null;

    public string $invoice_number = '';
    public string $gse_type_id = '';
    public string $branch_id = '';
    public string $airline_id = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function setInvoice(GseInvoice $invoice): void
    {
        $this->record = $invoice;
        $this->invoice_number = $invoice->invoice_number;
        $this->gse_type_id = (string) $invoice->gse_type_id;
        $this->branch_id = (string) $invoice->branch_id;
        $this->airline_id = (string) $invoice->airline_id;
        $this->dateFrom = $invoice->dateFrom?->format('Y-m-d') ?? '';
        $this->dateTo = $invoice->dateTo?->format('Y-m-d') ?? '';
    }

    public function rules(): array
    {
        return [
            'invoice_number' => ['required', 'string', 'max:80', 'unique:gse_invoices,invoice_number,' . $this->record?->id],
            'gse_type_id' => ['required', 'exists:gse_types,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'airline_id' => ['required', 'exists:airlines,id'],
            'dateFrom' => ['required', 'date'],
            'dateTo' => ['required', 'date', 'after_or_equal:dateFrom'],
        ];
    }

    public function headerRules(): array
    {
        // Used before loading recaps, when the invoice number may not be ready yet.
        return [
            'gse_type_id' => ['required', 'exists:gse_types,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'airline_id' => ['required', 'exists:airlines,id'],
            'dateFrom' => ['required', 'date'],
            'dateTo' => ['required', 'date', 'after_or_equal:dateFrom'],
        ];
    }

    public function persist(): GseInvoice
    {
        $validated = $this->validate();

        // Keep create and edit paths behind one method so the component can sync
        // recap pivot rows after the invoice header has been saved.
        if ($this->record) {
            $this->record->update($validated);

            return $this->record->fresh();
        }

        $this->record = GseInvoice::query()->create($validated);

        return $this->record;
    }

    public function delete(): void
    {
        if (! $this->record) {
            throw new \RuntimeException('No GSE invoice record selected for deletion.');
        }

        $this->record->delete();
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'record',
            'invoice_number',
            'gse_type_id',
            'branch_id',
            'airline_id',
            'dateFrom',
            'dateTo',
        ]);
    }
}
