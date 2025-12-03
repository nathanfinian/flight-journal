<?php

namespace App\Exports;

use App\Models\Flight;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FlightsExport implements FromView
{
    private ?string $from = ''; // 'YYYY-MM-DD'
    private ?string $to   = '';
    private ?string $branch   = '';
    private ?string $airline   = '';

    public function __construct($from, $to, $branch, $airline)
    {
        $this->from = $from;
        $this->to = $to;
        $this->branch = $branch;
        $this->airline = $airline;
    }

    public function view(): View
    {
        $from = $this->from;
        $to   = $this->to;

        $flights = Flight::query()
            ->with([
                'branch:id,name',
                'originEquipment:id,registration',
                'departureEquipment:id,registration',
                'originAirlineRoute.airline:id,name',
                'originAirlineRoute.airportRoute.origin:id,iata',
                'originAirlineRoute.airportRoute.destination:id,iata',
                'departureAirlineRoute.airportRoute.origin:id,iata',
                'departureAirlineRoute.airportRoute.destination:id,iata',
            ])
            ->when($this->branch, fn($q) => $q->where('branch_id', $this->branch))
            ->when(
                $this->airline,
                fn($q) =>
                $q->whereHas(
                    'originAirlineRoute',
                    fn($r) => $r->where('airline_id', $this->airline)
                )
            )
            // --- DATE FILTERS (exactly like Livewire) ---
            ->when(
                $from && $to,
                fn($q) => $q->whereBetween('service_date', [$from, $to])
            )
            ->when(
                $from && !$to,
                fn($q) => $q->whereDate('service_date', '>=', $from)
            )
            ->when(
                !$from && $to,
                fn($q) => $q->whereDate('service_date', '<=', $to)
            )
            ->orderBy('service_date', 'asc')
            ->get();

        return view('exports.flights', [
            'flights' => $flights,
            'from'    => $this->from,
            'to'      => $this->to,
        ]);
    }
}
