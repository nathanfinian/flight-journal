<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Summary Histori Penerbangan</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo-black.png') }}">

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 24px;
            color: #222;
        }

        h2, h4, p {
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 18px;
            text-align: center;
        }

        .meta {
            margin-top: 6px;
            color: #555;
            font-size: 11px;
        }

        .toolbar {
            margin-bottom: 14px;
            text-align: right;
        }

        .print-button {
            border: 1px solid #777;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            padding: 6px 10px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #999;
            padding: 7px 9px;
        }

        th {
            background: #e9ecef;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .total-row td {
            background: #f4f4f4;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar no-print">
        <button type="button" class="print-button" onclick="window.print()">Print</button>
    </div>

    <div class="header">
        <h2>Summary Histori Penerbangan {{ $airline }}{{ $type }}</h2>
        <h4>{{ $branch }}</h4>
        <p class="meta">{{ $dateFrom }} sampai {{ $dateTo }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 42px;">#</th>
                <th>Airline</th>
                <th>Description</th>
                <th>Route</th>
                <th>Flight Type</th>
                <th class="text-center" style="width: 90px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($summaryRows as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->airline ?: '-' }}</td>
                    <td>{{ $row->description }}</td>
                    <td>{{ $row->route ?: '-' }}</td>
                    <td>
                        {{ $row->flight_type_name ?: '-' }}
                        @if($row->flight_type)
                            ({{ $row->flight_type }})
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($row->quantity) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No flights found.</td>
                </tr>
            @endforelse

            <tr class="total-row">
                <td colspan="5" class="text-end">Grand Total</td>
                <td class="text-center">{{ number_format($totalFlights) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="5" class="text-end">Total Delay Charge</td>
                <td class="text-center">{{ number_format($totalDelayCharges) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
