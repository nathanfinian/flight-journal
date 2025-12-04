<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\FlightType;
use Illuminate\Database\QueryException;

class FlightTypeModify extends Component
{
    // Route param (e.g. /settings/branch/{branch}/edit)
    public ?int $typeId = null;

    // Form fields
    public string  $name       = '';
    public string  $type_code       = '';

    public function mount(?int $typeId = null): void
    {
        if ($typeId) {
            $row = FlightType::find($typeId);
            if (!$row) {
                session()->flash('notify', [
                    'content' => 'Tipe flight tidak ditemukan!',
                    'type' => 'error'
                ]);
                return;
            }

            $this->typeId  = $row->getKey();
            $this->name       = (string) $row->name;
            $this->type_code  = (string) $row->type_code;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'type_code'   => ['required', 'string', 'max:4', 'unique:flight_types,type_code'],
        ];
    }

    public function saveChanges()
    {
        $data = $this->validate();

        $flightType = FlightType::updateOrCreate(
            ['id' => $this->typeId],
            $data
        );

        $this->typeId = $flightType->id;

        session()->flash('notify', [
            'content' => 'Flight Type berhasil disimpan!',
            'type' => 'success'
        ]);

        return $this->redirectRoute('settings.flight-type', navigate: true);
    }

    public function delete()
    {
        $row = FlightType::find($this->typeId);
        $name = $row?->name ?? 'Unknown';

        if (!$row) {
            session()->flash('notify', [
                'content' => 'Tipe flight not found',
                'type' => 'error'
            ]);
        }

        try {
            $row->delete();

            session()->flash('notify', [
                'content' => $name . ' berhasil dihapus!',
                'type' => 'success'
            ]);
            $this->redirectRoute('settings.flight-type', navigate: true);
        } catch (QueryException $e) {
            // 23000 => integrity constraint violation (FK in use, etc.)
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Tipe flight ini dipakai di jurnal penerbangan dan tidak dapat dihapus.',
                    'type' => 'warning'
                ]);
                return;
            }
            // Re-throw unexpected errors for visibility in logs
            throw $e;
        }
    }

    /** For headings/buttons in the view */
    public function getIsEditProperty(): bool
    {
        return (bool) $this->typeId;
    }

    public function render()
    {
        return view('livewire.settings.flight-type-modify');
    }
}
