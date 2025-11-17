<?php

namespace App\Livewire\Forms;

use Carbon\Carbon;
use Livewire\Form;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule;
use App\Models\ScheduledFlights;
use App\Models\AirlineRoute;
use App\Models\Equipment;
use App\Models\Airline;
use Illuminate\Validation\ValidationException;

class FlightScheduleForm extends Form
{
    public ?ScheduledFlights $record = null;

    #[Validate('required|string|max:10')]
    public string $arrival_flight_number = '';

    #[Validate('required|string|max:10')]
    public string $departure_flight_number = '';

    #[Validate('required|integer|exists:branches,id')]
    public ?int $branch_id = null;

    #[Validate('required|integer|exists:airlines,id')]
    public ?int $airline_id = null;

    #[Validate('required|integer|exists:airline_routes,id')]
    public ?int $arrival_route = null;

    #[Validate('required|integer|exists:airline_routes,id')]
    public ?int $departure_route = null;

    #[Validate('nullable|integer|exists:equipments,id')]
    public ?int $equipment_id = null;

    #[Validate('required')]
    public string $sched_dep = '';

    #[Validate('required')]
    public string $sched_arr = '';

    #[Validate('required|array|min:1')]
    #[Validate('each:integer|exists:days,id')]
    public array $days = [];


    # --------------------------------------------------------
    # LOAD EDIT RECORD
    # --------------------------------------------------------
    public function setRecord(?ScheduledFlights $record)
    {
        $this->record = $record;

        if ($record) {
            $this->arrival_flight_number   = $record->arrival_flight_no;
            $this->departure_flight_number = $record->departure_flight_no;
            $this->branch_id               = $record->branch_id;
            $this->airline_id              = $record->airline_id;
            $this->arrival_route           = $record->arrival_route_id;
            $this->departure_route         = $record->departure_route_id;
            $this->equipment_id            = $record->equipment_id;
            $this->sched_dep               = substr($record->sched_dep, 0, 5);
            $this->sched_arr               = substr($record->sched_arr, 0, 5);
            $this->days                    = $record->days->pluck('id')->toArray();
        }
    }


    # --------------------------------------------------------
    # TIME FORMAT VALIDATION
    # --------------------------------------------------------
    private function checkTimeFormat(?string $time)
    {
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            throw ValidationException::withMessages([
                'time_format' => 'Invalid format waktu: gunakan HH:MM (00–23 : 00–59)',
            ]);
        }
    }

    private function formatTime(?string $time): ?string
    {
        if (!$time) return null;

        try {
            return Carbon::createFromFormat('H:i', $time)->format('H:i:s');
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'time_format' => 'Invalid format waktu: gunakan HH:MM',
            ]);
        }
    }


    # --------------------------------------------------------
    # UNIQUE + CUSTOM VALIDATION
    # --------------------------------------------------------
    private function validateAirlineRouteMatch()
    {
        $arrival = AirlineRoute::with('airline')->find($this->arrival_route);
        $depart  = AirlineRoute::with('airline')->find($this->departure_route);

        if (
            !$arrival || $arrival->airline_id != $this->airline_id ||
            !$depart  || $depart->airline_id != $this->airline_id
        ) {
            throw ValidationException::withMessages([
                'route' => 'Rute tidak sesuai dengan airline yang dipilih.',
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

    private function validateCompositeUnique()
    {
        $this->validate([
            'arrival_route' => [
                Rule::unique('scheduled_flights', 'arrival_route_id')
                    ->where(
                        fn($q) =>
                        $q->where('branch_id', $this->branch_id)
                            ->where('equipment_id', $this->equipment_id)
                            ->where('sched_dep', $this->sched_dep)
                    )
                    ->ignore($this->record?->id),
            ],
        ], [
            'arrival_route.unique' =>
            'Jadwal dengan Branch, Equipment, dan ETD ini sudah ada.',
        ]);
    }


    # --------------------------------------------------------
    # SAVE
    # --------------------------------------------------------
    public function save()
    {
        // 1) Validate property-level rules
        $this->validate();

        // 2) Validate unique flight numbers
        $this->validate([
            'arrival_flight_number' => [
                Rule::unique('scheduled_flights', 'flight_no')->ignore($this->record?->id)
            ],
            'departure_flight_number' => [
                Rule::unique('scheduled_flights', 'flight_no')->ignore($this->record?->id)
            ],
        ]);

        // 3) Check time formats
        $this->checkTimeFormat($this->sched_dep);
        $this->checkTimeFormat($this->sched_arr);

        // 4) Airline-route-equipment validation
        $this->validateAirlineRouteMatch();
        $this->validateEquipmentMatch();

        // 5) Composite uniqueness
        $this->validateCompositeUnique();

        // 6) Format time
        $this->sched_dep = $this->formatTime($this->sched_dep);
        $this->sched_arr = $this->formatTime($this->sched_arr);

        // 7) Save record
        $flight = ScheduledFlights::updateOrCreate(
            ['id' => $this->record?->id],
            [
                'arrival_flight_no'   => strtoupper($this->arrival_flight_number),
                'departure_flight_no' => strtoupper($this->departure_flight_number),
                'branch_id'           => $this->branch_id,
                'airline_id'          => $this->airline_id,
                'arrival_route_id'    => $this->arrival_route,
                'departure_route_id'  => $this->departure_route,
                'equipment_id'        => $this->equipment_id,
                'sched_dep'           => $this->sched_dep,
                'sched_arr'           => $this->sched_arr,
            ]
        );

        // Sync days
        $flight->days()->sync($this->days);

        return $flight;
    }
}
