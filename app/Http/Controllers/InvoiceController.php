<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Traits\Terbilang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    use Terbilang;

    public function print(string $invoice)
    {
        $invoice = Invoice::where('invoice_number', $invoice)
            ->with([
                'rate',
                'airline',
                'branch',
            ])
            ->firstOrFail();

        $delayChargeDetails = DB::table('actual_flights as af')
            ->leftJoin('airline_routes as ar_dep', 'ar_dep.id', '=', 'af.departure_route_id')
            ->select(
                'af.delay_charge as delay_charge_flight_no',
                DB::raw("
                    CASE
                        WHEN af.delay_charge = af.origin_flight_no THEN 'Arrival'
                        WHEN af.delay_charge = af.departure_flight_no THEN 'Departure'
                    END as delay_charge_type
                "),
                DB::raw('COUNT(*) as quantity')
            )
            ->whereBetween('af.service_date', [
                $invoice->dateFrom,
                $invoice->dateTo
            ])
            ->where('af.branch_id', $invoice->branch_id)
            ->where('ar_dep.airline_id', $invoice->airline_id)
            ->whereNotNull('af.delay_charge')
            ->where('af.delay_charge', '!=', '')
            ->whereNull('af.deleted_at')
            ->groupBy('af.delay_charge', 'delay_charge_type')
            ->orderBy('af.delay_charge')
            ->orderBy('delay_charge_type')
            ->get();

        $flightDetails = DB::table('actual_flights as af')
            ->leftJoin('airline_routes as ar_dep', 'ar_dep.id', '=', 'af.departure_route_id')
            ->leftJoin('flight_types as ft', 'ft.id', '=', 'af.flight_type_id')
            ->leftJoin('airline_rate_flight_type as arft', function ($join) use ($invoice) {
                $join->on('arft.flight_type_id', '=', 'af.flight_type_id')
                    ->where('arft.airline_rate_id', '=', $invoice->airline_rates_id);
            })
            ->select(
                'af.flight_type_id',
                'ft.type_code as flight_type',
                'ft.name as flight_type_name',
                'af.departure_flight_no',
                DB::raw('COALESCE(arft.percentage, 100) as rate_percentage'),
                DB::raw('COUNT(*) as quantity')
            )
            ->whereBetween('af.service_date', [
                $invoice->dateFrom,
                $invoice->dateTo
            ])
            ->where('af.branch_id', $invoice->branch_id)
            ->where('ar_dep.airline_id', $invoice->airline_id)
            ->whereNull('af.deleted_at')
            ->groupBy(
                'af.flight_type_id',
                'ft.type_code',
                'ft.name',
                'af.departure_flight_no',
                'arft.percentage'
            )
            ->orderBy('ft.type_code')
            ->orderBy('af.departure_flight_no')
            ->get();

        $flightList = DB::table('actual_flights as af')
            ->leftJoin('equipments as eq_dep', 'eq_dep.id', '=', 'af.departure_equipment_id')
            ->leftJoin('equipments as eq_org', 'eq_org.id', '=', 'af.origin_equipment_id')
            ->leftJoin('airline_routes as ar_org', 'ar_org.id', '=', 'af.origin_route_id')
            ->leftJoin('airport_routes as apr_org', 'apr_org.id', '=', 'ar_org.airport_route_id')
            ->leftJoin('airports as ap_org_from', 'ap_org_from.id', '=', 'apr_org.origin_id')
            ->leftJoin('airports as ap_org_to', 'ap_org_to.id', '=', 'apr_org.destination_id')
            ->leftJoin('airline_routes as ar_dep', 'ar_dep.id', '=', 'af.departure_route_id')
            ->leftJoin('airport_routes as apr_dep', 'apr_dep.id', '=', 'ar_dep.airport_route_id')
            ->leftJoin('airports as ap_dep_from', 'ap_dep_from.id', '=', 'apr_dep.origin_id')
            ->leftJoin('airports as ap_dep_to', 'ap_dep_to.id', '=', 'apr_dep.destination_id')
            ->leftJoin('flight_types as ft', 'ft.id', '=', 'af.flight_type_id')
            ->leftJoin('airline_rate_flight_type as arft', function ($join) use ($invoice) {
                $join->on('arft.flight_type_id', '=', 'af.flight_type_id')
                    ->where('arft.airline_rate_id', '=', $invoice->airline_rates_id);
            })
            ->select(
                'af.service_date',
                'af.flight_type_id',
                'af.departure_flight_no',
                'af.actual_arr',
                'af.actual_dep',
                'ft.name as flight_type_name',
                'ft.type_code as flight_type',
                DB::raw('COALESCE(arft.percentage, 100) as rate_percentage'),
                DB::raw('COALESCE(eq_dep.registration, eq_org.registration) as registration_number'),
                DB::raw("CONCAT(ap_org_from.iata, '-', ap_org_to.iata) as arrival_route"),
                DB::raw("CONCAT(ap_dep_from.iata, '-', ap_dep_to.iata) as departure_route")
            )
            ->whereBetween('af.service_date', [
                $invoice->dateFrom,
                $invoice->dateTo
            ])
            ->where('af.branch_id', $invoice->branch_id)
            ->where('ar_dep.airline_id', $invoice->airline_id)
            ->whereNull('af.deleted_at')
            ->orderBy('af.service_date')
            ->orderBy('af.departure_flight_no')
            ->get();

        $totalQty = 0;
        $totalPreTax = 0;
        $delayChargeUnitPrice = (float) ($invoice->rate->delay_rate ?? 0);

        foreach ($delayChargeDetails as $delayChargeDetail) {
            $delayChargeTotal = $delayChargeDetail->quantity * $delayChargeUnitPrice;
            $totalQty += $delayChargeDetail->quantity;
            $totalPreTax += $delayChargeTotal;
        }

        foreach ($flightDetails as $detail) {
            $unitPrice = $invoice->rate->ground_fee * ((float) $detail->rate_percentage / 100);
            $lineTotal = $unitPrice * $detail->quantity;
            $totalQty += $detail->quantity;
            $totalPreTax += $lineTotal;
        }

        $totalPPN = $totalPreTax * $invoice->rate->ppn_rate;
        $totalPPH = $totalPreTax * $invoice->rate->pph_rate;
        $totalKON = $totalPreTax * $invoice->rate->konsesi_rate;

        $formattedPPN = number_format($invoice->rate->ppn_rate * 100);
        $formattedPPH = number_format($invoice->rate->pph_rate * 100);
        $formattedKON = number_format($invoice->rate->konsesi_rate * 100);

        $totalAfterPPN = $totalPreTax + $totalPPN;
        $totalAfterKON = $totalAfterPPN + $totalKON;
        $totalAfterPPH = $totalAfterKON - $totalPPH;

        $finalTerbilang = $this->Terbilang($totalAfterKON);
        $currentDate = Carbon::now()->format('d F Y');

        return view('print.invoice', [
            'invoice' => $invoice,
            'delayChargeDetails' => $delayChargeDetails,
            'delayChargeUnitPrice' => $delayChargeUnitPrice,
            'flightDetails' => $flightDetails,
            'flightList' => $flightList,

            // RAW values
            'totalQty' => $totalQty,
            'totalPreTax' => $totalPreTax,
            'totalPPN' => $totalPPN,
            'totalPPH' => $totalPPH,
            'totalKON' => $totalKON,
            'totalAfterKON' => $totalAfterKON,
            'totalAfterPPH' => $totalAfterPPH,
            'totalAfterPPN' => $totalAfterPPN,

            'formattedPPN' => $formattedPPN,
            'formattedPPH' => $formattedPPH,
            'formattedKON' => $formattedKON,

            'finalTerbilang' => $finalTerbilang,
            'currentDate' => $currentDate,
        ]);
    }
}
