<?php

namespace App\Exports;

use App\Models\Flight;
use App\Models\AirlineRate;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MonthlyFlightExport implements FromView, ShouldAutoSize
{
    private ?Collection $records = null;

    public function __construct(
        private string $month,
        private string $flightNo,
        private ?string $branch = null,
    ) {}

    public function flights(): Collection
    {
        $start = CarbonImmutable::createFromFormat('!Y-m', $this->month, 'Asia/Jakarta');

        return $this->records ??= Flight::query()
            ->with([
                'originEquipment:id,registration',
                'departureEquipment:id,registration',
                'originAirlineRoute.airportRoute.origin:id,iata',
                'originAirlineRoute.airportRoute.destination:id,iata',
                'departureAirlineRoute.airportRoute.origin:id,iata',
                'departureAirlineRoute.airportRoute.destination:id,iata',
            ])
            ->when($this->branch, fn ($query) => $query->where('branch_id', $this->branch))
            ->whereBetween('service_date', [$start->toDateString(), $start->endOfMonth()->toDateString()])
            ->where(fn ($query) => $query->where('origin_flight_no', $this->flightNo)
                ->orWhere('departure_flight_no', $this->flightNo))
            ->orderBy('service_date')
            ->orderBy('actual_arr')
            ->orderBy('id')
            ->get();
    }

    public function view(): View
    {
        $start = CarbonImmutable::createFromFormat('!Y-m', $this->month, 'Asia/Jakarta');
        $days = [];
        for ($day = $start; $day->month === $start->month; $day = $day->addDay()) {
            $days[] = $day;
        }

        $flights = $this->flights();
        $rates = AirlineRate::query()
            ->with('flightTypes')
            ->whereIn('airline_id', $flights->pluck('departureAirlineRoute.airline_id')->filter()->unique())
            ->whereIn('branch_id', $flights->pluck('branch_id')->unique())
            ->whereDate('date_from', '<=', $start->endOfMonth()->toDateString())
            ->whereDate('date_to', '>=', $start->toDateString())
            ->orderByDesc('date_from')
            ->orderByDesc('id')
            ->get();

        $charges = [];
        foreach ($flights as $flight) {
            $rate = $rates->first(fn ($rate) =>
                $rate->airline_id == $flight->departureAirlineRoute?->airline_id
                && $rate->branch_id == $flight->branch_id
                && $flight->service_date->betweenIncluded($rate->date_from, $rate->date_to)
            );

            $percentage = $rate?->flightTypes->firstWhere('id', $flight->flight_type_id)?->pivot?->percentage ?? 100;
            $charges[$flight->id] = $rate && $rate->ground_fee !== null
                ? round((float) $rate->ground_fee * (float) $percentage / 100, 2)
                : null;
        }

        return view('exports.monthly-flight', [
            'days' => $days,
            'flightsByDate' => $flights->groupBy(fn ($flight) => $flight->service_date->toDateString()),
            'charges' => $charges,
        ]);
    }
}
