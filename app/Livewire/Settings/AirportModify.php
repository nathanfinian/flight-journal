<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Airport;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class AirportModify extends Component
{
    // Route param (e.g. /settings/airports/{airport}/edit)
    public ?int $airportId = null;

    // Form fields
    public string  $city     = '';
    public ?string $icao     = null; // 4 chars, optional
    public ?string $iata     = null; // 3 chars, optional
    public ?string $country  = null; // optional
    public string  $tz       = 'Asia/Jakarta'; // default timezone

    /** Mount for edit/create */
    public function mount(?int $airport = null): void
    {
        if ($airport) {
            $row = Airport::find($airport);
            if (!$row) {
                session()->flash('notify', [
                    'content' => 'Airport not found',
                    'type' => 'error'
                ]);
                return;
            }

            $this->airportId = $row->getKey();
            $this->city      = (string) $row->city;
            $this->icao      = $row->icao;
            $this->iata      = $row->iata;
            $this->country   = $row->country;
            // $this->tz        = $row->tz ?? 'Asia/Jakarta';
        }
    }

    /** Validation rules */
    protected function rules(): array
    {
        return [
            'city' => ['required', 'string', 'max:120'],

            'icao' => [
                'required',
                'string',
                'size:4',
                'regex:/^[A-Z0-9]{4}$/',
                Rule::unique('airports', 'icao')
                    ->ignore($this->airportId)
                    ->where(fn($q) => $q->whereNotNull('icao')), // only consider non-null values on the column
            ],

            'iata' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Z0-9]{3}$/',
                Rule::unique('airports', 'iata')
                    ->ignore($this->airportId)
                    ->where(fn($q) => $q->whereNotNull('iata')), // only consider non-null values on the column
            ],

            'country' => ['required', 'string', 'max:80'],
            // 'tz'       => ['required', 'string', 'max:40'],
        ];
    }

    /** Auto-uppercase codes as user types */
    public function updatedIcao($value): void
    {
        $this->icao = $value ? strtoupper(trim($value)) : null;
    }

    public function updatedIata($value): void
    {
        $this->iata = $value ? strtoupper(trim($value)) : null;
    }

    /** Save (create or update) */
    public function saveChanges()
    {
        $data = $this->validate();

        // Normalize blanks to null
        foreach (['icao', 'iata', 'country'] as $k) {
            $data[$k] = isset($data[$k]) && $data[$k] !== '' ? $data[$k] : null;
        }

        // Force uppercase for safety
        if ($data['icao']) $data['icao'] = strtoupper($data['icao']);
        if ($data['iata']) $data['iata'] = strtoupper($data['iata']);

        $userId = Auth::id();
        if ($this->airportId) {
            $data['updated_by'] = $userId;
        } else {
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;
        }

        $airport = Airport::updateOrCreate(
            ['id' => $this->airportId],
            $data
        );

        $this->airportId = $airport->id;

        session()->flash('notify', [
            'content' => 'Airport saved successfully!',
            'type' => 'success'
        ]);

        return $this->redirectRoute('settings.airport', navigate: true);
    }

    /** Delete airport */
    public function delete()
    {
        $row = Airport::find($this->airportId);
        $airportName = $row?->city . ' (' . $row?->iata . ')';

        if (!$row) {
            session()->flash('notify', [
                'content' => 'Airport not found',
                'type' => 'error'
            ]);
            return;
        }

        try {
            $row->delete();

            session()->flash('notify', [
                'content' => $airportName . ' deleted successfully!',
                'type' => 'success'
            ]);

            $this->redirectRoute('settings.airport', navigate: true);

        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Airport ini sedang digunakan dan tidak dapat dihapus.',
                    'type' => 'warning'
                ]);
                return;
            }
            throw $e;
        }
    }

    /** For headings/buttons in the view */
    public function getIsEditProperty(): bool
    {
        return (bool) $this->airportId;
    }

    public function render()
    {
        return view('livewire.settings.airport-modify');
    }
}
