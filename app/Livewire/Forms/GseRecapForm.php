<?php

namespace App\Livewire\Forms;

use App\Models\GseRecap;
use App\Models\GseType;
use Illuminate\Support\Facades\DB;
use Livewire\Form;

class GseRecapForm extends Form
{
    public ?GseRecap $record = null;

    public string $gse_type_id = '';
    public string $branch_id = '';
    public string $airline_id = '';
    public string $service_date = '';
    public string $equipment_id = '';
    public string $flight_number = '';
    public string $er_number = '';
    public string $operator_name = '';
    public string $remarks = '';

    public string $start_time = '';
    public string $end_time = '';

    public string $start_ps = '';
    public string $end_ps = '';
    public string $ata = '';
    public string $atd = '';

    public function setRecap(GseRecap $recap): void
    {
        $this->record = $recap;
        $this->gse_type_id = (string) $recap->gse_type_id;
        $this->branch_id = (string) $recap->branch_id;
        $this->airline_id = (string) $recap->airline_id;
        $this->service_date = $recap->service_date?->format('Y-m-d') ?? '';
        $this->equipment_id = (string) $recap->equipment_id;
        $this->flight_number = (string) $recap->flight_number;
        $this->er_number = (string) $recap->er_number;
        $this->operator_name = (string) $recap->operator_name;
        $this->remarks = (string) $recap->remarks;

        $this->start_time = $this->formatTimeForInput(optional($recap->gpuDetail)->start_time);
        $this->end_time = $this->formatTimeForInput(optional($recap->gpuDetail)->end_time);

        $this->start_ps = (string) optional($recap->pushbackDetail)->start_ps;
        $this->end_ps = (string) optional($recap->pushbackDetail)->end_ps;
        $this->ata = (string) optional($recap->pushbackDetail)->ata;
        $this->atd = (string) optional($recap->pushbackDetail)->atd;
    }

    public function rules(): array
    {
        $recapId = $this->record?->id;
        $rules = [
            'gse_type_id' => ['required', 'exists:gse_types,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'airline_id' => ['required', 'exists:airlines,id'],
            'service_date' => ['required', 'date'],
            'equipment_id' => ['required', 'exists:equipments,id'],
            'flight_number' => ['required', 'string', 'max:10'],
            'er_number' => ['required', 'string', 'max:255', 'unique:gse_recaps,er_number' . ($recapId ? ',' . $recapId : '')],
            'operator_name' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ];

        return array_merge($rules, $this->detailRules());
    }

    public function store(): GseRecap
    {
        $validated = $this->validate();

        return DB::transaction(function () use ($validated) {
            $recap = GseRecap::create($this->basePayload($validated));
            $this->syncDetail($recap, $validated);

            return $recap->fresh(['gpuDetail', 'pushbackDetail']);
        });
    }

    public function update(): GseRecap
    {
        if (! $this->record) {
            throw new \RuntimeException('GSE recap record not set');
        }

        $validated = $this->validate();

        return DB::transaction(function () use ($validated) {
            $this->record->update($this->basePayload($validated));
            $this->syncDetail($this->record, $validated);

            return $this->record->fresh(['gpuDetail', 'pushbackDetail']);
        });
    }

    public function delete(): void
    {
        if (! $this->record) {
            throw new \RuntimeException('No GSE recap record selected for deletion.');
        }

        $this->record->delete();
        $this->resetForm();
    }

    public function resetDetailFields(): void
    {
        $this->reset([
            'start_time',
            'end_time',
            'start_ps',
            'end_ps',
            'ata',
            'atd',
        ]);
    }

    public function resetForm(): void
    {
        $this->reset([
            'record',
            'gse_type_id',
            'branch_id',
            'airline_id',
            'service_date',
            'equipment_id',
            'flight_number',
            'er_number',
            'operator_name',
            'remarks',
            'start_time',
            'end_time',
            'start_ps',
            'end_ps',
            'ata',
            'atd',
        ]);
    }

    private function basePayload(array $validated): array
    {
        return [
            'gse_type_id' => $validated['gse_type_id'],
            'branch_id' => $validated['branch_id'],
            'airline_id' => $validated['airline_id'],
            'service_date' => $validated['service_date'],
            'equipment_id' => $validated['equipment_id'],
            'flight_number' => $validated['flight_number'],
            'er_number' => $validated['er_number'],
            'operator_name' => $validated['operator_name'],
            'remarks' => $validated['remarks'] ?: null,
        ];
    }

    private function detailRules(): array
    {
        return match ($this->detailType()) {
            'gpu' => [
                'start_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
                'end_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            ],
            'pushback' => [
                'start_ps' => ['required', 'string', 'max:20'],
                'end_ps' => ['required', 'string', 'max:20'],
                'ata' => ['required', 'string', 'max:20'],
                'atd' => ['required', 'string', 'max:20'],
            ],
            default => [],
        };
    }

    private function syncDetail(GseRecap $recap, array $validated): void
    {
        $detailType = $this->detailType();

        if ($detailType === 'gpu') {
            $recap->gpuDetail()->updateOrCreate([], [
                'start_time' => $this->formatTimeForStorage($validated['start_time']),
                'end_time' => $this->formatTimeForStorage($validated['end_time']),
            ]);

            $recap->pushbackDetail()?->delete();
        } elseif ($detailType === 'pushback') {
            $recap->pushbackDetail()->updateOrCreate([], [
                'start_ps' => $validated['start_ps'],
                'end_ps' => $validated['end_ps'],
                'ata' => $validated['ata'],
                'atd' => $validated['atd'],
            ]);

            $recap->gpuDetail()?->delete();
        } else {
            $recap->gpuDetail()?->delete();
            $recap->pushbackDetail()?->delete();
        }
    }

    public function detailType(): ?string
    {
        if (! $this->gse_type_id) {
            return null;
        }

        $serviceName = (string) GseType::query()->whereKey($this->gse_type_id)->value('type_name');
        $normalized = strtolower($serviceName);

        if (str_contains($normalized, 'gpu')) {
            return 'gpu';
        }

        if (str_contains($normalized, 'att')) {
            return 'pushback';
        }

        return null;
    }

    private function formatTimeForInput(mixed $value): string
    {
        if (blank($value)) {
            return '';
        }

        $value = (string) $value;

        return preg_match('/^\d{2}:\d{2}:\d{2}$/', $value) === 1
            ? substr($value, 0, 5)
            : $value;
    }

    private function formatTimeForStorage(string $value): string
    {
        return preg_match('/^\d{2}:\d{2}$/', $value) === 1
            ? "{$value}:00"
            : $value;
    }
}
