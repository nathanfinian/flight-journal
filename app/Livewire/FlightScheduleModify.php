<?php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;

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

    public string $airlineIata = '';

    public bool $isEdit = false;

    public function mount(?int $scheduled = null)
    {
        $this->branches = Branch::where('status', 'ACTIVE')->orderBy('name')->get();
        $this->airlines = Airline::where('status', 'ACTIVE')->orderBy('name')->get();
        $this->dayList  = Day::orderBy('id')->get();

        $this->equipments = Equipment::where('status', 'ACTIVE')->get();

        $this->airlineRoutes = AirlineRoute::query()
            ->with([
                'airline:id,name',
                'airportRoute.origin:id,iata',
                'airportRoute.destination:id,iata',
            ])
            ->get()
            ->mapWithKeys(fn($r) => [
                $r->id => "{$r->airline->name} - {$r->airportRoute->origin->iata} ➜ {$r->airportRoute->destination->iata}",
            ])
            ->toArray();

        if ($scheduled) {
            $this->isEdit = true;
            $this->flight   = ScheduledFlights::with('days')->findOrFail($scheduled);

            $this->form->setRecord($this->flight);
        }
    }

    public function updatedFormAirlineId($value)
    {
        $this->loadEquipmentRoute($value);

        $this->reset([
            'form.equipment_id',
            'form.origin_route',
            'form.departure_route',
        ]);
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
                'airline:id,name',
                'airportRoute.origin:id,iata',
                'airportRoute.destination:id,iata',
            ])
            ->get()
            ->mapWithKeys(fn($r) => [
                $r->id => "{$r->airline->name} - {$r->airportRoute->origin->iata} ➜ {$r->airportRoute->destination->iata}",
            ])
            ->toArray();

        // 2.3 Fetch IATA for masking/prefixing the flight number
        $this->airlineIata = $airlineId
            ? Airline::whereKey($airlineId)->value('iata_code')
            : null;

        // Optional: prefill/normalize the flight number prefix
        if ($this->airlineIata) {
            // keep only digits after the prefix
            $digits = preg_replace('/\D/', '', $this->form->arrival_flight_number);
            $this->form->arrival_flight_number = Str::upper($this->airlineIata) . $digits;

            $digits2 = preg_replace('/\D/', '', $this->form->departure_flight_number);
            $this->form->departure_flight_number = Str::upper($this->airlineIata) . $digits2;
        }
    }

    public function saveChanges()
    {
        $flight = $this->form->save();

        session()->flash('notify', [
            'content' => 'Jadwal Penerbangan ' . $flight->airline->name .  ' berhasil disimpan!',
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

