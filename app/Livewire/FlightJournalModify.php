<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Flight;
use App\Models\Airline;
use Livewire\Component;
use App\Models\Equipment;
use Illuminate\Support\Str;
use App\Models\AirlineRoute;
use Illuminate\Validation\Rule;
use App\Models\ScheduledFlights;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Http\Requests\StoreFlightRequest;
use Illuminate\Validation\ValidationException;

class FlightJournalModify extends Component
{
    public $branches;
    public $equipments;
    public $airlineRoutes;
    public $airlines;
    public $dayList;

    public ?Flight $record = null;
    public ?ScheduledFlights $scheduledFlight = null;
    public ?int $id = null;
    public bool $isEdit = false;

    // Form properties
    public ?string $flight_number = null;
    public ?string $branch_id = '';
    public ?string $airline_route_id = '';
    public ?string $equipment_id = '';

    public ?string $sched_dep = '';
    public ?string $sched_arr = '';
    public ?string $actual_dep = '';
    public ?string $actual_arr = '';

    public ?int $pax = null;
    public ?int $ground_time = null;
    public ?string $pic = '';
    public ?string $notes = '';

    public ?string $airlineIata = null;
    public ?string $airline_id = '';

    public ?string $type;

    public function mount(?int $id = null)
    {
        $this->type = request()->query('type');  // or request('type')

        // Load dropdown choices
        $this->branches = Branch::where('status', 'ACTIVE')->orderBy('name')->get(['id', 'name']);
        $this->airlines = Airline::where('status', 'ACTIVE')->orderBy('name')->get(['id', 'name']);

        $this->equipments = Equipment::with('airline:id,name')
            ->where('status', 'ACTIVE')
            ->orderBy('airline_id')
            ->get(['id', 'registration', 'airline_id']);

        // Load routes
        $this->airlineRoutes = AirlineRoute::query()
            ->with([
                'airline:id,name',
                'airportRoute.origin:id,iata',
                'airportRoute.destination:id,iata',
            ])
            ->get()
            ->mapWithKeys(fn($r) => [
                $r->id => "{$r->airline->name} - {$r->airportRoute->origin->iata} ➜ {$r->airportRoute->destination->iata}"
            ])
            ->toArray();

        // Edit mode, load data from who is requesting
        if($this->type === 'scheduled') //Data from scheduled flight
        {
            $this->scheduledFlight = ScheduledFlights::findOrFail($id);

            $this->isEdit = true;

            $this->airline_id = $this->scheduledFlight->airline->id;
            $this->flight_number = $this->scheduledFlight->flight_no;
            $this->branch_id = $this->scheduledFlight->branch_id;
            $this->airline_route_id = $this->scheduledFlight->airline_route_id;
            $this->equipment_id = $this->scheduledFlight->equipment_id;
            $this->sched_dep = substr($this->scheduledFlight->sched_dep, 0, 5);
            $this->sched_arr = substr($this->scheduledFlight->sched_arr, 0, 5);

        }else if($this->type === 'actual' || $this->type === 'history')//Data from flights table
        {
            $this->record = Flight::findOrFail($id);

            $this->isEdit = true;

            $this->id = $this->record->id;

            $this->airline_id = $this->record->airline->id;
            $this->flight_number = $this->record->flight_no;
            $this->branch_id = $this->record->branch_id;
            $this->airline_route_id = $this->record->airline_route_id;
            $this->equipment_id = $this->record->equipment_id;

            $this->sched_dep = substr($this->record->sched_dep, 0, 5);
            $this->sched_arr = substr($this->record->sched_arr, 0, 5);
            $this->actual_dep = substr($this->record->actual_dep, 0, 5);
            $this->actual_arr = substr($this->record->actual_arr, 0, 5);

            $this->pax = $this->record->pax;
            $this->pic = $this->record->pic;
            $this->ground_time = $this->record->ground_time;
            $this->notes = $this->record->notes;
        }else if($id == null){
            // go to create no data retrieved
            $this->airline_id = 1;
        }else{
            session()->flash('notify', [
                'content' => 'Tipe data tidak ada!',
                'type'    => 'error',
            ]);

            if ($this->type === 'scheduled') {
                return redirect()->route('flight-journal');
            } else{
                return redirect()->route('flight-journal.actual');
            }           
        }
        // $this->loadEquipmentRoute($this->airline_id);
    }

