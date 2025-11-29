<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Flight;
use App\TimeValidation;
use App\Models\Equipment;
use App\Models\AirlineRoute;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Models\ScheduledFlights;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ActualFlightsForm extends Form
{
    use TimeValidation;

    public ?Flight $record = null;

    #[Validate('required|integer|exists:airlines,id')]
    public string $airline_id = '';

    #[Validate('required|string|min:3|max:10')]
    public string $origin_flight_number = '';

    #[Validate('required|string|min:3|max:10')]
    public string $departure_flight_number = '';

    #[Validate('required|integer|exists:airline_routes,id')]
    public string $origin_route = '';

    #[Validate('required|integer|exists:airline_routes,id')]
    public string $departure_route = '';

    #[Validate('required|integer|exists:branches,id')]
    public string $branch_id = '';

    #[Validate('required|integer|exists:equipments,id')]
    public string $origin_equipment = '';

    #[Validate('required|integer|exists:equipments,id')]
    public string $departure_equipment = '';

    #[Validate('required')]
    public string $sched_arr = '';

    #[Validate('required')]
    public string $sched_dep = '';

    #[Validate('required')]
    public string $actual_arrival = '';

    #[Validate('required')]
    public string $actual_departure = '';

    #[Validate('nullable|integer|between:1,999')]
    public string $pax = '';

    #[Validate('nullable|integer|min:1|max:9999')]
    public string $ground_time = '';

    #[Validate('nullable|string|min:3')]
    public string $pic = '';

    #[Validate('nullable|string|max:255')]
    public string $notes = '';

    #[Validate('nullable|string|max:11')]
    public string $service_date = '';

    # --------------------------------------------------------
    # LOAD EDIT RECORD
    # --------------------------------------------------------
    public function setRecord(?Flight $flight, ?ScheduledFlights $scheduled)
    {
        if($flight){
            $record = $flight;
            $this->record = $record;

            $this->origin_equipment = $record->origin_equipment_id ?? '';
            $this->departure_equipment = $record->departure_equipment_id ?? '';
            $this->actual_departure = substr($record->actual_dep, 0, 5);
            $this->actual_arrival = substr($record->actual_arr, 0, 5);
            $this->pax = $record->pax ?? '';
            $this->pic = $record->pic ?? '';
            $this->ground_time = $record->ground_time ?? '';
            $this->notes = $record->notes ?? '';

            //Set date for historic flights
            $this->service_date = $record->service_date->format('Y-m-d');
        }else if($scheduled){
            $record = $scheduled;
            $this->setDate();
            $this->origin_equipment        = $record->equipment_id ?? '';
            $this->departure_equipment     = $record->equipment_id ?? '';
        }

        if ($record) {
            $this->airline_id              = $record->airline->id;
            $this->origin_flight_number    = $record->origin_flight_no;
            $this->departure_flight_number = $record->departure_flight_no;
            $this->branch_id               = $record->branch_id;
            $this->origin_route            = $record->origin_route_id;
            $this->departure_route         = $record->departure_route_id;
            $this->sched_dep = $record->sched_dep ? substr($record->sched_dep, 0, 5) : '';
            $this->sched_arr = $record->sched_arr ? substr($record->sched_arr, 0, 5) : '';
        }
    }

    # --------------------------------------------------------
    # UNIQUE + CUSTOM VALIDATION
    # --------------------------------------------------------
    private function validateAirlineRouteMatch()
    {
        if ($this->origin_route == $this->departure_route) {
            throw ValidationException::withMessages([
                'same_route' => 'Rute arrival dan departure sama!',
            ]);
        }

        $arrival = AirlineRoute::with('airline')->find($this->origin_route);
        $depart  = AirlineRoute::with('airline')->find($this->departure_route);

        if (!$arrival || $arrival->airline_id != $this->airline_id) {
            throw ValidationException::withMessages([
                'origin_route' => 'Rute tidak sesuai dengan airline yang dipilih.',
            ]);
        } else if (!$depart || $depart->airline_id != $this->airline_id) {
            throw ValidationException::withMessages([
                'departure_route' => 'Rute tidak sesuai dengan airline yang dipilih.',
            ]);
        }
    }

    private function validateEquipmentMatch()
    {
        if (!$this->origin_equipment) return;

        if (!$this->departure_equipment) return;

        $equipments = Equipment::whereIn('id', [$this->origin_equipment, $this->departure_equipment])->get()->keyBy('id');

        $oeq = $equipments[$this->origin_equipment] ?? null;
        $deq = $equipments[$this->departure_equipment] ?? null;

        if ($oeq && $oeq->airline_id != $this->airline_id) {
            throw ValidationException::withMessages([
                'origin_equipment' => 'Origin Equipment tidak sesuai dengan airline.',
            ]);
        }

        if ($deq && $deq->airline_id != $this->airline_id) {
            throw ValidationException::withMessages([
                'departure_equipment' => 'Departure equipment tidak sesuai dengan airline.',
            ]);
        }
    }

    private function validateFlightNumber()
    {
        if ($this->origin_flight_number == $this->departure_flight_number) {
            throw ValidationException::withMessages([
                'same_flight_no' => 'Flight Number Origin dan Departure Sama!',
            ]);
        }
    }

    public function setDate()
    {
        $this->service_date = Carbon::now()->format('Y-m-d');
    }

    # --------------------------------------------------------
    # SAVE
    # --------------------------------------------------------
    public function save($isEdit)
    {
        $this->checkTimeFormat($this->sched_dep);
        $this->checkTimeFormat($this->sched_arr);
        $this->checkTimeFormat($this->actual_departure);
        $this->checkTimeFormat($this->actual_arrival);

        // 1) Validate property-level rules
        $this->validate();

        // 2) Validate unique flight numbers
        $this->validate([
            'origin_flight_number' => [
                Rule::unique('actual_flights', 'origin_flight_no')
                    ->where(fn($q) => $q->whereDate('service_date', $this->service_date))
                    ->ignore($this->record?->id),
            ],
            'departure_flight_number' => [
                Rule::unique('actual_flights', 'departure_flight_no')
                    ->where(fn($q) => $q->whereDate('service_date', $this->service_date)) 
                    ->ignore($this->record?->id),
            ],
        ]);

        // 3) Airline-route-equipment-flight number validation
        $this->validateAirlineRouteMatch();
        $this->validateFlightNumber();
        $this->validateEquipmentMatch();

        // 4) Format time
        $this->sched_dep = $this->formatTime($this->sched_dep);
        $this->sched_arr = $this->formatTime($this->sched_arr);
        $this->actual_departure = $this->formatTime($this->actual_departure);
        $this->actual_arrival = $this->formatTime($this->actual_arrival);

        $payload = [
            'origin_flight_no'      => strtoupper($this->origin_flight_number),
            'departure_flight_no'   => strtoupper($this->departure_flight_number),
            'branch_id'             => $this->branch_id,
            'origin_route_id'       => $this->origin_route,
            'departure_route_id'    => $this->departure_route,
            'origin_equipment_id'   => $this->origin_equipment,
            'departure_equipment_id'=> $this->departure_equipment,
            'sched_dep'             => $this->sched_dep,
            'sched_arr'             => $this->sched_arr,
            'actual_dep'            => $this->actual_departure,
            'actual_arr'            => $this->actual_arrival,
            'service_date'          => $this->service_date,
            'pax'                   => $this->pax ?: null,
            'ground_time'           => $this->ground_time ?: null,
            'pic'                   => $this->pic ?: null,
            'notes'                 => $this->notes ?: null,
        ];

        $userId = Auth::id();

        if ($isEdit) {
            $payload['updated_by'] = $userId;
        } else {
            $payload['created_by'] = $userId;
            $payload['updated_by'] = $userId;
        }

        if ($isEdit && $this->record) {
            $flight = $this->record->update($payload);
            return;
        } else {
            $flight = Flight::create($payload);
            return $flight->airline->name;
        }
        
    }
}
