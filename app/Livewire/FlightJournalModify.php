<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Flight;
use Livewire\Component;
use App\Models\Equipment;
use App\Models\AirlineRoute;
use Illuminate\Validation\Rule;
use App\Models\ScheduledFlights;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class FlightJournalModify extends Component
{
    public $branches;
    public $equipments;
    public $airlineRoutes;
    public $dayList;

    public ?Flight $record = null;
    public ?Flight $flightSource = null;
    public ?ScheduledFlights $source = null;
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

    public ?string $pax = '';
    public ?string $ground_time = '';
    public ?string $pic = '';
    public ?string $notes = '';
    public array $days = [];

    public ?string $type;

    public function mount(?int $id = null)
    {
        $this->type = request()->query('type');  // or request('type')

        // Load dropdown choices
        $this->branches = Branch::where('status', 'ACTIVE')->orderBy('name')->get(['id', 'name']);

        $this->equipments = Equipment::with('airline:id,name')
            ->where('status', 'ACTIVE')
            ->orderBy('airline_id')
            ->get(['id', 'registration', 'airline_id']);

        // Load routes
        // $this->loadAirlineRoutes();
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
            $this->source = ScheduledFlights::with('days')->findOrFail($id);

            $this->isEdit = true;

            $this->flight_number = $this->source->flight_no;
            $this->branch_id = $this->source->branch_id;
            $this->airline_route_id = $this->source->airline_route_id;
            $this->equipment_id = $this->source->equipment_id;
            $this->sched_dep = substr($this->source->sched_dep, 0, 5);
            $this->sched_arr = substr($this->source->sched_arr, 0, 5);

        }else if($this->type === 'actual')//Data from flights table
        {
            $this->flightSource = Flight::findOrFail($id);

            $this->isEdit = true;

            $this->flight_number = $this->flightSource->flight_no;
            $this->branch_id = $this->flightSource->branch_id;
            $this->airline_route_id = $this->flightSource->airline_route_id;
            $this->equipment_id = $this->flightSource->equipment_id;

            $this->sched_dep = substr($this->flightSource->sched_dep, 0, 5);
            $this->sched_arr = substr($this->flightSource->sched_arr, 0, 5);
            $this->actual_dep = substr($this->flightSource->actual_dep, 0, 5);
            $this->actual_arr = substr($this->flightSource->actual_arr, 0, 5);

            $this->pax = $this->flightSource->pax;
            $this->pic = $this->flightSource->pic;
            $this->ground_time = substr($this->flightSource->ground_time, 0, 5);
            $this->notes = $this->flightSource->notes;
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

        // --- 1️⃣ Validation ---
        $this->validate([
            'flight_number'   => ['required', 'string', 'max:10'],
            'branch_id'       => ['required', 'integer', 'exists:branches,id'],
            'airline_route_id'=> ['required', 'integer', 'exists:airline_routes,id'], 
            'equipment_id'    => ['required', 'integer', 'exists:equipments,id'],
            'sched_dep'       => ['required'],
            'sched_arr'       => ['required', 'after_or_equal:sched_dep'],
            'actual_dep'      => ['required'],
            'actual_arr'      => ['required', 'after_or_equal:actual_dep'],
            'notes'           => ['nullable', 'string', 'max:255'],
        ]);

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
        ];

        if ($this->isEdit) {
            $payload['updated_by'] = $userId;
        } else {
            $payload['created_by'] = $userId;
            $payload['updated_by'] = $userId;
        }

        // --- 4️⃣ Save or update ---
        $this->record = Flight::updateOrCreate(
            ['id' => $this->record?->id],
            $payload
        );

        // --- 6️⃣ Flash message ---
        session()->flash('notify', [
            'content' => 'Flight Journal recorded successfully!',
            'type'    => 'success',
        ]);

        return redirect()->route('flight-journal.actual');
    }

    public function delete()
    {
        $row = ScheduledFlights::find($this->record?->id);
        $name = $row?->flight_no ?? 'Unknown';

        if (!$row) {
            session()->flash('notify', [
                'content' => 'Penerbangan terjadwal tidak ditemukan',
                'type' => 'error'
            ]);
        }

        try {
            $row->delete();

            session()->flash('notify', [
                'content' => $name . ' berhasil dihapus!',
                'type' => 'success'
            ]);
            $this->redirectRoute('flight-schedule.actual', navigate: true);
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

