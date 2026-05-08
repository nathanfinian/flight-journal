<?php

namespace App\Livewire\Forms;

use App\Models\Invoice_gse as GseInvoice;
use App\Models\GseType;
use Illuminate\Support\Collection;
use Livewire\Form;

class GseInvoiceForm extends Form
{
    public const COMBINED_GPU_ATT = 'gpu_att';

    public ?GseInvoice $record = null;

    public string $invoice_number = '';
    public string $gse_type_id = '';
    public string $branch_id = '';
    public string $airline_id = '';
    public string $toWhom = '';
    public string $toTitle = '';
    public string $toCompany = '';
    public string $signer_name = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function setInvoice(GseInvoice $invoice): void
    {
        $this->record = $invoice;
        $this->invoice_number = $invoice->invoice_number;
        $this->gse_type_id = (string) $invoice->gse_type_id;
        $this->branch_id = (string) $invoice->branch_id;
        $this->airline_id = (string) $invoice->airline_id;
        $this->toWhom = (string) $invoice->toWhom;
        $this->toTitle = (string) $invoice->toTitle;
        $this->toCompany = (string) $invoice->toCompany;
        $this->signer_name = (string) $invoice->signer_name;
        $this->dateFrom = $invoice->dateFrom?->format('Y-m-d') ?? '';
        $this->dateTo = $invoice->dateTo?->format('Y-m-d') ?? '';
    }

    public function rules(): array
    {
        return [
            'invoice_number' => ['required', 'string', 'max:80', 'unique:gse_invoices,invoice_number,' . $this->record?->id],
            'gse_type_id' => ['required', $this->gseTypeRule()],
            'branch_id' => ['required', 'exists:branches,id'],
            'airline_id' => ['required', 'exists:airlines,id'],
            'toWhom' => ['required', 'string', 'max:255'],
            'toTitle' => ['required', 'string', 'max:255'],
            'toCompany' => ['required', 'string', 'max:255'],
            'signer_name' => ['required', 'string', 'max:255'],
            'dateFrom' => ['required', 'date'],
            'dateTo' => ['required', 'date', 'after_or_equal:dateFrom'],
        ];
    }

    public function headerRules(): array
    {
        // Used before loading recaps, when the invoice number may not be ready yet.
        return [
            'gse_type_id' => ['required', $this->gseTypeRule()],
            'branch_id' => ['required', 'exists:branches,id'],
            'airline_id' => ['required', 'exists:airlines,id'],
            'dateFrom' => ['required', 'date'],
            'dateTo' => ['required', 'date', 'after_or_equal:dateFrom'],
        ];
    }

    public function persist(): GseInvoice
    {
        $validated = $this->validate();
        $validated['gse_type_id'] = $this->storageGseTypeId($validated['gse_type_id']);
        $validated['toWhom'] = $validated['toWhom'] ?: null;
        $validated['toTitle'] = $validated['toTitle'] ?: null;
        $validated['toCompany'] = $validated['toCompany'] ?: null;
        $validated['signer_name'] = $validated['signer_name'] ?: null;

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
            'toWhom',
            'toTitle',
            'toCompany',
            'signer_name',
            'dateFrom',
            'dateTo',
        ]);
    }

    private function gseTypeRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if ($value === self::COMBINED_GPU_ATT) {
                if ($this->hasGpuAndAttTypes()) {
                    return;
                }

                $fail('The selected GSE type is invalid.');

                return;
            }

            if (! GseType::query()->whereKey($value)->exists()) {
                $fail('The selected GSE type is invalid.');
            }
        };
    }

    private function storageGseTypeId(string $gseTypeId): string
    {
        if ($gseTypeId !== self::COMBINED_GPU_ATT) {
            return $gseTypeId;
        }

        return (string) $this->combinedGpuAttTypeIds()->first();
    }

    private function combinedGpuAttTypeIds(): Collection
    {
        return GseType::query()
            ->where(function ($query): void {
                $query
                    ->whereRaw('LOWER(service_name) LIKE ?', ['%gpu%'])
                    ->orWhereRaw('LOWER(service_name) LIKE ?', ['%att%']);
            })
            ->orderByRaw("CASE WHEN LOWER(service_name) LIKE '%gpu%' THEN 0 ELSE 1 END")
            ->pluck('id');
    }

    private function hasGpuAndAttTypes(): bool
    {
        $serviceNames = GseType::query()
            ->where(function ($query): void {
                $query
                    ->whereRaw('LOWER(service_name) LIKE ?', ['%gpu%'])
                    ->orWhereRaw('LOWER(service_name) LIKE ?', ['%att%']);
            })
            ->pluck('service_name')
            ->map(fn (string $serviceName): string => strtolower($serviceName));

        return $serviceNames->contains(fn (string $serviceName): bool => str_contains($serviceName, 'gpu'))
            && $serviceNames->contains(fn (string $serviceName): bool => str_contains($serviceName, 'att'));
    }
}
