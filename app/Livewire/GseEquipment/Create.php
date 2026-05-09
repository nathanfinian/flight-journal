<?php

namespace App\Livewire\GseEquipment;

use App\Models\Branch;
use App\Models\GseEquipment;
use App\Models\GseType;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public $gseTypes;
    public $branches;

    public ?int $gseEquipmentId = null;
    public string $equipment_code = '';
    public string $gse_type_id = '';
    public string $name = '';
    public string $serial_number = '';
    public string $asset_number = '';
    public string $branch_id = '';
    public string $manufacture_year = '';
    public string $purchase_date = '';
    public string $total_hours_used = '';
    public string $status = 'ACTIVE';
    public bool $isEdit = false;

    public function mount(?int $id = null): void
    {
        $this->gseTypes = GseType::query()
            ->orderBy('type_name')
            ->get(['id', 'type_name']);

        $this->branches = Branch::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($id === null) {
            return;
        }

        $equipment = GseEquipment::query()->findOrFail($id);

        $this->isEdit = true;
        $this->gseEquipmentId = $equipment->getKey();
        $this->equipment_code = (string) $equipment->equipment_code;
        $this->gse_type_id = (string) $equipment->gse_type_id;
        $this->name = (string) $equipment->name;
        $this->serial_number = (string) $equipment->serial_number;
        $this->asset_number = (string) $equipment->asset_number;
        $this->branch_id = (string) $equipment->branch_id;
        $this->manufacture_year = (string) $equipment->manufacture_year;
        $this->purchase_date = $equipment->purchase_date?->format('Y-m-d') ?? '';
        $this->total_hours_used = $equipment->total_hours_used !== null
            ? (string) (float) $equipment->total_hours_used
            : '';
        $this->status = (string) $equipment->status;
    }

    protected function rules(): array
    {
        return [
            'equipment_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('gse_equipment', 'equipment_code')->ignore($this->gseEquipmentId, 'gse_equipment_id'),
            ],
            'gse_type_id' => ['required', 'exists:gse_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'asset_number' => ['nullable', 'string', 'max:100'],
            'branch_id' => ['required', 'exists:branches,id'],
            'manufacture_year' => ['nullable', 'integer', 'digits:4', 'min:1900', 'max:' . ((int) date('Y') + 1)],
            'purchase_date' => ['nullable', 'date'],
            'total_hours_used' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'max:20'],
        ];
    }

    public function saveChanges()
    {
        $payload = $this->validate();
        $payload['serial_number'] = $payload['serial_number'] ?: null;
        $payload['asset_number'] = $payload['asset_number'] ?: null;
        $payload['manufacture_year'] = $payload['manufacture_year'] ?: null;
        $payload['purchase_date'] = $payload['purchase_date'] ?: null;
        $payload['total_hours_used'] = $payload['total_hours_used'] ?: null;

        if ($this->gseEquipmentId) {
            GseEquipment::query()
                ->whereKey($this->gseEquipmentId)
                ->update($payload);
        } else {
            GseEquipment::query()->create($payload);
        }

        session()->flash('notify', [
            'content' => 'Equipment GSE berhasil disimpan!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('gseequipment', navigate: true);
    }

    public function delete()
    {
        $row = GseEquipment::query()->find($this->gseEquipmentId);
        $name = $row?->equipment_code ?? 'Unknown';

        if (! $row) {
            session()->flash('notify', [
                'content' => 'Equipment GSE tidak ditemukan',
                'type' => 'error',
            ]);

            return;
        }

        try {
            $row->delete();

            session()->flash('notify', [
                'content' => 'Equipment GSE ' . $name . ' berhasil dihapus!',
                'type' => 'success',
            ]);

            return $this->redirectRoute('gseequipment', navigate: true);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Equipment GSE ini masih dipakai dan tidak dapat dihapus.',
                    'type' => 'warning',
                ]);

                return;
            }

            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.gse-equipment.create');
    }
}
