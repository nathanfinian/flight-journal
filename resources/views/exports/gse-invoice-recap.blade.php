<table>
    <thead>
        <tr>
            <th colspan="11">GSE Invoice Recap</th>
        </tr>
        <tr>
            <th>Invoice Number</th>
            <td colspan="10">{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <th>Branch</th>
            <td colspan="10">{{ $invoice->branch?->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Airline</th>
            <td colspan="10">{{ $invoice->airline?->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Period</th>
            <td colspan="10">
                {{ $invoice->dateFrom?->format('Y-m-d') ?? '-' }} sampai {{ $invoice->dateTo?->format('Y-m-d') ?? '-' }}
            </td>
        </tr>
        <tr>
            <th>#</th>
            <th>Service Date</th>
            <th>ER Number</th>
            <th>Flight Number</th>
            <th>Airline</th>
            <th>Service</th>
            <th>Equipment</th>
            <th>Charge Type</th>
            <th>Rate</th>
            <th>Qty</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoice->recaps as $recap)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $recap->service_date?->format('Y-m-d') ?? '-' }}</td>
                <td>{{ $recap->er_number }}</td>
                <td>{{ $recap->flight_number }}</td>
                <td>{{ $recap->airline?->name ?? '-' }}</td>
                <td>{{ $recap->gseType?->service_name ?? '-' }}</td>
                <td>{{ $recap->equipment?->registration ?? '-' }}</td>
                <td>{{ $recap->pivot->charge_type ?: '-' }}</td>
                <td>{{ (float) $recap->pivot->service_rate }}</td>
                <td>{{ (float) $recap->pivot->quantity }}</td>
                <td>{{ (float) $recap->pivot->amount }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="10">Total</td>
            <td>{{ (float) $invoice->recaps->sum(fn ($recap) => $recap->pivot->amount) }}</td>
        </tr>
    </tbody>
</table>
