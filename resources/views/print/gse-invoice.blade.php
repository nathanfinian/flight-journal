<!DOCTYPE html>
<html>
	<head>
        <title>{{ $invoice->invoice_number }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('img/logo-black.png') }}">
        <style>
            @media print {
                .no-print { display: none; }
                .page-break {
                    break-before: page;
                    page-break-before: always;
                }
            }
            .text-center { text-align: center !important; }
            .text-end { text-align: right !important; }
            .align-middle { vertical-align: middle !important; }
            .nowrap { white-space: nowrap; }
            .gse-detail-table {
                font-size: 10px;
            }
            .gse-detail-table th,
            .gse-detail-table td {
                padding: 4px 5px;
            }
            .gse-service-group td {
                background-color: #f2f2f2;
                font-weight: bold;
                text-transform: uppercase;
            }
        </style>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

		<link href="DataTables/datatables.min.css" rel="stylesheet">
		<script src="DataTables/datatables.min.js"></script>
        <link rel="stylesheet" href="{{ Vite::asset('resources/css/custom.css') }}">
	</head>
	<body>
        @php
            $billingCompany = $invoice->toCompany ?: ($invoice->airline?->name ?? '-');
            $recipientName = $invoice->toWhom ?: '';
            $recipientTitle = $invoice->toTitle ?: '';
            $signerName = $invoice->signer_name ?: '';
        @endphp
		<div class="header-content">
			<div class="logo">
				<img class="company-logo" src="{{ asset('img/logo-kop.png') }}" alt="MCA Logo">
			</div>
			<div class="company-info">
                <div id="company-name" style="font-size: 18px; font-weight: bold;">PT. MULIO CITRA ANGKASA</div>
                <div id="company-branch" style="font-weight: bold;">
                    {{ $invoice->branch?->airport?->iata }} - {{ $invoice->branch?->name }}
                </div>
                <div id="company-department">GROUND HANDLING</div>
                <div id="company-address">{{ $invoice->branch?->address }}</div>
                <div id="company-phones">Hp. {{ $invoice->branch?->phone_number }} &nbsp;</div>
                <div id="company-email">E-mail : {{ $invoice->branch?->email }}</div>
			</div>
		</div>
		<hr/>

		<div class="invoice-title">INVOICE</div>
		<div class="invoice-number">Nomor: {{ $invoice->invoice_number }}</div>

		<div class="recipient">
			Kepada Yth,<br/>
			{{ $billingCompany }} <br>
			Di -<br />
			{{ $invoice->branch?->name ?? '-' }}
		</div>

		<div class="body-text">
			Penagihan biaya GSE {{ $billingCompany }} untuk periode tanggal
            {{ $invoice->dateFrom?->format('d F Y') ?? '-' }} s/d {{ $invoice->dateTo?->format('d F Y') ?? '-' }},
            sebagai berikut:
		</div>

		<table class="items-table">
			<thead>
				<tr>
					<th style="width: 5%;">No</th>
					<th style="width: 50%;">Uraian</th>
					<th style="width: 5%;" class="text-center">Qty</th>
					<th style="width: 20%;" class="text-center">Harga Satuan (Rp)</th>
					<th style="width: 20%;" class="text-center">Jumlah (Rp)</th>
				</tr>
			</thead>
			<tbody>
                @forelse ($summaryRows as $row)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $row['description'] }}</td>
                        <td class="text-center">{{ number_format($row['quantity']) }}</td>
                        <td class="text-end">{{ number_format($row['service_rate']) }}</td>
                        <td class="text-end">{{ number_format($row['amount']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data GSE pada invoice ini.</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="2"><b>Total</b></td>
                    <td class="text-center">{{ number_format($totalQty) }}</td>
                    <td>&nbsp;</td>
                    <td class="text-end"><b>Rp. {{ number_format($totalPreTax) }}</b></td>
                </tr>
                <tr>
                    <td colspan="4"><b>Grand Total</b></td>
                    <td class="text-end"><b>Rp. {{ number_format($totalPreTax) }}</b></td>
                </tr>
			</tbody>
		</table>

		<div class="amount-in-words">
			Terbilang: {{ $finalTerbilang }}
		</div>

		<div class="payment-instructions">
			Mohon pembayaran dapat ditransferkan ke Bank BNI<br>
			<b>{{ $invoice->branch?->account_number }}</b>
		</div>

		<div class="closing">
			Demikian surat tagihan ini kami sampaikan, atas perhatian dan kerjasamanya diucapkan terima kasih.
		</div>

		<div class="signatures mb-3 w-enter">
			<div class="sign-box">
				<div style="font-weight: bold; margin-bottom: 4px;">{{ $billingCompany }}</div>
				<div style="margin-bottom: 60px;">&nbsp;</div>
				<div class="name"><b><u>{{ $recipientName ?: str_repeat("\u{00a0}", 24) }}</u></b></div>
				<div class="title"><b>{{ $recipientTitle }}</b></div>
			</div>

			<div class="sign-box">
                <div style="margin-bottom: 4px; font-weight: bold;">
                    {{ $invoice->branch?->name ?? 'Pontianak' }}, {{ $currentDate }} <br>
                    PT. MULIO CITRA ANGKASA
                </div>
				<div class="name" style="margin-top: 60px;"><b><u>{{ $signerName ?: str_repeat("\u{00a0}", 24) }}</u></b></div>
				<div class="title"><b>Direktur</b></div>
			</div>
		</div>

		<div class="header-content">
			<div class="logo">
				<img class="company-logo" src="{{ asset('img/logo-kop.png') }}" alt="MCA Logo">
			</div>
			<div class="company-info">
				<div id="company-department" class="fw-bold">Detail Perhitungan GSE</div>
				<div id="company-name">PT. MULIO CITRA ANGKASA</div>
				<div id="company-branch">Perusahaan: {{ $billingCompany }}</div>
				<div>Periode: {{ $invoice->dateFrom?->format('d F Y') ?? '-' }} sampai {{ $invoice->dateTo?->format('d F Y') ?? '-' }}</div>
			</div>
		</div>
		<hr/>

		<table class="items-table my-3 gse-detail-table">
			<thead class="text-center align-middle">
				<tr>
					<th class="text-center">No</th>
					<th class="text-center">Tanggal</th>
					<th class="text-center">ER</th>
					<th class="text-center">No. Flt</th>
					<th class="text-center">Service</th>
					<th class="text-center">Rate</th>
					<th class="text-center">Qty</th>
					<th class="text-center">Jumlah</th>
				</tr>
			</thead>
			<tbody>
                @php $currentService = null; @endphp
				@forelse ($invoiceRecaps as $invoiceRecap)
                    @php
                        $recap = $invoiceRecap->recap;
                        $serviceName = $recap?->gseType?->type_name ?? '-';
                    @endphp
                    @if ($currentService !== $serviceName)
                        @php $currentService = $serviceName; @endphp
                        <tr class="gse-service-group">
                            <td colspan="8">{{ $serviceName }}</td>
                        </tr>
                    @endif
					<tr>
						<td class="text-center">{{ $loop->iteration }}</td>
						<td class="text-center nowrap">{{ $recap?->service_date?->format('d/m/Y') ?? '-' }}</td>
						<td class="text-center">{{ $recap?->er_number ?? '-' }}</td>
						<td class="text-center">{{ $recap?->flight_number ?? '-' }}</td>
						<td>{{ $serviceName }}</td>
						<td class="text-end nowrap">{{ number_format((float) $invoiceRecap->service_rate) }}</td>
						<td class="text-center">{{ number_format((float) $invoiceRecap->quantity) }}</td>
						<td class="text-end nowrap">{{ number_format((float) $invoiceRecap->amount) }}</td>
					</tr>
				@empty
					<tr>
						<td colspan="8" class="text-center">Tidak ada data GSE pada invoice ini.</td>
					</tr>
				@endforelse
			</tbody>
			<tfoot>
				<tr class="fw-bold">
                    <td colspan="6" class="text-end">TOTAL</td>
                    <td class="text-center">{{ number_format($totalQty) }}</td>
                    <td class="text-end">{{ number_format($totalPreTax) }}</td>
				</tr>
			</tfoot>
		</table>

		<div class="signatures mb-3">
			<div class="sign-box">
				<div style="font-weight: bold; margin-bottom: 4px;">{{ $billingCompany }}</div>
				<div style="margin-bottom: 60px;">&nbsp;</div>
				<div class="name"><b><u>{{ $recipientName ?: str_repeat("\u{00a0}", 24) }}</u></b></div>
				<div class="title"><b>{{ $recipientTitle }}</b></div>
			</div>

			<div class="sign-box">
                <div style="margin-bottom: 4px; font-weight: bold;">
                    {{ $invoice->branch?->name ?? 'Pontianak' }}, {{ $currentDate }} <br>
                    PT. MULIO CITRA ANGKASA
                </div>
				<div class="name" style="margin-top: 60px;"><b><u>{{ $signerName ?: str_repeat("\u{00a0}", 24) }}</u></b></div>
				<div class="title"><b>Direktur</b></div>
			</div>
		</div>
	</body>
</html>
