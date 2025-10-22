<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Aircraft;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class AircraftModify extends Component
{
    public ?int $aircraft = null;
    public ?int $typeId = null;

    public string $type_name = '';
    public ?string $icao_code = null;
    public ?string $iata_code = null;
    public ?int $seat_capacity = null;

    public function mount(?int $aircraft = null): void
    {
        if ($aircraft) {
            $aircraftData = Aircraft::find($aircraft);
            if (!$aircraftData) {
                // Handle gracefully instead of 404
                session()->flash('notify', [
                    'content' => 'Aircraft tidak ditemukan!',
                    'type' => 'error'
                ]);
                // Optionally redirect:
                // return $this->redirectRoute('settings.aircraftData');
                return;
            }
            $this->typeId        = $aircraftData->getKey();
            $this->type_name     = $aircraftData->type_name;
            $this->icao_code     = $aircraftData->icao_code;
            $this->iata_code     = $aircraftData->iata_code;
            $this->seat_capacity = $aircraftData->seat_capacity;
        }
    }

    protected function rules(): array
    {
        return [
            'type_name'     => [
                'required',
                'string',
                'max:40',
            ],
            'icao_code'     => ['required', 'string', 'max:4', 'regex:/^[A-Z0-9]{2,4}$/'],
            'iata_code'     => ['nullable', 'string', 'max:3', 'regex:/^[A-Z0-9]{2,3}$/'],
            'seat_capacity' => ['required', 'integer', 'min:1', 'max:65535'],
        ];
    }

    public function updatedIcaoCode($value): void
    {
        $this->icao_code = $value ? strtoupper(trim($value)) : null;
    }

    public function updatedIataCode($value): void
    {
        $this->iata_code = $value ? strtoupper(trim($value)) : null;
    }

    public function saveChanges()
    {
        // make sure '' doesn't fail integer validation
        if ($this->seat_capacity === '') {
            $this->seat_capacity = null;
        }

        $data = $this->validate();

        // normalize
        $data['icao_code'] = $data['icao_code'] ? strtoupper($data['icao_code']) : null;
        $data['iata_code'] = $data['iata_code'] ? strtoupper($data['iata_code']) : null;

        $userId = Auth::id();
        if ($this->isEdit) {
            // editing existing row → only updated_by
            $data['updated_by'] = $userId;
        } else {
            // creating new row → set both created_by & updated_by
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;
        }

        $aircraft = Aircraft::updateOrCreate(
            ['id' => $this->typeId],
            $data
        );

        $this->typeId = $aircraft->id;

        session()->flash('notify', [
            'content' => 'Aircraft saved successfully',
            'type' => 'success'
        ]);

        // Optionally redirect after save
        return $this->redirectRoute('settings.aircraft', navigate: true);
    }

    public function delete()
    {
        $row = Aircraft::find($this->aircraft);

        if (!$row) {
            session()->flash('notify', [
                'content' => 'Aircraft not found',
                'type' => 'error'
            ]);
        }

        try {
            $name = $row->type_name;
            $row->delete();

            session()->flash('notify', [
                'content' => $name . ' deleted successfully!',
                'type' => 'success'
            ]);
            $this->redirectRoute('settings.aircraft', navigate: true);
        } catch (QueryException $e) {
            // 23000 => integrity constraint violation (FK in use, etc.)
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Aircraft ini ada di catatan penerbangan dan tidak dapat dihapus.',
                    'type' => 'warning'
                ]);
                return;
            }
            // Re-throw unexpected errors for visibility in logs
            throw $e;
        }
    }

    /** Helper for the view (button text, header, etc.) */
    public function getIsEditProperty(): bool
    {
        return (bool) $this->aircraft;
    }

    public function render()
    {
        return view('livewire.settings.aircraft-modify');
    }
}
