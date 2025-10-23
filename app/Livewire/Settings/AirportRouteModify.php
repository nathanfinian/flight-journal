<?php

namespace App\Livewire\Settings;

use App\Models\Airline;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Airport;        // has id, city/name/iata/icao
use App\Models\AirportRoute;   // table: airport_routes

class AirportRouteModify extends Component
{
    public ?AirportRoute $record = null;

    /** @var \Illuminate\Support\Collection<int, Airport> */
    public $cities; // naming kept to match your Blade
    public $airlines; 

    public ?int $routeId = null;
    public string $origin_id = '';
    public string $destination_id = '';
    public $selected_airlines = [];
    public $selected_airlines_name = [];
    public string $status = 'ACTIVE';

    public bool $isEdit = false;

    public function mount(?int $route = null): void
    {
        $this->routeId = $route;

        // Load choices
        $this->cities = Airport::query()
            ->orderBy('city')     // or ->orderBy('name')
            ->get(['id', 'city', 'iata']);

        // Load choices
        $this->airlines = Airline::query()
            ->orderBy('name')     // or ->orderBy('name')
            ->get(['id', 'name', 'icao_code']);

        if ($route) {
            $this->record = AirportRoute::findOrFail($route);
            $this->selected_airlines = $this->record
                ->airlines()
                ->pluck('airlines.id')
                ->map(fn($id) => (string) $id)
                ->toArray();

            $this->selected_airlines_name = $this->record
                ->airlines()
                ->get(['name', 'iata_code'])
                ->map(fn($a) => "{$a->name} ({$a->iata_code})")
                ->toArray();
            $this->isEdit = true;

            $this->origin_id      = (int) $this->record->origin_id;
            $this->destination_id = (int) $this->record->destination_id;
            $this->status      = $this->record->status;
        }
    }

    public function saveChanges()
    {
        // Validation (includes: origin != destination, composite uniqueness)
        $this->validate([
            'origin_id' => ['required', 'integer', 'exists:airports,id', 'different:destination_id'],
            'destination_id' => ['required', 'integer', 'exists:airports,id', 'different:origin_id'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
            'selected_airlines' => ['array', 'min:1'], // ✅ ensure at least one airline selected
            'selected_airlines.*' => ['integer', 'exists:airlines,id'],
        ]);

        // Composite unique on (origin_id, destination_id)
        $this->validate([
            'origin_id' => [
                Rule::unique('airport_routes', 'origin_id')
                    ->where(fn($q) => $q->where('destination_id', $this->destination_id))
                    ->ignore($this->record?->id),
            ],
        ], [
            'origin_id.unique' => 'This origin → destination pair already exists',
        ]);

        // Audit fields
        $userId = Auth::id();
        $payload = [
            'origin_id' => $this->origin_id,
            'destination_id' => $this->destination_id,
            'status' => $this->status,
        ];

        if ($this->isEdit) {
            $payload['updated_by'] = $userId;
        } else {
            $payload['created_by'] = $userId;
            $payload['updated_by'] = $userId;
        }

        $this->record = AirportRoute::updateOrCreate(
            ['id' => $this->record?->id],
            $payload
        );

        // ✅ Sync the pivot table (airline_routes)
        if (!empty($this->selected_airlines)) {
            $this->record->airlines()->sync($this->selected_airlines);
        }

        session()->flash('notify', [
            'content' => 'Airport route saved successfully',
            'type' => 'success',
        ]);

        return redirect()->route('settings.airport-route');
    }

    public function delete()
    {
        if ($this->record) {
            $rute = 'Rute ' . $this->record->origin->iata . ' → ' . $this->record->destination->iata;
            $this->record->delete();

            session()->flash('notify', [
                'content' => $rute . ' successfully deleted',
                'type' => 'success',
            ]);

            return redirect()->route('settings.airport-route');
        }
    }

    public function render()
    {
        return view('livewire.settings.airport-route-modify');
    }
}
