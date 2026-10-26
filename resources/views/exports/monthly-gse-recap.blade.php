<table>
    <thead>
        <tr>
            <th rowspan="2" style="text-align: center; vertical-align: middle;">Tanggal</th>
            <th rowspan="2" style="text-align: center; vertical-align: middle;">ER No</th>
            <th rowspan="2" style="text-align: center; vertical-align: middle;">Flight No</th>
            <th rowspan="2" style="text-align: center; vertical-align: middle;">Aircraft Type</th>
            <th rowspan="2" style="text-align: center; vertical-align: middle;">Reg</th>
            <th rowspan="2" style="text-align: center; vertical-align: middle;"></th>
            <th rowspan="2" style="text-align: center; vertical-align: middle;">Operator</th>
            <th colspan="3" style="text-align: center; vertical-align: middle;">Durasi</th>
        </tr>
        <tr>
            <th style="text-align: center; vertical-align: middle;">Start Time</th>
            <th style="text-align: center; vertical-align: middle;">Finish Time</th>
            <th style="text-align: center; vertical-align: middle;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($days as $day)
            @forelse ($rowsByDate->get($day->toDateString(), []) as $row)
                <tr>
                    <td data-type="s">{{ $day->format('d-M-y') }}</td>
                    <td data-type="s">{{ $row['recap']->er_number }}</td>
                    <td data-type="s">{{ $row['recap']->flight_number }}</td>
                    <td data-type="s">{{ $row['recap']->equipment?->aircraft?->type_name }}</td>
                    <td data-type="s">{{ $row['recap']->equipment?->registration }}</td>
                    <td></td>
                    <td data-type="s">{{ $row['recap']->operator_name }}</td>
                    <td data-type="s">{{ $row['start'] }}</td>
                    <td data-type="s">{{ $row['finish'] }}</td>
                    @if ($row['duration'] !== null)
                        <td data-type="n" data-format="[hh]:mm">{{ $row['duration'] / 86400 }}</td>
                    @else
                        <td></td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td data-type="s">{{ $day->format('d-M-y') }}</td>
                    @for ($column = 0; $column < 9; $column++)
                        <td></td>
                    @endfor
                </tr>
            @endforelse
        @endforeach
        <tr>
            <td colspan="9" style="font-weight: bold; text-align: right;">Total Durasi</td>
            <td data-type="n" data-format="[hh]:mm" style="font-weight: bold;">{{ $totalDuration / 86400 }}</td>
        </tr>
    </tbody>
</table>
