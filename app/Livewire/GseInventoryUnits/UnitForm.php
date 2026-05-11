<?php

namespace App\Livewire\GseInventoryUnits;

use App\Models\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UnitForm extends Component
{
    public ?int $unitId = null;
    public string $unit_name = '';
    public string $unit_symbol = '';
    public bool $isEdit = false;

    public function mount(?int $id = null): void
    {
        if ($id === null) {
            return;
        }

        $unit = Unit::query()->findOrFail($id);

        $this->isEdit = true;
        $this->unitId = $unit->getKey();
        $this->unit_name = (string) $unit->unit_name;
        $this->unit_symbol = (string) $unit->unit_symbol;
    }

    protected function rules(): array
    {
        return [
            'unit_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('units', 'unit_name')->ignore($this->unitId, 'unit_id'),
            ],
            'unit_symbol' => ['required', 'string', 'max:10'],
        ];
    }

    public function saveChanges()
    {
        $payload = $this->validate();

        if ($this->unitId) {
            Unit::query()
                ->whereKey($this->unitId)
                ->update($payload);
        } else {
            Unit::query()->create($payload);
        }

        session()->flash('notify', [
            'content' => 'Tipe satuan berhasil disimpan!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('gseunits', navigate: true);
    }

    public function delete()
    {
        $row = Unit::query()->find($this->unitId);
        $name = $row?->unit_name ?? 'Unknown';

        if (! $row) {
            session()->flash('notify', [
                'content' => 'Tipe satuan tidak ditemukan',
                'type' => 'error',
            ]);

            return;
        }

        try {
            if ($row->items()->exists()) {
                session()->flash('notify', [
                    'content' => 'Tipe satuan ini masih dipakai oleh barang dan tidak dapat dihapus.',
                    'type' => 'warning',
                ]);

                return;
            }

            $row->delete();

            session()->flash('notify', [
                'content' => 'Tipe satuan ' . $name . ' berhasil dihapus!',
                'type' => 'success',
            ]);

            return $this->redirectRoute('gseunits', navigate: true);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Tipe satuan ini masih dipakai dan tidak dapat dihapus.',
                    'type' => 'warning',
                ]);

                return;
            }

            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.gse-inventory-units.unit-form');
    }
}
