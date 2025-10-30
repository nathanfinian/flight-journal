<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Day;
use App\Models\Branch;
use Livewire\Component;
use App\Models\Equipment;
use App\Models\AirlineRoute;
use Illuminate\Validation\Rule;
use App\Models\ScheduledFlights;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class FlightScheduleModify extends Component
{
    public $branches;
    public $equipments;
    public $flightRoutes;
    public $dayList;

    public ?ScheduledFlights $record = null;
    public bool $isEdit = false;

    // Form properties
    public ?string $flight_number = null;
    public ?string $branch_id = '';
    public ?string $airline_route_id = '';
    public ?string $equipment_id = '';
    public ?string $sched_dep = '';
    public ?string $sched_arr = '';
    public array $days = [];

    public function mount(?int $scheduled = null)
    {
        // Load dropdown choices
        $this->branches = Branch::where('status', 'ACTIVE')->orderBy('name')->get(['id', 'name']);
        $this->equipments = Equipment::with('airline:id,name')
            ->where('status', 'ACTIVE')
            ->orderBy('airline_id')
            ->get(['id', 'registration', 'airline_id']);

        // Load routes
        $this->flightRoutes = AirlineRoute::query()
            ->with([
                'airline:id,name',
                'airportRoute.origin:id,iata',
                'airportRoute.destination:id,iata',
            ])
            ->get()
            ->mapWithKeys(fn($r) => [
                $r->id => "{$r->airportRoute->origin->iata} ➜ {$r->airportRoute->destination->iata} - {$r->airline->name}"
            ])
            ->toArray();

        $this->dayList = Day::orderBy('id')->get(['id', 'day_name']);

        // Edit mode
        if ($scheduled) {
            $this->record = ScheduledFlights::with('days')->findOrFail($scheduled);
            $this->isEdit = true;

            $this->flight_number = $this->record->flight_no;
            $this->branch_id = $this->record->branch_id;
            $this->airline_route_id = $this->record->airline_route_id;
            $this->equipment_id = $this->record->equipment_id;
            $this->sched_dep = substr($this->record->sched_dep, 0, 5);
            $this->sched_arr = substr($this->record->sched_arr, 0, 5);
            $this->days = $this->record->days
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        }
    }

    public function saveChanges()
    {
        $this->sched_dep = $this->formatTime($this->sched_dep);
        $this->sched_arr = $this->formatTime($this->sched_arr);

        // --- 1️⃣ Validation ---
        $this->validate([
            'flight_number'   => ['required', 'string', 'max:10', Rule::unique('scheduled_flights', 'flight_no')->ignore($this->record?->id)],
            'branch_id'       => ['required', 'integer', 'exists:branches,id'],
            'airline_route_id' => ['required', 'integer', 'exists:airline_route,id'], 
            'equipment_id'    => ['integer', 'exists:equipments,id'],
            'sched_dep'       => ['required'],
            'sched_arr'       => ['required'],
            'days'            => ['required', 'array', 'min:1'],
            'days.*'          => ['integer', 'exists:days,id'],
        ]);

        // --- 2️⃣ Composite uniqueness ---
        $this->validate([
            'airline_route_id' => [
                Rule::unique('scheduled_flights', 'airline_route_id')
                    ->where(
                        fn($q) => $q
                            ->where('branch_id', $this->branch_id)
                            ->where('equipment_id', $this->equipment_id)
                            ->where('sched_dep', $this->sched_dep)
                    )
                    ->ignore($this->record?->id),
            ],
        ], [
            'airline_route_id.unique' => 'This flight schedule already exists for the same branch, equipment, and ETD.',
        ]);

        // --- 3️⃣ Audit fields ---
        $userId = Auth::id();
        $payload = [
            'flight_no'        => strtoupper($this->flight_number),
            'branch_id'        => $this->branch_id,
            'airline_route_id' => $this->airline_route_id,
            'equipment_id'     => $this->equipment_id,
            'sched_dep'        => $this->sched_dep,
            'sched_arr'        => $this->sched_arr,
        ];

        if ($this->isEdit) {
            $payload['updated_by'] = $userId;
        } else {
            $payload['created_by'] = $userId;
            $payload['updated_by'] = $userId;
        }

        // --- 4️⃣ Save or update ---
        $this->record = ScheduledFlights::updateOrCreate(
            ['id' => $this->record?->id],
            $payload
        );

        // --- 5️⃣ Sync pivot ---
        if (!empty($this->days)) {
            $this->record->days()->sync($this->days);
        }

        // --- 6️⃣ Flash message ---
        session()->flash('notify', [
            'content' => 'Flight schedule saved successfully!',
            'type'    => 'success',
        ]);

        return redirect()->route('flight-schedule');
    }

    public function delete()
    {
        $row = ScheduledFlights::find($this->record?->id);
        $name = $row?->flight_no ?? 'Unknown';

        if (!$row) {
            session()->flash('notify', [
                'content' => 'Scheduled Flight not found',
                'type' => 'error'
            ]);
        }

        try {
            $row->delete();

            session()->flash('notify', [
                'content' => $name . ' deleted successfully!',
                'type' => 'success'
            ]);
            $this->redirectRoute('flight-schedule', navigate: true);
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

    private function formatTime(?string $time): ?string
    {
        if (empty($time)) {
            return null;
        }

        // Match HH:MM exactly (00–23 : 00–59)
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'time_format' => 'Invalid format waktu: harus 24-jam HH:MM (00:00–23:59)',
            ]);
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
        return view('livewire.flight-schedule-modify');
    }
}
