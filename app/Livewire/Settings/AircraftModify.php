<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Aircraft;
use Illuminate\Validation\Rule;

class AircraftModify extends Component
{
    public ?int $typeId = null;

    public string $type_name = '';
    public ?string $icao_code = null;
    public ?string $iata_code = null;
    public ?int $seat_capacity = null;

    // If you pass an Aircraft via route model binding
    // public function mount(?Aircraft $aircraft = null): void
    // {
    //     if ($aircraft) {
    //         $this->typeId        = $aircraft->getKey();
    //         $this->type_name     = $aircraft->type_name;
    //         $this->icao_code     = $aircraft->icao_code;
    //         $this->iata_code     = $aircraft->iata_code;
    //         $this->seat_capacity = $aircraft->seat_capacity;
    //     }
    // }
    public function mount(?int $id = null): void
    {
        if ($id) {
            $aircraft = Aircraft::find($id);
            if (!$aircraft) {
                // Handle gracefully instead of 404
                $this->dispatch('notify', [
                    'title' => 'Not found',
                    'message' => 'Aircraft not found.',
                    'type' => 'error',
                ]);
                // Optionally redirect:
                // return $this->redirectRoute('settings.aircraft');
                return;
            }
            $this->typeId        = $aircraft->getKey();
            $this->type_name     = $aircraft->type_name;
            $this->icao_code     = $aircraft->icao_code;
            $this->iata_code     = $aircraft->iata_code;
            $this->seat_capacity = $aircraft->seat_capacity;
        }
    }

    protected function rules(): array
    {
        return [
            'type_name'     => [
                'required',
                'string',
                'max:40',
                Rule::unique('aircrafts', 'type_name')->ignore($this->typeId), // ✅ table = aircraft
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

    public function saveChanges(): void
    {
        // make sure '' doesn't fail integer validation
        if ($this->seat_capacity === '') {
            $this->seat_capacity = null;
        }

        $data = $this->validate();

        // normalize
        $data['icao_code'] = $data['icao_code'] ? strtoupper($data['icao_code']) : null;
        $data['iata_code'] = $data['iata_code'] ? strtoupper($data['iata_code']) : null;

        $aircraft = Aircraft::updateOrCreate(
            ['id' => $this->typeId],
            $data
        );

        $this->typeId = $aircraft->id;

        $this->dispatch('notify', [
            'title' => 'Saved',
            'message' => 'Aircraft saved successfully.',
            'type' => 'success',
        ]);
    }

    public function render()
    {
        return view('livewire.settings.aircraft-modify');
    }
}
