<?php

namespace App\Http\Controllers\Export;

use Illuminate\Http\Request;
use App\Exports\FlightsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Flight;

class FlightExportController extends Controller
{
    private ?string $dateFrom = '';
    private ?string $dateTo = '';
    private ?string $branch = '';
    private ?string $airline = '';

    public function export(Request $request)
    {
        //Get dates and names for display
        $this->dateFrom = $request->dateFrom;
        $this->dateTo = $request->dateTo;
        $this->branch = $request->branchName;
        $this->airline = $this->prependSpace($request->airlineName);

        return Excel::download(
            new FlightsExport(
                $request->dateFrom,
                $request->dateTo,
                $request->branch,
                $request->airline,
                $request->flightNo,
            ),
            $this->branch . ' ' . $this->dateFrom . ' sampai ' . $this->dateTo . $this->airline . ' Flights.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        //Get dates and names for display
        $this->dateFrom = $request->dateFrom;
        $this->dateTo = $request->dateTo;
        $this->branch = $request->branchName;
        $this->airline = $this->prependSpace($request->airlineName);

        return Excel::download(
            new FlightsExport(
                $request->dateFrom,
                $request->dateTo,
                $request->branch,
                $request->airline,
                $request->flightNo,
            ),
            $this->branch . ' ' . $this->dateFrom . ' sampai ' . $this->dateTo . ' ' . $this->airline . ' Flights.pdf',
             \Maatwebsite\Excel\Excel::MPDF);
    }

    public function print(Request $request)
    {
        //Get variables for data query
        $dateFrom = $request->dateFrom;
        $dateTo = $request->dateTo;

        $branch = $request->branch;
        $airline = $request->airline;
        $flightNo = $request->flightNo;

        //Prepare string for display
        $airlineName = $request->airlineName
            ? ' - ' . $request->airlineName
            : '';
        $branchName = $request->branchName
            ? 'Cabang ' . $request->branchName
            : '';

        $data = $this->getFlights($branch, $airline, $dateFrom, $dateTo, $flightNo);

        $dateFrom = $this->readableDate($dateFrom);
        $dateTo = $this->readableDate($dateTo);

        return view('print.flight-history', [
            'flights'     => $data,
            'branch'      => $branchName,
            'airline'     => $airlineName,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
        ]);
    }

    private function readableDate(?string $value): string
    {
        if (!$value) {
            return '';
        }

        return \Carbon\Carbon::parse($value)
            ->locale('id')
            ->translatedFormat('d M Y');
    }

    private function prependSpace(?string $value): string
    {
        return $value ? ' ' . $value : '';
    }

    private function getFlights($branch, $airline, $from, $to, $flightNo)
    {
        return Flight::query()
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
            // Filter branch
            ->when($branch, fn($q) => $q->where('branch_id', $branch))

            // Filter airline
            ->when(
                $airline,
                fn($q) =>
                $q->whereHas(
                    'originAirlineRoute',
                    fn($r) => $r->where('airline_id', $airline)
                )
            )
            //Filter based on Flight Number
            ->when($flightNo, fn($q) => $q->where(fn($sub) => $sub
                    ->where('origin_flight_no', 'like', "%{$flightNo}%")
                    ->orWhere('departure_flight_no', 'like', "%{$flightNo}%")
                )
            )

            // DATE FILTERS
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
            ->orderBy('actual_arr', 'asc')
            ->get();
    }
}
