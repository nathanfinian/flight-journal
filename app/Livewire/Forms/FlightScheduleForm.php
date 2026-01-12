<?php

namespace App\Livewire\Forms;

use Carbon\Carbon;
use Livewire\Form;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule;
use App\Models\ScheduledFlights;
use App\Models\AirlineRoute;
use App\Models\Equipment;
use App\Traits\TimeValidation;
use Illuminate\Validation\ValidationException;

class FlightScheduleForm extends Form
{
    use TimeValidation;

    public ?ScheduledFlights $record = null;

    #[Validate('required|integer|exists:airlines,id')]
    public string $airline_id = '';
    
    #[Validate('required|string|min:3|max:10')]
    public string $arrival_flight_number = '';

    #[Validate('required|string|min:3|max:10')]
    public string $departure_flight_number = '';

    #[Validate('required|integer|exists:airline_routes,id')]
    public string $origin_route = '';

    #[Validate('required|integer|exists:airline_routes,id')]
    public string $departure_route = '';

    #[Validate('required|integer|exists:branches,id')]
    public string $branch_id = '';

    #[Validate('nullable|integer|exists:equipments,id')]
    public string $equipment_id = '';

    #[Validate('required')]
    public string $sched_dep = '';

    #[Validate('required')]
    public string $sched_arr = '';

    #[Validate([
        'days' => 'required|min:1',
        'days.*' => [
            'required',
            'integer',
            'exists:days,id'
        ],
    ])]
    public $days = [];

    # --------------------------------------------------------
    # LOAD EDIT RECORD
    # --------------------------------------------------------
    public function setRecord(?ScheduledFlights $record)
    {
        $this->record = $record;

        if ($record) {
            $this->arrival_flight_number   = $record->origin_flight_no;
            $this->departure_flight_number = $record->departure_flight_no;
            $this->branch_id               = $record->branch_id;
            $this->airline_id              = $record->airline->id;
            $this->origin_route            = $record->origin_route_id;
            $this->departure_route         = $record->departure_route_id;
            $this->equipment_id            = $record->equipment_id ?? '';
            $this->sched_dep               = substr($record->sched_dep, 0, 5);
            $this->sched_arr               = substr($record->sched_arr, 0, 5);
            $this->days                    = $record->days->pluck('id')
                                                ->map(fn($id) => (string) $id)
                                                ->toArray();
        }
    }

    # --------------------------------------------------------
    # UNIQUE + CUSTOM VALIDATION
    # --------------------------------------------------------
    private function validateAirlineRouteMatch()
    {
        $arrival = AirlineRoute::with('airline')->find($this->origin_route);
        $depart  = AirlineRoute::with('airline')->find($this->departure_route);

        if ($this->origin_route == $this->departure_route) 
        {
            throw ValidationException::withMessages([
                'same_route' => 'Rute arrival dan departure sama!',
            ]);
        } 
        else if (!$arrival || $arrival->airline_id != $this->airline_id ) 
        {
            throw ValidationException::withMessages([
                'origin_route' => 'Rute tidak sesuai dengan airline yang dipilih.',
            ]);
        } 
        else if(!$depart || $depart->airline_id != $this->airline_id)
        {
            throw ValidationException::withMessages([
                'departure_route' => 'Rute tidak sesuai dengan airline yang dipilih.',
            ]);
        }
    }

    private function validateEquipmentMatch()
    {
        if (!$this->equipment_id) return;

        $eq = Equipment::find($this->equipment_id);

        if ($eq && $eq->airline_id != $this->airline_id) {
            throw ValidationException::withMessages([
                'equipment_id' => 'Equipment tidak sesuai dengan airline.',
            ]);
        }
    }

    private function validateFlightNumber()
    {
        if ($this->arrival_flight_number == $this->departure_flight_number) {
            throw ValidationException::withMessages([
                'same_flight_no' => 'Flight Number Origin dan Departure Sama!',
            ]);
        }
    }

    # --------------------------------------------------------
    # SAVE
    # --------------------------------------------------------
    public function save()
    {
        $this->checkTimeFormat($this->sched_dep);
        $this->checkTimeFormat($this->sched_arr);

        // 1) Validate property-level rules
        $this->validate();

        // 2) Validate unique flight numbers
        $this->validate([
            'arrival_flight_number' => [
                Rule::unique('scheduled_flights', 'origin_flight_no')->ignore($this->record?->id)
            ],
            'departure_flight_number' => [
                Rule::unique('scheduled_flights', 'departure_flight_no')->ignore($this->record?->id)
            ],
        ]);

        // 3) Airline-route-equipment-flight number validation
        $this->validateAirlineRouteMatch();
        $this->validateFlightNumber();
        $this->validateEquipmentMatch();

        // 4) Format time
        $this->sched_dep = $this->formatTime($this->sched_dep);
        $this->sched_arr = $this->formatTime($this->sched_arr);

        // 5) Save record
        $flight = ScheduledFlights::updateOrCreate(
            ['id' => $this->record?->id],
            [
                'origin_flight_no'      => strtoupper($this->arrival_flight_number),
                'departure_flight_no'   => strtoupper($this->departure_flight_number),
                'branch_id'             => $this->branch_id,
                'origin_route_id'       => $this->origin_route,
                'departure_route_id'    => $this->departure_route,
                'equipment_id'          => $this->equipment_id ?: null,
                'sched_dep'             => $this->sched_dep,
                'sched_arr'             => $this->sched_arr,
            ]
        );

        // Sync days
        $flight->days()->sync($this->days);

        return $flight;
    }
}
