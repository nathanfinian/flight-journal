<?php

namespace App\Http\Controllers\Export;

use Illuminate\Http\Request;
use App\Exports\FlightsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class FlightExportController extends Controller
{
    private ?string $dateFrom = '';
    private ?string $dateTo = '';
    private ?string $branch = '';
    private ?string $airline = '';

    public function export(Request $request)
    {
        $this->dateFrom = $request->dateFrom;
        $this->dateTo = $request->dateTo;
        $this->branch = $request->branchName;
        $this->airline = $request->airlineName;
        
        return Excel::download(
            new FlightsExport(
                $request->dateFrom,
                $request->dateTo,
                $request->branch,
                $request->airline
            ),
            $this->branch . ' ' . $this->dateFrom . ' sampai ' . $this->dateTo . ' ' . $this->airline . ' Flights.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $this->dateFrom = $request->dateFrom;
        $this->dateTo = $request->dateTo;
        $this->branch = $request->branchName;
        $this->airline = $request->airlineName;

        return Excel::download(
            new FlightsExport(
                $request->dateFrom,
                $request->dateTo,
                $request->branch,
                $request->airline
            ),
            $this->branch . ' ' . $this->dateFrom . ' sampai ' . $this->dateTo . ' ' . $this->airline . ' Flights.pdf',
             \Maatwebsite\Excel\Excel::MPDF);
    }
}
