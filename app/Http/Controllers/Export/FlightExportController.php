<?php

namespace App\Http\Controllers\Export;

use Illuminate\Http\Request;
use App\Exports\FlightsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Flight;
use Illuminate\Support\Facades\DB;

class FlightExportController extends Controller
{
    private ?string $dateFrom = '';
    private ?string $dateTo = '';
    private ?string $branch = '';
    private ?string $flightNo = '';
    private ?string $airline = '';
    private ?string $type = '';

    public function export(Request $request)
    {
        //Get dates and names for display
        $this->dateFrom = $request->dateFrom;
        $this->dateTo = $request->dateTo;
        $this->branch = $request->branchName;
        $this->flightNo = $this->prependSpace($request->flightNo);
        $this->airline = $this->prependSpace($request->airlineName);
        $this->type = $this->prependSpace($request->typeName);

        return Excel::download(
            new FlightsExport(
                $request->dateFrom,
                $request->dateTo,
                $request->branch,
                $request->airline,
                $request->flightNo,
                $request->type,
            ),
            $this->documentName('xlsx')
        );
    }

    public function exportPdf(Request $request)
    {
        //Get dates and names for display
        $this->dateFrom = $request->dateFrom;
        $this->dateTo = $request->dateTo;
        $this->branch = $request->branchName;
        $this->flightNo = $this->prependSpace($request->flightNo);
        $this->airline = $this->prependSpace($request->airlineName);
        $this->type = $this->prependSpace($request->typeName);

        return Excel::download(
            new FlightsExport(
                $request->dateFrom,
                $request->dateTo,
                $request->branch,
                $request->airline,
                $request->flightNo,
                $request->type,
            ),
            $this->documentName('pdf'),
             \Maatwebsite\Excel\Excel::MPDF);
    }

    public function print(Request $request)
    {
        //Get variables for data query
        $dateFrom = $request->dateFrom;
        $dateTo = $request->dateTo;

        $branch = $request->branch;
        $airline = $request->airline;
        $type = $request->type;
        $flightNo = $request->flightNo;

        //Prepare string for display
        $airlineName = $request->airlineName
            ? ' - ' . $request->airlineName
            : '';
        $branchName = $request->branchName
            ? 'Cabang ' . $request->branchName
            : '';
        $typeName = $request->typeName
            ? ' - ' . $request->typeName
            : '';

        $data = $this->getFlights($branch, $airline, $type, $dateFrom, $dateTo, $flightNo);

        $dateFrom = $this->readableDate($dateFrom);
        $dateTo = $this->readableDate($dateTo);

        return view('print.flight-history', [
            'flights'     => $data,
            'branch'      => $branchName,
            'airline'     => $airlineName,
            'type'        => $typeName,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
        ]);
    }

