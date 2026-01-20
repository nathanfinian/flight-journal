<?php

namespace App\Livewire\Settings;

use App\Models\Airline;
use Livewire\Component;
use App\Models\FlightType;
use App\Models\AirlineRate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class AirlineRatesModify extends Component
{
    public $airlines;
    public $flightTypes;

    public array $percentages = [];

    public ?string $airline_id = '';
    
    public ?string $airlineRateId = '';
    public ?string $charge_name = '';
    public ?string $charge_code = '';
    public ?string $ground_fee = '';
    public ?string $cargo_fee = '';

    public bool $isEdit = false;

    public function mount(?AirlineRate $airlineRate): void
    {
        //  Load choices
        $this->airlines = Airline::query()
            ->orderBy('name')     // or ->orderBy('name')
            ->get(['id', 'name', 'icao_code']);

        $this->flightTypes = FlightType::query()
            ->orderBy('id')     // or ->orderBy('name')
            ->get(['id', 'name']);

        foreach ($this->flightTypes as $type) {
            $this->percentages[$type->id] = [
                'flight_type_id' => $type->id,
                'percentage' => null,
            ];
        }

        if ($airlineRate) {
            $this->airlineRateId   = $airlineRate->getKey();
            $this->airline_id      = $airlineRate->airline_id;
            $this->charge_name     = (string) $airlineRate->charge_name;
            $this->charge_code     = (string) $airlineRate->charge_code;
            $this->ground_fee      = $this->toMoneyFormat($airlineRate->ground_fee);
            $this->cargo_fee       = $this->toMoneyFormat($airlineRate->cargo_fee);

            foreach ($airlineRate->flightTypes as $type) {
                $this->percentages[$type->id] = [
                    'flight_type_id' => $type->id,
                    'percentage' => number_format($type->pivot->percentage),
                ];
            }
        }else{
            session()->flash(
                'notify',
                [
                    'content' => 'Buat Rate Airline Baru!',
                    'type' => 'warning'
                ]);
                return;
        }
    }

    protected function rules(): array
    {
        return [
            'airline_id'    => ['required', 'exists:airlines,id'],
            'charge_name'   => ['required', 'string', 'max:120'],
            'charge_code'   => ['required', 'string', 'max:15'],
            'ground_fee'    => ['required','regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$|^\d+(,\d{1,2})?$/'],
            'cargo_fee'     => ['nullable','regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$|^\d+(,\d{1,2})?$/'], //Change money input rules
            'percentages.*.percentage' => 'nullable|numeric|min:0|max:100',
        ];
    }
    public function saveChanges()
    {
        $payload = $this->validate();

        $payload['ground_fee'] = $this->toDecimal($this->ground_fee);
        $payload['cargo_fee']  = $this->cargo_fee
            ? $this->toDecimal($this->cargo_fee)
            : null;

        // Save airline rate
        $airlineRate = AirlineRate::updateOrCreate(
            ['id' => $this->airlineRateId],
            $payload
        );

        // 🔥 SAVE PERCENTAGES TO PIVOT TABLE
        $syncData = [];

        foreach ($this->percentages as $row) {
            if ($row['percentage'] !== null) {
                $syncData[$row['flight_type_id']] = [
                    'percentage' => $row['percentage'],
                    'created_by' => Auth::id(),
                    'updated_at' => now(),
                ];
            }
        }

        $airlineRate->flightTypes()->sync($syncData);

        session()->flash('notify', [
            'content' => 'Rate airline berhasil disimpan!',
            'type' => 'success'
        ]);

        return $this->redirectRoute('settings.airlineRates', navigate: true);
    }

    public function delete()
    {
        $row = AirlineRate::find($this->airlineRateId);
        $name = 'Rate airline ' . ($row?->name ?? 'Unknown');

        if (!$row) {
            session()->flash('notify', [
                'content' => 'Rate airline tidak ditemukan',
                'type' => 'error'
            ]);
        }

        try {
            $row->delete();

            session()->flash('notify', [
                'content' => $name . ' berhasil dihapus!',
                'type' => 'success'
            ]);
            $this->redirectRoute('settings.airlineRates', navigate: true);
        } catch (QueryException $e) {
            // 23000 => integrity constraint violation (FK in use, etc.)
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Rate airline ini dipakai catatan penerbangan dan tidak dapat dihapus.',
                    'type' => 'warning'
                ]);
                return;
            }
            // Re-throw unexpected errors for visibility in logs
            throw $e;
        }
    }

    private function toDecimal($value)
    {
        return str_replace(['.', ','], ['', '.'], $value);
    }

    private function toMoneyFormat($value)
    {
        if ($value === null || $value === '') return '';

        return number_format($value, 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.settings.airline-rates-modify');
    }
}
