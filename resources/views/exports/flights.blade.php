<table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
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

                    <td class="px-4 py-3" rowspan="2">{{ $flight->branch->name ?? '—' }}</td>

                    <td class="px-4 py-3 font-semibold" rowspan="2">{{ $flight->service_date->format('Y-m-d') }}</td>

                    <td class="px-4 py-3 font-semibold">{{ $flight->origin_flight_no ?? '—' }}</td>

                    <td class="px-4 py-3 font-semibold">{{ $flight->originEquipment->registration ?? '—' }}</td>
                    
                    {{-- Origin route and Departure Route--}}
                    <td class="px-4 py-3">
                        {{ $flight->originAirlineRoute->airportRoute->origin->iata ?? '---' }}
                        →
                        {{ $flight->originAirlineRoute->airportRoute->destination->iata ?? '---' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ substr($flight->actual_arr, 0, 5) }}
                    </td>
                    <td class="px-4 py-3"></td>

                    <td class="px-4 py-3" rowspan="2">{{ $flight->pic }}</td>
                    <td class="px-4 py-3" rowspan="2">{{ $flight->pax}}</td>
                    <td class="px-4 py-3" rowspan="2">{{ $flight->notes}}</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 font-semibold">{{ $flight->departure_flight_no ?? '—' }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $flight->departureEquipment->registration ?? '—' }}</td>

                    <td class="px-4 py-3">
                        {{ $flight->departureAirlineRoute->airportRoute->origin->iata ?? '---' }}
                        →
                        {{ $flight->departureAirlineRoute->airportRoute->destination->iata ?? '---' }}
                    </td>

                    <td class="px-4 py-3"></td>
                    <td class="px-4 py-3">{{ substr($flight->actual_dep, 0, 5) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-4 text-center text-gray-400 dark:text-neutral-500">
                        No flights found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>