    public function printSummary(Request $request)
    {
        $dateFrom = $request->dateFrom;
        $dateTo = $request->dateTo;

        $branch = $request->branch;
        $airline = $request->airline;
        $type = $request->type;
        $flightNo = $request->flightNo;

        $airlineName = $request->airlineName
            ? ' - ' . $request->airlineName
            : '';
        $branchName = $request->branchName
            ? 'Cabang ' . $request->branchName
            : '';
        $typeName = $request->typeName
            ? ' - ' . $request->typeName
            : '';

        $delayChargeRows = $this->getDelayChargeSummaryRows($branch, $airline, $type, $dateFrom, $dateTo, $flightNo);
        $flightRows = $this->getFlightSummaryRows($branch, $airline, $type, $dateFrom, $dateTo, $flightNo);
        $summaryRows = $delayChargeRows->concat($flightRows)->values();

        return view('print.flight-history-summary', [
            'summaryRows' => $summaryRows,
            'totalFlights' => $summaryRows->sum('quantity'),
            'totalDelayCharges' => $delayChargeRows->sum('quantity'),
            'branch' => $branchName,
            'airline' => $airlineName,
            'type' => $typeName,
            'dateFrom' => $this->readableDate($dateFrom),
            'dateTo' => $this->readableDate($dateTo),
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

    private function documentName(string $extension): string
    {
        $name = implode(' ', array_filter([
            trim((string) $this->flightNo),
            trim((string) $this->branch),
            $this->readableDateRange(),
            trim((string) $this->airline),
            trim((string) $this->type),
        ]));

        return $this->safeFilename($name . '.' . $extension);
    }

    private function readableDateRange(): string
    {
        $dateFrom = $this->readableDocumentDate($this->dateFrom);
        $dateTo = $this->readableDocumentDate($this->dateTo);

        if ($dateFrom && $dateTo) {
            return $dateFrom . ' sampai ' . $dateTo;
        }

        if ($dateFrom) {
            return 'Mulai ' . $dateFrom;
        }

        if ($dateTo) {
            return 'Sampai ' . $dateTo;
        }

        return '';
    }

    private function readableDocumentDate(?string $value): string
    {
        if (!$value) {
            return '';
        }

        return \Carbon\Carbon::parse($value)
            ->locale('id')
            ->translatedFormat('d F Y');
    }

    private function safeFilename(string $value): string
    {
        return preg_replace('/[\\\\\/:*?"<>|]+/', '-', $value) ?? $value;
    }

    private function getFlightSummaryRows($branch, $airline, $type, $from, $to, $flightNo)
    {
        $billingFlightNoExpression = "
            CASE
                WHEN (LOWER(ft.name) LIKE '%ferry%' OR LOWER(ft.type_code) IN ('ferry', 'fry'))
                    THEN COALESCE(af.ferry_flight_no, af.departure_flight_no)
                ELSE af.departure_flight_no
            END
        ";

        $ferryDirectionExpression = "
            CASE
                WHEN NOT (LOWER(ft.name) LIKE '%ferry%' OR LOWER(ft.type_code) IN ('ferry', 'fry'))
                    THEN NULL
                WHEN COALESCE(af.ferry_flight_no, af.departure_flight_no) = af.origin_flight_no
                    THEN 'Arrival'
                WHEN COALESCE(af.ferry_flight_no, af.departure_flight_no) = af.departure_flight_no
                    THEN 'Departure'
            END
        ";

        $baseQuery = DB::table('actual_flights as af')
            ->leftJoin('airline_routes as ar_dep', 'ar_dep.id', '=', 'af.departure_route_id')
            ->leftJoin('airport_routes as apr_dep', 'apr_dep.id', '=', 'ar_dep.airport_route_id')
            ->leftJoin('airports as ap_dep_from', 'ap_dep_from.id', '=', 'apr_dep.origin_id')
            ->leftJoin('airports as ap_dep_to', 'ap_dep_to.id', '=', 'apr_dep.destination_id')
            ->leftJoin('airlines as al_dep', 'al_dep.id', '=', 'ar_dep.airline_id')
            ->leftJoin('flight_types as ft', 'ft.id', '=', 'af.flight_type_id')
            ->select(
                'af.flight_type_id',
                'ft.type_code as flight_type',
                'ft.name as flight_type_name',
                'al_dep.name as airline',
                DB::raw("CONCAT(ap_dep_from.iata, ' - ', ap_dep_to.iata) as route"),
                DB::raw("{$billingFlightNoExpression} as departure_flight_no"),
                DB::raw("{$ferryDirectionExpression} as ferry_direction")
            )
            ->when($branch, fn($q) => $q->where('af.branch_id', $branch))
            ->when($type, fn($q) => $q->where('af.flight_type_id', $type))
            ->when($airline, fn($q) => $q->where('ar_dep.airline_id', $airline))
            ->when($flightNo, fn($q) => $q->where(fn($sub) => $sub
                ->where('af.origin_flight_no', 'like', "%{$flightNo}%")
                ->orWhere('af.departure_flight_no', 'like', "%{$flightNo}%")
                ->orWhere('af.ferry_flight_no', 'like', "%{$flightNo}%")
            ))
            ->when($from && $to, fn($q) => $q->whereBetween('af.service_date', [$from, $to]))
            ->when($from && !$to, fn($q) => $q->whereDate('af.service_date', '>=', $from))
            ->when(!$from && $to, fn($q) => $q->whereDate('af.service_date', '<=', $to))
            ->whereNull('af.deleted_at');

        return DB::query()
            ->fromSub($baseQuery, 'flight_details')
            ->select(
                'flight_type_id',
                'flight_type',
                'flight_type_name',
                'airline',
                'route',
                'departure_flight_no',
                'ferry_direction',
                DB::raw('COUNT(*) as quantity')
            )
            ->groupBy(
                'flight_type_id',
                'flight_type',
                'flight_type_name',
                'airline',
                'route',
                'departure_flight_no',
                'ferry_direction'
            )
            ->orderBy('flight_type')
            ->orderBy('departure_flight_no')
            ->get()
            ->map(function ($row) {
                $row->description = 'FLIGHT ' . $row->departure_flight_no
                    . ' (' . ($row->ferry_direction ? $row->ferry_direction . ' ' : '')
                    . $row->flight_type_name . ')';

                return $row;
            });
    }

    private function getDelayChargeSummaryRows($branch, $airline, $type, $from, $to, $flightNo)
    {
        return DB::table('actual_flights as af')
            ->leftJoin('airline_routes as ar_dep', 'ar_dep.id', '=', 'af.departure_route_id')
            ->leftJoin('airlines as al_dep', 'al_dep.id', '=', 'ar_dep.airline_id')
            ->select(
                'al_dep.name as airline',
                'af.delay_charge as departure_flight_no',
                DB::raw("
                    CASE
                        WHEN af.delay_charge = af.origin_flight_no THEN 'Arrival'
                        WHEN af.delay_charge = af.departure_flight_no THEN 'Departure'
                    END as delay_charge_type
                "),
                DB::raw('COUNT(*) as quantity')
            )
            ->when($branch, fn($q) => $q->where('af.branch_id', $branch))
            ->when($type, fn($q) => $q->where('af.flight_type_id', $type))
            ->when($airline, fn($q) => $q->where('ar_dep.airline_id', $airline))
            ->when($flightNo, fn($q) => $q->where(fn($sub) => $sub
                ->where('af.origin_flight_no', 'like', "%{$flightNo}%")
                ->orWhere('af.departure_flight_no', 'like', "%{$flightNo}%")
                ->orWhere('af.ferry_flight_no', 'like', "%{$flightNo}%")
                ->orWhere('af.delay_charge', 'like', "%{$flightNo}%")
            ))
            ->when($from && $to, fn($q) => $q->whereBetween('af.service_date', [$from, $to]))
            ->when($from && !$to, fn($q) => $q->whereDate('af.service_date', '>=', $from))
            ->when(!$from && $to, fn($q) => $q->whereDate('af.service_date', '<=', $to))
            ->whereNotNull('af.delay_charge')
            ->where('af.delay_charge', '!=', '')
            ->whereNull('af.deleted_at')
            ->groupBy('al_dep.name', 'af.delay_charge', 'delay_charge_type')
            ->orderBy('af.delay_charge')
            ->orderBy('delay_charge_type')
            ->get()
            ->map(function ($row) {
                $row->route = '';
                $row->flight_type = '';
                $row->flight_type_name = 'Delay Charge';
                $row->ferry_direction = null;
                $row->description = 'FLIGHT ' . $row->departure_flight_no
                    . ' (' . $row->delay_charge_type . ' Delay Charge)';

                return $row;
            });
    }

    private function getFlights($branch, $airline, $type, $from, $to, $flightNo)
    {
        return Flight::query()
            ->with([
                'branch:id,name',
                'flightType:id,name,type_code',
                'originEquipment:id,registration',
                'departureEquipment:id,registration',
                'originAirlineRoute.airline:id,name',
                'originAirlineRoute.airportRoute.origin:id,iata',
                'originAirlineRoute.airportRoute.destination:id,iata',
                'departureAirlineRoute.airline:id,name',
                'departureAirlineRoute.airportRoute.origin:id,iata',
                'departureAirlineRoute.airportRoute.destination:id,iata',
            ])
            // Filter branch
            ->when($branch, fn($q) => $q->where('branch_id', $branch))

            // Filter flight type
            ->when($type, fn($q) => $q->where('flight_type_id', $type))

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
