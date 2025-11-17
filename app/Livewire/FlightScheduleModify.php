<?php

// namespace App\Livewire;

// use Carbon\Carbon;
// use App\Models\Day;
// use App\Models\Branch;
// use App\Models\Airline;
// use Livewire\Component;
// use App\Models\Equipment;
// use Illuminate\Support\Str;
// use App\Models\AirlineRoute;
// use Illuminate\Validation\Rule;
// use App\Models\ScheduledFlights;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Database\QueryException;
// use Illuminate\Validation\ValidationException;

// class FlightScheduleModify extends Component
// {
//     public $branches;
//     public $airlines;
//     public $equipments;
//     public $airlineRoutes;
//     public $dayList;

//     public ?ScheduledFlights $record = null;
//     public bool $isEdit = false;

//     // Form properties
//     public ?string $arrival_flight_number = null;
//     public ?string $departure_flight_number = null;
//     public ?string $branch_id = '';
//     public ?string $airline_id = '';

//     public ?string $arrival_route = '';
//     public ?string $departure_route = '';

//     public ?string $equipment_id = '';
//     public ?string $sched_dep = '';
//     public ?string $sched_arr = '';
//     public array $days = [];

//     public ?string $airlineIata = null;    // e.g. "JT"

//     public function mount(?int $scheduled = null)
//     {
//         // Load dropdown choices
//         $this->branches = Branch::where('status', 'ACTIVE')->orderBy('name')->get(['id', 'name']);
//         $this->airlines = Airline::where('status', 'ACTIVE')->orderBy('name')->get(['id', 'name']);

//         $this->equipments = Equipment::with('airline:id,name')
//             ->where('status', 'ACTIVE')
//             ->orderBy('airline_id')
//             ->get(['id', 'registration', 'airline_id']);

//         // Load routes
//         $this->airlineRoutes = AirlineRoute::query()
//             ->with([
//                 'airline:id,name',
//                 'airportRoute.origin:id,iata',
//                 'airportRoute.destination:id,iata',
//             ])
//             ->get()
//             ->mapWithKeys(fn($r) => [
//                 $r->id => "{$r->airline->name} - {$r->airportRoute->origin->iata} ➜ {$r->airportRoute->destination->iata}"
//             ])
//             ->toArray();

//         $this->dayList = Day::orderBy('id')->get(['id', 'day_name']);

//         // Edit mode
//         if ($scheduled) {
//             $this->record = ScheduledFlights::with('days')->findOrFail($scheduled);
//             $this->isEdit = true;
            
//             $this->airline_id = $this->record->airline->id;
//             $this->arrival_flight_number = $this->record->arrival_flight_number;
//             $this->departure_flight_number = $this->record->departure_flight_number;
//             $this->branch_id = $this->record->branch_id;
//             $this->arrival_route = $this->record->arrival_route;
//             $this->departure_route = $this->record->departure_route;
//             $this->equipment_id = $this->record->equipment_id;
//             $this->sched_dep = substr($this->record->sched_dep, 0, 5);
//             $this->sched_arr = substr($this->record->sched_arr, 0, 5);
//             $this->days = $this->record->days
//                 ->pluck('id')
//                 ->map(fn($id) => (string) $id)
//                 ->toArray();
//         }
//     }

//     public function saveChanges()
//     {
//         $this->checkFormat($this->sched_dep);
//         $this->checkFormat($this->sched_arr);
//         // Convert empty string to null for optional foreign keys
//         $this->equipment_id = $this->equipment_id ?: null;

//         // --- 1️⃣ Validation ---
//         $this->validate([
//             'arrival_flight_number'  => ['required', 'string', 'max:10', Rule::unique('scheduled_flights', 'flight_no')->ignore($this->record?->id)],
//             'departure_flight_number'  => ['required', 'string', 'max:10', Rule::unique('scheduled_flights', 'flight_no')->ignore($this->record?->id)],
//             'branch_id'       => ['required', 'integer', 'exists:branches,id'],
//             'airline_id'      => ['required', 'integer', 'exists:airlines,id'],
//             'arrival_route'=> ['required', 'integer', 'exists:airline_routes,id'], 
//             'departure_route'=> ['required', 'integer', 'exists:airline_routes,id'], 
//             'equipment_id'    => ['nullable', 'integer', 'exists:equipments,id'],
//             'sched_dep'       => ['required'],
//             'sched_arr'       => ['required'],
//             'days'            => ['required', 'array', 'min:1'],
//             'days.*'          => ['integer', 'exists:days,id'],
//         ]);

