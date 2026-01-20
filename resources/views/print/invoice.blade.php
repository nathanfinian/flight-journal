<!DOCTYPE html>
<html>
<head>
    <title>{{ $invoice->invoice_number }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo-black.png') }}">
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

<h1>Invoice {{ $invoice->invoice_number }}</h1>

<p>Airline: {{ $invoice->airline->name }}</p>
<p>Branch: {{ $invoice->branch->name }}</p>

</body>
</html>
