<!DOCTYPE html>
<html >
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
        </style>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

		<link href="DataTables/datatables.min.css" rel="stylesheet">
		<script src="DataTables/datatables.min.js"></script>
        <link rel="stylesheet" href="{{ Vite::asset('resources/css/custom.css') }}">

	</head>
	<body>
		<!-- HEADER -->
		<div class="header-content">
			<div class="logo">
				<img class="company-logo" src="{{ asset('img/logo-kop.png') }}" alt="MCA Logo">
			</div>
			<div class="company-info">
                <div id="company-name" style="font-size: 18px; font-weight: bold;">PT. MULIO CITRA ANGKASA</div>
                <div id="company-branch" style="font-weight: bold;">{{ $invoice->branch->airport->iata }} - {{ $invoice->branch->name }}</div>
                <div id="company-department">GROUND HANDLING</div>
                <div id="company-address">{{ $invoice->branch->address }}</div>
                <div id="company-phones">
                    Hp. {{ $invoice->branch->phone_number }} &nbsp;</span>
                </div>
                <div id="company-email">E-mail : {{ $invoice->branch->email }}</span></div>
			</div>
		</div>
		<hr/>

		<!-- INVOICE TITLE AND NUMBER -->
		<div class="invoice-title">INVOICE</div>
		<div class="invoice-number">Nomor: {{ $invoice->invoice_number }}</div>

		<!-- RECIPIENT INFORMATION -->
		<div class="recipient">
			Kepada Yth,<br/>
			{{ $invoice->toCompany }} <br>
			Di -<br />
			{{ $invoice->branch->name }}</span>
		</div>

		<!-- BODY TEXT -->
		<div class="body-text">
			Penagihan biaya Ground Handling {{ $invoice->toCompany }} untuk Periode tanggal {{ $invoice->dateFromFormatted }} s/d 
			{{ $invoice->dateToFormatted }}, Sebagai Berikut:
		</div>

		<!-- ITEMS TABLE -->
		<table class="items-table">
			<thead>
				<tr>
					<th style="width: 5%;">No</th> 
					<th style="width: 50%;">Uraian</th> 
					<th style="width: 5%;">Qty</th> 
					<th style="width: 20%;">Harga Satuan (Rp)</th> 
					<th style="width: 20%;">Jumlah (Rp)</th>
				</tr>
			</thead>
			<tbody id="flight-items">
                {{-- Summary of flights loop, total of each flight number per date range--}}
			@foreach ( $flightDetails as $flightDetail )
			@php
				$lineTotal = $invoice->rate->ground_fee * $flightDetail->Quantity;
			@endphp

			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>FLIGHT {{ $flightDetail->departure_flight_no }}</td>
				<td>{{ $flightDetail->Quantity }}</td>
				<td>{{ number_format($invoice->rate->ground_fee) }}</td>
				<td>{{ number_format($lineTotal) }}</td>
			</tr>
			@endforeach
			
			<tr>
				<td colspan="2"><b>Total</b></td>
                {{-- Total quantity --}}
				<td>{{ $totalQty }}</td>
				<td>&nbsp;</td>
				<td><b>Rp. {{ number_format($totalPreTax) }}</b></td>
			</tr>
			<tr>
				<td colspan="2">PPN</td>
				<td colspan="2" class="text-center">11% X {{ number_format($totalPreTax) }}</td>
				<td>Rp. {{ number_format($totalPPN) }}</td>
			</tr>
			<tr>
				<td colspan="4"></td>
				<td>Rp. {{ number_format($totalAfterPPN) }}</td>
			</tr>
			<tr>
				<td colspan="2">PPH</td>
				<td colspan="2" class="text-center">11% X {{ number_format($totalPreTax) }}</td>
				<td>Rp. {{ number_format($totalPPH) }}</td>
			</tr>
			<tr>
				<td colspan="4"></td>
				<td>Rp. {{ number_format($totalAfterPPH) }}</td>
			</tr>
			<tr>
				<td colspan="2">Konsesi</td>
				<td colspan="2" class="text-center">11% X {{ number_format($totalPreTax) }}</td>
				<td>Rp. {{ number_format($totalKON) }}</td>
			</tr>
			<tr>
				<td colspan="4"><b>Grand Total</b></td>
				<td><b>Rp. {{ number_format($totalAfterKON) }}</b></td>
			</tr>
			</tbody>
		</table>

		<!-- AMOUNT IN WORDS -->
		<div class="amount-in-words">
			Terbilang: {{ $finalTerbilang }}
		</div>

		<!-- PAYMENT INSTRUCTIONS -->
		<div class="payment-instructions">
			Mohon pembayaran dapat ditransferkan ke Bank BNI<br>
			 <b>{{ $invoice->branch->account_number }}</b>
		</div>

		<!-- CLOSING PARAGRAPH -->
		<div class="closing">
			Demikian surat tagihan ini kami sampaikan, atas perhatian dan kerjasamanya diucapkan terima kasih.
		</div>

		<!-- SIGNATURES SECTION -->
		<div class="signatures mb-3 w-enter">
			<!-- Left signature block -->
			<div class="sign-box">
				<div id="left-company-name" style="font-weight: bold; margin-bottom: 4px;">
					{{ $invoice->toCompany }}
				</div>
				<div id="left-sign-date" style="margin-bottom: 60px;">&nbsp;</div>
				<div class="name" id="left-signer-name">
					<b><u>{{ $invoice->toWhom }}</u></b>
				</div>
				<div class="title" id="left-signer-title">
					<b>{{ $invoice->toTitle }}</b>
				</div>
			</div>

			<!-- Right signature block -->
			<div class="sign-box">
			<div id="invoice-location-date" style="margin-bottom: 4px; font-weight: bold;">
				Pontianak, {{ $currentDate }} <br>
				PT. MULIO CITRA ANGKASA
			</div>

			<div class="name" id="right-signer-name" style="margin-top: 40px;"><b><u>{{ $invoice->signer_name }}</u></b></div>
			<div class="title" id="right-signer-title"><b>Direktur</b></div>
			</div>
		</div>
		<div class="page-break"></div>

		<!-- HEADER -->
		<div class="header-content">
			<div class="logo">
				<img class="company-logo" src="{{ asset('img/logo-kop.png') }}" alt="MCA Logo">
			</div>
			<div class="company-info">
				<div id="company-department" class="fw-bold">Detail Perhitungan GROUND HANDLING</div>
				<div id="company-name">PT. MULIO CITRA ANGKASA</div>
				<div id="company-branch">Perusahaan: {{ $invoice->toCompany }}</div>
				<div>Periode: {{ $invoice->dateFromFormatted }} sampai {{ $invoice->dateToFormatted }}</div>
			</div>
		</div>
		<hr/>
		
		<!-- Detail perhitunga ground handling table -->
		<table class="items-table my-3">
			<thead class="text-center align-middle">
				<tr>
					<th rowspan="2" scope="col">Tanggal</th>
					<th rowspan="2" scope="col">No. Flt</th>
					<th rowspan="2" scope="col">Reg</th>
					<th rowspan="2" scope="col">Route</th>
					<th scope="col">Landing</th>
					<th scope="col">Berangkat</th>
					<th rowspan="2" scope="col">Nilai Harga</th>
				</tr>
				<tr>
					<th scope="col">WIB</th>
					<th scope="col">WIB</th>
				</tr>
			</thead>

			<tbody>
                {{-- Flight details, each flight details of certain date range --}}
				@php $totalFlightFee = 0; @endphp
				@forelse ($flightList as $flight)
					@php
						$lineTotal = (float) $invoice->rate->ground_fee;
						$totalFlightFee += $lineTotal;
					@endphp
					<tr>
						<td rowspan="2" class="text-center">{{ \Carbon\Carbon::parse($flight->service_date)->format('d/m/Y') }}</td>
						<td rowspan="2" class="text-center">{{ $flight->departure_flight_no }}</td>
						<td class="text-center">{{ $flight->registration_number ?? '-' }}</td>
						<td class="text-center">{{ $flight->arrival_route ?? '-' }}</td>
						<td class="text-center">{{ $flight->actual_arr ? substr($flight->actual_arr, 0, 5) : '-' }}</td>
						<td class="text-center">&nbsp;</td>
						<td rowspan="2" class="text-end">{{ number_format($lineTotal) }}</td>
					</tr>
					<tr>
						<td class="text-center">{{ $flight->registration_number ?? '-' }}</td>
						<td class="text-center">{{ $flight->departure_route ?? '-' }}</td>
						<td class="text-center">&nbsp;</td>
						<td class="text-center">{{ $flight->actual_dep ? substr($flight->actual_dep, 0, 5) : '-' }}</td>
					</tr>
				@empty
					<tr>
						<td colspan="7" class="text-center">Tidak ada data flight pada periode ini.</td>
					</tr>
				@endforelse
			</tbody>

			<tfoot>
				<tr class="fw-bold">
				<td colspan="6" class="text-end">TOTAL</td>
				<td class="text-end">{{ number_format($totalFlightFee) }}</td>
				</tr>
			</tfoot>
		</table>

		<div class="signatures mb-3">
			<!-- Left signature block -->
			<div class="sign-box">
				<div id="left-company-name" style="font-weight: bold; margin-bottom: 4px;">
					{{ $invoice->toCompany }}
				</div>
				<div id="left-sign-date" style="margin-bottom: 60px;">&nbsp;</div>
				<div class="name" id="left-signer-name">
					<b><u>{{ $invoice->toWhom }}</u></b>
				</div>
				<div class="title" id="left-signer-title">
					<b>{{ $invoice->toTitle }}</b>
				</div>
			</div>

			<!-- Right signature block -->
			<div class="sign-box">
			<div id="invoice-location-date" style="margin-bottom: 4px; font-weight: bold;">
				Pontianak, {{ $currentDate }} <br>
				PT. MULIO CITRA ANGKASA
			</div>

			<div class="name" id="right-signer-name" style="margin-top: 40px;"><b><u>{{ $invoice->signer_name }}</u></b></div>
			<div class="title" id="right-signer-title"><b>Direktur</b></div>
			</div>
		</div>
	</body>
</html>