//         // --- Extra check: verify that airline_route belongs to airline_id ---
//         $airlineRoute = AirlineRoute::with('airline')->find($this->airline_route_id);
//         if (!$airlineRoute || $airlineRoute->airline_id != $this->airline_id) {
//             throw ValidationException::withMessages([
//                 'airline_route_id' => 'Airline belum terdaftar di rute yang dipilih',
//             ]);
//         }
        
//         // Validate equipment-airline match
//         $equipment    = Equipment::with('airline')->find($this->equipment_id);
//         if ($equipment && $equipment->airline_id != $this->airline_id) {
//             throw ValidationException::withMessages([
//                 'equipment_id' => 'Peralatan tidak terdaftar pada airline yang dipilih',
//             ]);
//         }

//         // --- 2️⃣ Composite uniqueness ---
//         $this->validate([
//             'airline_route_id' => [
//                 Rule::unique('scheduled_flights', 'airline_route_id')
//                     ->where(
//                         fn($q) => $q
//                             ->where('branch_id', $this->branch_id)
//                             ->where('equipment_id', $this->equipment_id)
//                             ->where('sched_dep', $this->sched_dep)
//                     )
//                     ->ignore($this->record?->id),
//             ],
//         ], [
//             'airline_route_id.unique' => 'This flight schedule already exists for the same branch, equipment, and ETD.',
//         ]);

//         $this->sched_dep = $this->formatTime($this->sched_dep);
//         $this->sched_arr = $this->formatTime($this->sched_arr);

//         // --- 3️⃣ Audit fields ---
//         $userId = Auth::id();
//         $payload = [
//             'arrival_flight_number' => strtoupper($this->arrival_flight_number),
//             'departure_flight_number' => strtoupper($this->departure_flight_number),
//             'branch_id'             => $this->branch_id,
//             'arrival_route'         => $this->arrival_route,
//             'departure_route'       => $this->departure_route,
//             'equipment_id'          => $this->equipment_id,
//             'sched_dep'             => $this->sched_dep,
//             'sched_arr'             => $this->sched_arr,
//         ];

//         if ($this->isEdit) {
//             $payload['updated_by'] = $userId;
//         } else {
//             $payload['created_by'] = $userId;
//             $payload['updated_by'] = $userId;
//         }

//         // --- 4️⃣ Save or update ---
//         $this->record = ScheduledFlights::updateOrCreate(
//             ['id' => $this->record?->id],
//             $payload
//         );

//         // --- 5️⃣ Sync pivot ---
//         if (!empty($this->days)) {
//             $this->record->days()->sync($this->days);
//         }

//         // --- 6️⃣ Flash message ---
//         session()->flash('notify', [
//             'content' => 'Flight schedule berhasil disimpan!',
//             'type'    => 'success',
//         ]);

//         return redirect()->route('flight-schedule');
//     }

//     public function delete()
//     {
//         $row = ScheduledFlights::find($this->record?->id);
//         $name = $row?->flight_no ?? 'Unknown';

//         if (!$row) {
//             session()->flash('notify', [
//                 'content' => 'Scheduled Flight not found',
//                 'type' => 'error'
//             ]);
//         }

//         try {
//             $row->delete();

//             session()->flash('notify', [
//                 'content' => $name . ' berhasil dihapus!',
//                 'type' => 'success'
//             ]);
//             $this->redirectRoute('flight-schedule', navigate: true);
//         } catch (QueryException $e) {
//             // 23000 => integrity constraint violation (FK in use, etc.)
//             if ($e->getCode() === '23000') {
//                 session()->flash('notify', [
//                     'content' => 'Jadwal pesawat ini ada di journal penerbangan dan tidak dapat dihapus.',
//                     'type' => 'warning'
//                 ]);
//                 return;
//             }
//             // Re-throw unexpected errors for visibility in logs
//             throw $e;
//         }
//     }

//     public function updatedAirlineId($value)
//     {
//         $this->loadEquipmentRoute($value);

//         $this->reset(['equipment_id', 'airline_route_id']);
//     }

