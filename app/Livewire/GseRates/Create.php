<?php

namespace App\Livewire\GseRates;

use App\Models\Airline;
use App\Models\Branch;
use App\Models\GseType;
use App\Models\GseTypeRate;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    public $gseTypes;
    public $airlines;
    public $branches;

    public ?int $gseRateId = null;
    public ?string $gse_type_id = '';
    public ?string $airline_id = '';
    public ?string $branch_id = '';
    public ?string $effective_from = '';
    public ?string $effective_to = '';
    public ?string $charge_type = '';
    public ?string $service_rate = '';

    public bool $isEdit = false;

    public function mount(?int $id = null): void
    {
        $this->gseTypes = GseType::query()
            ->orderBy('type_name')
            ->get(['id', 'type_name']);
        $this->airlines = Airline::orderBy('name')->get(['id', 'name']);
        $this->branches = Branch::orderBy('name')->get(['id', 'name']);

        if ($id !== null) {
            $gseRate = GseTypeRate::query()->findOrFail($id);

            $this->isEdit = true;
            $this->gseRateId = $gseRate->getKey();
            $this->gse_type_id = (string) $gseRate->gse_type_id;
            $this->airline_id = (string) $gseRate->airline_id;
            $this->branch_id = (string) $gseRate->branch_id;
            $this->effective_from = $gseRate->effective_from?->format('Y-m-d');
            $this->effective_to = $gseRate->effective_to?->format('Y-m-d');
            $this->charge_type = (string) $gseRate->charge_type;
            $this->service_rate = number_format((float) $gseRate->service_rate, 2, ',', '.');
            return;
        }

        session()->flash('notify', [
            'content' => 'Buat rate GSE baru!',
            'type' => 'success',
        ]);
    }

    protected function rules(): array
    {
        return [
            'airline_id' => ['required', 'exists:airlines,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'gse_type_id' => ['required', 'exists:gse_types,id'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'charge_type' => ['required', 'in:HOURLY,PER_HANDLING'],
            'service_rate' => ['required', 'regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$|^\d+(,\d{1,2})?$/'],
        ];
    }

    public function updated(string $property): void
    {
        if (! array_key_exists($property, $this->rules())) {
            return;
        }

        $this->resetValidation($property);

        if (in_array($property, ['airline_id', 'branch_id', 'gse_type_id', 'effective_from', 'effective_to'], true)) {
            $this->resetValidation('effective_from');
        }

        if ($property === 'effective_from') {
            $this->resetValidation('effective_to');
        }
    }

    public function saveChanges()
    {
        $payload = $this->validate();
        $payload['service_rate'] = $this->toDecimal($this->service_rate);
        $payload['effective_to'] = $payload['effective_to'] ?: null;
        Validator::make($payload, [
            'service_rate' => ['numeric', 'between:0,9999999999.99'],
        ])->validate();

        $gseRate = DB::transaction(function () use ($payload) {
            // Serialize contract-period checks even when no matching rate exists yet.
            GseType::whereKey($payload['gse_type_id'])->lockForUpdate()->firstOrFail();

            $overlap = GseTypeRate::query()
                ->overlappingContract($payload)
                ->when($this->gseRateId, fn ($query) => $query->where('id', '!=', $this->gseRateId))
                ->exists();

            if ($overlap) {
                throw ValidationException::withMessages([
                    'effective_from' => 'Periode rate bertumpang tindih untuk airline, cabang, dan GSE type ini.',
                ]);
            }

            $rate = $this->gseRateId
                ? GseTypeRate::findOrFail($this->gseRateId)
                : new GseTypeRate;
            if ($rate->exists && $rate->invoiceRecaps()->exists()) {
                foreach (['gse_type_id', 'airline_id', 'branch_id'] as $field) {
                    $previous = $rate->getAttribute($field);
                    $changed = (string) $previous !== (string) $payload[$field];

                    // Legacy contract assignments can be filled without changing saved invoices.
                    if ($changed && ! ($previous === null && in_array($field, ['airline_id', 'branch_id'], true))) {
                        throw ValidationException::withMessages([
                            $field => 'Airline, cabang, dan GSE type pada rate yang sudah dipakai tidak dapat diganti. Buat rate baru untuk kontrak lain.',
                        ]);
                    }
                }
            }

            $rate->fill($payload)->save();

            return $rate;
        });

        $this->gseRateId = $gseRate->id;

        session()->flash('notify', [
            'content' => 'Rate GSE berhasil disimpan!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('rategse', navigate: true);
    }

    public function delete()
    {
        $row = GseTypeRate::find($this->gseRateId);
        $name = $row?->gseType?->type_name ?? 'Unknown';

        if (! $row) {
            session()->flash('notify', [
                'content' => 'Rate GSE tidak ditemukan',
                'type' => 'error',
            ]);
            return;
        }

        if ($row->invoiceRecaps()->exists()) {
            session()->flash('notify', [
                'content' => 'Rate GSE ini dipakai pada invoice recap dan tidak dapat dihapus.',
                'type' => 'warning',
            ]);
            return;
        }

        try {
            $row->delete();

            session()->flash('notify', [
                'content' => 'Rate ' . $name . ' berhasil dihapus!',
                'type' => 'success',
            ]);

            return $this->redirectRoute('rategse', navigate: true);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Rate GSE ini dipakai pada invoice recap dan tidak dapat dihapus.',
                    'type' => 'warning',
                ]);
                return;
            }

            throw $e;
        }
    }

    private function toDecimal($value)
    {
        return str_replace(['.', ','], ['', '.'], $value);
    }

    public function render()
    {
        return view('livewire.gse-rates.create');
    }
}
