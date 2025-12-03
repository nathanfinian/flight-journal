<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Flight</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo-black.png') }}">

    <style>
        /* Use simple print-friendly styling */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #222;
        }

        h2, h4 {
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #999;
        }

        td.text-center {
            text-align: center !important;
        }

        /* Override Tailwind paddings for correct print spacing */
        .px-4 { padding-left: 10px; padding-right: 10px; }
        .py-3 { padding-top: 6px; padding-bottom: 6px; }

        .font-semibold { font-weight: bold; }

        .shadow-lg { box-shadow: none !important; }

        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>

<body>

<div class="header">
    <h2>Report Histori Penerbangan {{ $airline }}</h2>
    <h4>{{ $branch }}</h4>
    <p style="font-size: 11px; color: #555;">{{ $dateFrom }} sampai {{ $dateTo }}</p>
</div>

<table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-3 text-left" rowspan="2">#</th>
            <th class="px-4 py-3 text-left" rowspan="2">Branch</th>
            <th class="px-4 py-3 text-left" rowspan="2">Tanggal</th>
            <th class="px-4 py-3 text-left" rowspan="2">Flight Number</th>
            <th class="px-4 py-3 text-left" rowspan="2">Registration</th>
            <th class="px-4 py-3 text-left" rowspan="2">Route</th>
            <th class="px-4 py-3 text-left">Landing</th>
            <th class="px-4 py-3 text-left">Departure</th>
            <th class="px-4 py-3 text-left" rowspan="2">PIC</th>
            <th class="px-4 py-3 text-left" rowspan="2">PAX</th>
            <th class="px-4 py-3 text-left" rowspan="2">Notes</th>
        </tr>
        <tr>
            <th class="px-4 py-3 text-left">WIB</th>
            <th class="px-4 py-3 text-left">WIB</th>
        </tr>
    </thead>

    <tbody>
        @forelse($flights as $index => $flight)
            <tr>
                <td class="px-4 py-3" rowspan="2">{{ $index + 1 }}</td>

                <td class="px-4 py-3" rowspan="2">
                    {{ $flight->branch->name ?? '—' }}
                </td>

                <td class="px-4 py-3 font-semibold" rowspan="2">
                    {{ $flight->service_date->locale('id')->translatedFormat('d M Y') }}
                </td>

                <!-- ORIGIN flight -->
                <td class="px-4 py-3 font-semibold">
                    {{ $flight->origin_flight_no ?? '—' }}
                </td>

                <td class="px-4 py-3 font-semibold">
                    {{ $flight->originEquipment->registration ?? '—' }}
                </td>

                <td class="px-4 py-3">
                    {{ $flight->originAirlineRoute->airportRoute->origin->iata ?? '---' }}
                    →
                    {{ $flight->originAirlineRoute->airportRoute->destination->iata ?? '---' }}
                </td>

                <td class="px-4 py-3 text-center">
                    {{ $flight->actual_arr ? substr($flight->actual_arr, 0, 5) : '' }}
                </td>

                <td class="px-4 py-3"></td>

                <td class="px-4 py-3" rowspan="2">{{ $flight->pic ?? '' }}</td>
                <td class="px-4 py-3" rowspan="2">{{ $flight->pax ?? '' }}</td>
                <td class="px-4 py-3" rowspan="2">{{ $flight->notes ?? '' }}</td>
            </tr>

            <!-- DEPARTURE flight -->
            <tr>
                <td class="px-4 py-3 font-semibold">
                    {{ $flight->departure_flight_no ?? '—' }}
                </td>

                <td class="px-4 py-3 font-semibold">
                    {{ $flight->departureEquipment->registration ?? '—' }}
                </td>

                <td class="px-4 py-3">
                    {{ $flight->departureAirlineRoute->airportRoute->origin->iata ?? '---' }}
                    →
                    {{ $flight->departureAirlineRoute->airportRoute->destination->iata ?? '---' }}
                </td>

                <td class="px-4 py-3"></td>

                <td class="px-4 py-3 text-center">
                    {{ $flight->actual_dep ? substr($flight->actual_dep, 0, 5) : '' }}
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="11" class="px-4 py-4 text-center text-gray-400">
                    No flights found.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
</body>
</html>
