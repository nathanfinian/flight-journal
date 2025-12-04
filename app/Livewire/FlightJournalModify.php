<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Flight;
use App\Models\Airline;
use Livewire\Component;
use App\Models\Equipment;
use Illuminate\Support\Str;
use App\Models\AirlineRoute;
use App\Models\ScheduledFlights;
use App\Livewire\Forms\ActualFlightsForm;
use App\Models\FlightType;

class FlightJournalModify extends Component
{
    public ActualFlightsForm $form;

    public ?Flight $record = null;
    public ?ScheduledFlights $scheduledFlight = null;

    public $branches;
    public $flightTypes;
    public $equipments;
    public $airlineRoutes;
    public $airlines;
    
    public bool $isEdit = false;

    public ?string $airlineIata = null;
    public ?string $airline_id = '';

    public ?string $type = '';

    public function mount(?int $id = null)
    {
        $this->type = request()->query('type');

        // Load dropdown choices
        $this->branches = Branch::where('status', 'ACTIVE')->orderBy('name')->get(['id', 'name']);
        $this->flightTypes = FlightType::orderBy('id')->get(['id', 'name']);
        $this->airlines = Airline::where('status', 'ACTIVE')->orderBy('name')->get(['id', 'name']);

        $this->equipments = Equipment::with('airline:id,name')
            ->where('status', 'ACTIVE')
            ->orderBy('airline_id')
            ->get(['id', 'registration', 'airline_id']);

        // Load routes
        $this->airlineRoutes = AirlineRoute::query() //Fix routes and equipments
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

        if($id == null){
            $this->form->setDate();
            return; // go to create, when no data retrieved
        }
        else if($this->type === 'scheduled') //Data from scheduled flight
        {
            $this->scheduledFlight = ScheduledFlights::findOrFail($id);

            $this->form->setRecord(null, $this->scheduledFlight);
        }
        else if($this->type === 'actual' || $this->type === 'history')//Data from flights table
        {
            $this->record = Flight::findOrFail($id);

            $this->isEdit = true;

            $this->form->setRecord($this->record, null);

        }
        else{
            session()->flash('notify', [
                'content' => 'Error, tipe data tidak terdaftar!',
                'type'    => 'error',
            ]);

            if ($this->type === 'scheduled') {
                return redirect()->route('flight-journal');
            } else if($this->type === 'actual'){
                return redirect()->route('flight-journal.actual');           
            } else{
                return redirect()->route('flight-history');
            }           
        }
    }

    public function saveChanges()
    {
        $airline = $this->form->save($this->isEdit);

        // --- 6️⃣ Flash message ---
        session()->flash('notify', [
            'content' => 'Flight ' . $airline . ' recorded successfully!',
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
        $this->form->record?->delete();

        session()->flash('notify', [
            'content' => 'Journal penerbangan berhasil dihapus!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('flight-journal.actual', navigate: true);
    }

    public function updatedFormAirlineId($value)
    {
        $this->loadEquipmentRoute($value);

        $this->reset([
            'form.origin_equipment',
            'form.departure_equipment',
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

        // Prefill/normalize the flight number prefix
        if ($this->airlineIata) {
            // keep only digits after the prefix
            $digits = preg_replace('/\D/', '', $this->form->origin_flight_number);
            $this->form->origin_flight_number = Str::upper($this->airlineIata) . $digits;

            $digits2 = preg_replace('/\D/', '', $this->form->departure_flight_number);
            $this->form->departure_flight_number = Str::upper($this->airlineIata) . $digits2;
        }
    }

    public function render()
    {
        return view('livewire.flight-journal-modify');
    }
}

