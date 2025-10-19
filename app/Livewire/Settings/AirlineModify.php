<?php

namespace App\Livewire\Settings;

use App\Models\Airline;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class AirlineModify extends Component
{
    // Route param (e.g. /settings/airlines/{airline}/edit)
    public ?int $airlineId = null;

    // Form fields
    public string  $name       = '';
    public ?string $iata_code  = null;   // 2 chars, optional
    public ?string $icao_code  = null;   // 3 chars, optional
    public ?string $callsign   = null;   // optional
    public ?string $country    = null;   // optional
    public string  $status     = 'ACTIVE'; // ACTIVE | INACTIVE these selects are automatically selected when passed with livewire

    public function mount(?int $airline = null): void
    {
        if ($airline) {
            $row = Airline::find($airline);
            if (!$row) {
                $this->dispatch('notify', [
                    'title'   => 'Not found',
                    'message' => 'Airline not found.',
                    'type'    => 'error',
                ]);
                return;
            }

            $this->airlineId  = $row->getKey();
            $this->name       = (string) $row->name;
            $this->iata_code  = $row->iata_code;
            $this->icao_code  = $row->icao_code;
            $this->callsign   = $row->callsign;
            $this->country    = $row->country;
            $this->status     = $row->status ?: 'ACTIVE';
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],

            // IATA: nullable, exactly 2 alphanum, unique if present
            'iata_code' => [
                'nullable',
                'string',
                'size:2',
                'regex:/^[A-Z0-9]{2}$/',
                Rule::unique('airlines', 'iata_code')
                    ->ignore($this->airlineId)
                    ->where(fn($q) => $q->whereNotNull('iata_code')),
            ],

            // ICAO: nullable, exactly 3 alphanum, unique if present
            'icao_code' => [
                'nullable',
                'string',
                'size:3',
                'regex:/^[A-Z0-9]{3}$/',
                Rule::unique('airlines', 'icao_code')
                    ->ignore($this->airlineId)
                    ->where(fn($q) => $q->whereNotNull('icao_code')),
            ],

            'callsign' => ['nullable', 'string', 'max:40'],
            'country'  => ['required', 'string', 'max:60'],
            'status'   => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }

    /** Auto-uppercase codes (and callsign) as user types */
    public function updatedIataCode($value): void
    {
        $this->iata_code = $value ? strtoupper(trim($value)) : null;
    }
    public function updatedIcaoCode($value): void
    {
        $this->icao_code = $value ? strtoupper(trim($value)) : null;
    }
    public function updatedCallsign($value): void
    {
        $this->callsign = $value ? strtoupper(trim($value)) : null;
    }

    public function saveChanges()
    {
        $data = $this->validate();

        // Normalize blanks to null
        foreach (['iata_code', 'icao_code', 'callsign', 'country'] as $k) {
            $data[$k] = isset($data[$k]) && $data[$k] !== '' ? $data[$k] : null;
        }

        // Ensure uppercase for codes again (server-side safety)
        if ($data['iata_code']) $data['iata_code'] = strtoupper($data['iata_code']);
        if ($data['icao_code']) $data['icao_code'] = strtoupper($data['icao_code']);
        if ($data['callsign'])  $data['callsign']  = strtoupper($data['callsign']);

        // Stamp who did it
        $userId = Auth::id();
        if ($this->isEdit) {
            // editing existing row → only updated_by
            $data['updated_by'] = $userId;
        } else {
            // creating new row → set both created_by & updated_by
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;
        }

        $airline = Airline::updateOrCreate(
            ['id' => $this->airlineId],
            $data
        );

        $this->airlineId = $airline->id;

        session()->flash('notify', [
            'content' => 'Airline saved successfully!',
            'type' => 'success'
        ]);

        return $this->redirectRoute('settings.airline', navigate: true);
    }

    public function delete()
    {
        $row = Airline::find($this->airlineId);

        if (!$row) {
            session()->flash('notify', [
                'content' => 'Airline not found',
                'type' => 'error'
            ]);
        }

        try {
            $row->delete();

            session()->flash('notify', [
                'content' => 'Airline deleted successfully!',
                'type' => 'success'
            ]);
            $this->redirectRoute('settings.airline', navigate: true);

        } catch (QueryException $e) {
            // 23000 => integrity constraint violation (FK in use, etc.)
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Airline ini ada di catatan penerbangan dan tidak dapat dihapus.',
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
        return (bool) $this->airlineId;
    }

    public function render()
    {
        return view('livewire.settings.airline-modify');
    }
}
