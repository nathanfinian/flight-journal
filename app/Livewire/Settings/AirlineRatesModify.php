<?php

namespace App\Livewire\Settings;

use App\Models\Airline;
use App\Models\Branch;
use Livewire\Component;
use App\Models\FlightType;
use App\Models\AirlineRate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class AirlineRatesModify extends Component
{
    public $airlines;
    public $branches;
    public $flightTypes;

    public array $percentages = [];

    public ?string $airline_id = '';
    public ?string $branch_id = '';
    
    public ?string $airlineRateId = '';
    public ?string $charge_name = '';
    public ?string $charge_code = '';
    public ?string $date_from = '';
    public ?string $date_to = '';
    public ?string $ground_fee = '';
    public ?string $delay_rate = '';
    public ?string $cargo_fee = '';
    public ?string $ppn_rate = '';
    public ?string $pph_rate = '';
    public ?string $konsesi_rate = '';

    public bool $isEdit = false;

    public function mount(?AirlineRate $airlineRate): void
    {
        //  Load choices
        $this->airlines = Airline::query()
            ->orderBy('name')     // or ->orderBy('name')
            ->get(['id', 'name', 'icao_code']);

        $this->branches = Branch::query()
            ->where('status', 'ACTIVE')
            ->orderBy('name')
            ->get(['id', 'name']);

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
            $this->isEdit          = true;
            $this->airlineRateId   = $airlineRate->getKey();
            $this->airline_id      = $airlineRate->airline_id;
            $this->branch_id       = (string) $airlineRate->branch_id;
            $this->charge_name     = (string) $airlineRate->charge_name;
            $this->charge_code     = (string) $airlineRate->charge_code;
            $this->date_from       = $airlineRate->date_from?->format('Y-m-d');
            $this->date_to         = $airlineRate->date_to?->format('Y-m-d');
            $this->ground_fee      = $this->toMoneyFormat($airlineRate->ground_fee);
            $this->delay_rate      = $this->toMoneyFormat($airlineRate->delay_rate);
            $this->cargo_fee       = $this->toMoneyFormat($airlineRate->cargo_fee);
            $this->ppn_rate        = $this->toRateFormat($airlineRate->ppn_rate);
            $this->pph_rate        = $this->toRateFormat($airlineRate->pph_rate);
            $this->konsesi_rate    = $this->toRateFormat($airlineRate->konsesi_rate);

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
            'branch_id'     => ['required', 'exists:branches,id'],
            'charge_name'   => ['required', 'string', 'max:120'],
            'charge_code'   => ['required', 'string', 'max:15'],
            'date_from'     => ['required', 'date'],
            'date_to'       => ['required', 'date', 'after_or_equal:date_from'],
            'ground_fee'    => ['required','regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$|^\d+(,\d{1,2})?$/'],
            'delay_rate'    => ['nullable','regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$|^\d+(,\d{1,2})?$/'],
            'cargo_fee'     => ['nullable','regex:/^\d{1,3}(\.\d{3})*(,\d{1,2})?$|^\d+(,\d{1,2})?$/'], //Change money input rules
            'ppn_rate'      => ['required', 'numeric', 'min:0', 'max:100'],
            'pph_rate'      => ['required', 'numeric', 'min:0', 'max:100'],
            'konsesi_rate'  => ['required', 'numeric', 'min:0', 'max:100'],
            'percentages.*.percentage' => 'nullable|numeric|min:0|max:100',
        ];
    }
    public function saveChanges()
    {
        $payload = $this->validate();

        $payload['ground_fee'] = $this->toDecimal($this->ground_fee);
        $payload['delay_rate'] = $this->delay_rate
            ? $this->toDecimal($this->delay_rate)
            : null;
        $payload['cargo_fee']  = $this->cargo_fee
            ? $this->toDecimal($this->cargo_fee)
            : null;
        $payload['ppn_rate'] = $this->toRateDecimal($this->ppn_rate);
        $payload['pph_rate'] = $this->toRateDecimal($this->pph_rate);
        $payload['konsesi_rate'] = $this->toRateDecimal($this->konsesi_rate);

        // Save airline rate
        $airlineRate = AirlineRate::updateOrCreate(
            ['id' => $this->airlineRateId],
            $payload
        );

        // 🔥 SAVE PERCENTAGES TO PIVOT TABLE
        $syncData = [];

        foreach ($this->percentages as $row) {
            if ($row['percentage'] === null || $row['percentage'] === '') {
                continue;
            }

            $syncData[$row['flight_type_id']] = [
                'percentage' => (float) $row['percentage'],
                'created_by' => Auth::id(),
                'updated_at' => now(),
            ];
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

    public function deletePivot(int $flightTypeId)
    {
        unset($this->percentages[$flightTypeId]);
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

    private function toRateFormat($value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Show as percentage input: 0.11 -> 11, 0.075 -> 7.5
        $numeric = (float) $value;
        $display = $numeric <= 1 ? ($numeric * 100) : $numeric;

        return rtrim(rtrim(number_format($display, 4, '.', ''), '0'), '.');
    }

    private function toRateDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $numeric = (float) str_replace(',', '.', trim((string) $value));

        // User enters percentage (11 / 7.5), store as decimal (0.11 / 0.075).
        if ($numeric > 1) {
            return round($numeric / 100, 4);
        }

        return round($numeric, 4);
    }

    public function render()
    {
        return view('livewire.settings.airline-rates-modify');
    }
}
