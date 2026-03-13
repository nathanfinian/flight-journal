<?php

namespace App\Livewire\GseRates;

use App\Models\GseType;
use App\Models\GseTypeRate;
use Illuminate\Database\QueryException;
use Livewire\Component;

class Create extends Component
{
    public $gseTypes;

    public ?int $gseRateId = null;
    public ?string $gse_type_id = '';
    public ?string $effective_from = '';
    public ?string $effective_to = '';
    public ?string $charge_type = '';
    public ?string $service_rate = '';

    public bool $isEdit = false;

    public function mount(?GseTypeRate $gseRate = null): void
    {
        $this->gseTypes = GseType::query()
            ->orderBy('service_name')
            ->get(['id', 'service_name']);

        if ($gseRate) {
            $this->isEdit = true;
            $this->gseRateId = $gseRate->getKey();
            $this->gse_type_id = (string) $gseRate->gse_type_id;
            $this->effective_from = $gseRate->effective_from?->format('Y-m-d');
            $this->effective_to = $gseRate->effective_to?->format('Y-m-d');
            $this->charge_type = (string) $gseRate->charge_type;
            $this->service_rate = number_format((float) $gseRate->service_rate, 0, ',', '.');
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
            'gse_type_id' => ['required', 'exists:gse_types,id'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'charge_type' => ['required', 'in:HOURLY,PER_HANDLING'],
            'service_rate' => ['required', 'regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$|^\d+(,\d{1,2})?$/'],
        ];
    }

    public function saveChanges()
    {
        $payload = $this->validate();
        $payload['service_rate'] = $this->toDecimal($this->service_rate);

        $gseRate = GseTypeRate::updateOrCreate(
            ['id' => $this->gseRateId],
            $payload
        );

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
        $name = $row?->gseType?->service_name ?? 'Unknown';

        if (! $row) {
            session()->flash('notify', [
                'content' => 'Rate GSE tidak ditemukan',
                'type' => 'error',
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