//     protected function loadEquipmentRoute(?int $airlineId = null)
//     {
//         // 2.1 Re-query equipments for this airline
//         $this->equipments = Equipment::query()
//             ->with('airline:id,name')
//             ->where('status', 'ACTIVE')
//             ->when($airlineId, fn($q) => $q->where('airline_id', $airlineId))
//             ->orderBy('registration')
//             ->get(['id', 'registration', 'airline_id'])
//             ->all(); // array for easy foreach

//         // 2.2 Re-query routes for this airline
//         $this->airlineRoutes = AirlineRoute::query()
//             ->when($airlineId, fn($q) => $q->where('airline_id', $airlineId))
//             ->with([
//                 'airportRoute.origin:id,iata',
//                 'airportRoute.destination:id,iata',
//             ])
//             ->get()
//             ->mapWithKeys(fn($r) => [
//                 $r->id => "{$r->airportRoute->origin->iata} ➜ {$r->airportRoute->destination->iata}",
//             ])
//             ->toArray();

//         // 2.3 Fetch IATA for masking/prefixing the flight number
//         $this->airlineIata = $airlineId
//             ? Airline::whereKey($airlineId)->value('iata_code')
//             : null;

//         // Optional: prefill/normalize the flight number prefix
//         if ($this->airlineIata) {
//             // keep only digits after the prefix
//             $digits = preg_replace('/\D/', '', $this->arrival_flight_number);
//             $this->arrival_flight_number = Str::upper($this->airlineIata) . $digits;
//         }
//     }

//     private function checkFormat(?string $time)
//     {
//         // Match HH:MM exactly (00–23 : 00–59)
//         if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
//             throw \Illuminate\Validation\ValidationException::withMessages([
//                 'time_format' => 'Invalid format waktu: harus 24-jam HH:MM (00:00–23:59)',
//             ]);
//         }
//     }

//     private function formatTime(?string $time): ?string
//     {
//         if (empty($time)) {
//             return null;
//         }

//         try {
//             return Carbon::createFromFormat('H:i', $time)->format('H:i:s');
//         } catch (\Exception $e) {
//             throw \Illuminate\Validation\ValidationException::withMessages([
//                 'time_format' => 'Invalid format waktu: harus 24-jam HH:MM (00:00–23:59)',
//             ]);
//         }
//     }

//     public function render()
//     {
//         return view('livewire.flight-schedule-modify');
//     }
// }

namespace App\Livewire;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Airline;
use App\Models\Equipment;
use App\Models\AirlineRoute;
use App\Models\Day;
use App\Models\ScheduledFlights;
use App\Livewire\Forms\FlightScheduleForm;

class FlightScheduleModify extends Component
{
    public FlightScheduleForm $form;

    public ScheduledFlights $flight;

    public $branches;
    public $airlines;
    public $equipments;
    public $airlineRoutes;
    public $dayList;

    public bool $isEdit = false;

    public function mount(?int $scheduled)
    {
        $this->branches = Branch::where('status', 'ACTIVE')->orderBy('name')->get();
        $this->airlines = Airline::where('status', 'ACTIVE')->orderBy('name')->get();
        $this->dayList  = Day::orderBy('id')->get();

        $this->equipments = Equipment::where('status', 'ACTIVE')->get();

        $this->airlineRoutes = AirlineRoute::query()
            ->with([
                'airportRoute.origin:id,iata',
                'airportRoute.destination:id,iata',
            ])
            ->get()
            ->mapWithKeys(fn($r) => [
                $r->id => "{$r->airportRoute->origin->iata} ➜ {$r->airportRoute->destination->iata}",
            ])
            ->toArray();

        if ($scheduled) {
            $this->isEdit = true;
            $this->flight   = ScheduledFlights::findOrFail($scheduled);

            $this->form->setRecord($this->flight);
        }
    }

    public function save()
    {
        $flight = $this->form->save();

        session()->flash('notify', [
            'content' => 'Jadwal Penerbangan berhasil disimpan!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('flight-schedule');
    }

    public function delete()
    {
        $this->form->record?->delete();

        session()->flash('notify', [
            'content' => 'Jadwal penerbangan berhasil dihapus!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('flight-schedule');
    }

    public function render()
    {
        return view('livewire.flight-schedule-modify');
    }
}

