<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Flight Number</th>
            <th>Reg Number</th>
            <th>Route</th>
            <th>Landing Time (WIB)</th>
            <th>Departure Time (WIB)</th>
            <th>Tagihan (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($days as $day)
            @forelse ($flightsByDate->get($day->toDateString(), []) as $flight)
            <tr>
                <td rowspan="2" data-type="s" style="vertical-align: middle;">{{ $flight->service_date->format('d-M-y') }}</td>
                <td rowspan="2" data-type="s" style="vertical-align: middle;">{{ $flight->departure_flight_no }}</td>
                <td data-type="s">{{ $flight->originEquipment?->registration }}</td>
                <td data-type="s">{{ $flight->originAirlineRoute?->airportRoute?->origin?->iata ?? '' }} - {{ $flight->originAirlineRoute?->airportRoute?->destination?->iata ?? '' }}</td>
                <td data-type="s">{{ substr($flight->actual_arr ?? '', 0, 5) }}</td>
                <td></td>
                @if ($charges[$flight->id] !== null)
                    <td rowspan="2" data-type="n" data-format="#,##0.00" style="vertical-align: middle;">{{ $charges[$flight->id] }}</td>
                @else
                    <td rowspan="2" data-type="s" style="vertical-align: middle;">Rate tidak ditemukan</td>
                @endif
            </tr>
            <tr>
                <td data-type="s">{{ $flight->departureEquipment?->registration }}</td>
                <td data-type="s">{{ $flight->departureAirlineRoute?->airportRoute?->origin?->iata ?? '' }} - {{ $flight->departureAirlineRoute?->airportRoute?->destination?->iata ?? '' }}</td>
                <td></td>
                <td data-type="s">{{ substr($flight->actual_dep ?? '', 0, 5) }}</td>
            </tr>
            @empty
                <tr>
                    <td rowspan="2" data-type="s" style="vertical-align: middle;">{{ $day->format('d-M-y') }}</td>
                    <td rowspan="2"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td rowspan="2"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforelse
        @endforeach
        <tr>
            <td colspan="6" style="font-weight: bold; text-align: right;">Subtotal</td>
            <td data-type="n" data-format="#,##0.00" style="font-weight: bold;">{{ round(array_sum($charges), 2) }}</td>
        </tr>
    </tbody>
</table>