    public function saveChanges()
    {
        //Check format and return error
        $this->checkTimeFormat($this->sched_dep);
        $this->checkTimeFormat($this->sched_arr);
        $this->checkTimeFormat($this->actual_dep);
        $this->checkTimeFormat($this->actual_arr);

        // Convert empty string to null for optional foreign keys
        $this->equipment_id = $this->equipment_id ?: null;

        // --- Extra check: verify that airline_route belongs to airline_id ---
        $airlineRoute = AirlineRoute::with('airline')->find($this->airline_route_id);
        if (!$airlineRoute || $airlineRoute->airline_id != $this->airline_id) {
            throw ValidationException::withMessages([
                'airline_route_id' => 'Airline tidak terdaftar di rute yang dipilih',
            ]);
        }

        // Validate equipment-airline match
        $equipment    = Equipment::with('airline')->find($this->equipment_id);
        if ($equipment && $equipment->airline_id != $this->airline_id) {
            throw ValidationException::withMessages([
                'equipment_id' => 'Peralatan tidak terdaftar pada airline yang dipilih',
            ]);
        }

        // --- Validation via Form Request ---
        $request = new StoreFlightRequest();
        $this->validate(
            $request->rules(),
            $request->messages()
        );

        //change format for db once format has no error
        $this->sched_dep = $this->formatTime($this->sched_dep);
        $this->sched_arr = $this->formatTime($this->sched_arr);
        $this->actual_dep = $this->formatTime($this->actual_dep);
        $this->actual_arr = $this->formatTime($this->actual_arr);

        // --- 3️⃣ Audit fields ---
        $userId = Auth::id();
        $payload = [
            'flight_no'        => strtoupper($this->flight_number),
            'branch_id'        => $this->branch_id,
            'airline_route_id' => $this->airline_route_id,
            'equipment_id'     => $this->equipment_id,
            'sched_dep'        => $this->sched_dep,
            'sched_arr'        => $this->sched_arr,
            'actual_dep'       => $this->actual_dep,
            'actual_arr'       => $this->actual_arr,
            'service_date'     => Carbon::today(),
            'notes'            => $this->notes,
            'pax'              => $this->pax,
            'ground_time'      => $this->ground_time,
            'pic'              => $this->pic,
        ];

        if ($this->isEdit) {
            $payload['updated_by'] = $userId;
        } else {
            $payload['created_by'] = $userId;
            $payload['updated_by'] = $userId;
        }

        // --- 4️⃣ Save or update ---
        $this->record = $this->id
            ? Flight::updateOrCreate(['id' => $this->id], $payload)
            : Flight::create($payload);


        // --- 6️⃣ Flash message ---
        session()->flash('notify', [
            'content' => 'Flight Journal recorded successfully!',
            'type'    => 'success',
        ]);

        if ($this->type === 'scheduled') {
            return redirect()->route('flight-journal');
        } 
        else if($this->type === 'actual') {
            return redirect()->route('flight-journal.actual');
        }
        else {
            return redirect()->route('flight-history');
        }
    }

    public function delete()
    {
        $row = Flight::find($this->id);
        $name = $row?->flight_no ?? 'Unknown';

        if (!$row) {
            session()->flash('notify', [
                'content' => 'Penerbangan tidak ditemukan',
                'type' => 'error'
            ]);
        }

        try {
            $row->delete();

            session()->flash('notify', [
                'content' => $name . ' berhasil dihapus!',
                'type' => 'success'
            ]);
            $this->redirectRoute('flight-journal.actual', navigate: true);
        } catch (QueryException $e) {
            // 23000 => integrity constraint violation (FK in use, etc.)
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Jadwal pesawat ini ada di journal penerbangan dan tidak dapat dihapus.',
                    'type' => 'warning'
                ]);
                return;
            }
            // Re-throw unexpected errors for visibility in logs
            throw $e;
        }
    }

    public function updatedAirlineId($value)
    {
        $this->loadEquipmentRoute($value);

        $this->reset(['equipment_id', 'airline_route_id']);
    }

    protected function loadEquipmentRoute(?int $airlineId = null)
    {
        // 2.1 Re-query equipments for this airline
        $this->equipments = Equipment::query()
            ->with('airline:id,name')
            ->where('status', 'ACTIVE')
            ->when($airlineId, fn($q) => $q->where('airline_id', $airlineId))
            ->orderBy('registration')
            ->get(['id', 'registration', 'airline_id'])
            ->all(); // array for easy foreach

        // 2.2 Re-query routes for this airline
        $this->airlineRoutes = AirlineRoute::query()
            ->when($airlineId, fn($q) => $q->where('airline_id', $airlineId))
            ->with([
                'airportRoute.origin:id,iata',
                'airportRoute.destination:id,iata',
            ])
            ->get()
            ->mapWithKeys(fn($r) => [
                $r->id => "{$r->airportRoute->origin->iata} ➜ {$r->airportRoute->destination->iata}",
            ])
            ->toArray();

        // 2.3 Fetch IATA for masking/prefixing the flight number
        $this->airlineIata = $airlineId
            ? Airline::whereKey($airlineId)->value('iata_code')
            : null;

        // Optional: prefill/normalize the flight number prefix
        if ($this->airlineIata) {
            // keep only digits after the prefix
            $digits = preg_replace('/\D/', '', $this->flight_number);
            $this->flight_number = Str::upper($this->airlineIata) . $digits;
        }
    }

    private function checkTimeFormat(?string $time)
    {
        // Match HH:MM exactly (00–23 : 00–59)
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'time_format' => 'Invalid format waktu: harus 24-jam HH:MM (00:00–23:59)',
            ]);
        }
    }

    private function formatTime(?string $time): ?string
    {
        if (empty($time)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('H:i', $time)->format('H:i:s');
        } catch (\Exception $e) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'time_format' => 'Invalid format waktu: harus 24-jam HH:MM (00:00–23:59)',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.flight-journal-modify');
    }
}

