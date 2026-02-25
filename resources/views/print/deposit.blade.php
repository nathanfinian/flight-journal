<!DOCTYPE html>
<html >
<head>

    <title>{{ $deposit->receipt_number }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo-black.png') }}">
    <style>
        @media print {
            .no-print { display: none; }
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
    <title>Kwitansi : {{ $deposit->receipt_number }}</title>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr class="pr-3">
            <td rowspan="5" align="center" valign="middle" style="padding-right: 12px;"><img class="company-logo" src="{{ asset('img/logo-kop.png') }}" alt="MCA Logo" height="60"></td>
            
        </tr>
        <tr>
            <td width="55%" class="fontksr14">&nbsp;PT. MULIO CITRA ANGKASA</td>
            <td align="left" class="fontksr11" width="15%" >&nbsp;</td>
            <td align="left" class="fontksr11" width="20%" >&nbsp;</td>
        </tr>
        <tr>
            <td width="55%" class="fontksr11">&nbsp;{{ $deposit->branch->address }}</td>
            <td align="right" class="fontksr12" width="15%" >Tgl. Invoice</td>
            <td align="left" class="fontksr12" width="35%" >&nbsp;:&nbsp;{{ $deposit->dateFormatted }}</td>
        </tr>
        <tr>
            <td width="55%" class="fontksr11">&nbsp;Telp. {{ $deposit->branch->phone_number }}</td>
            <td align="right" class="fontksr12" width="15%" >No. Invoice</td>
            <td align="left" class="fontksr12" width="35%" >&nbsp;:&nbsp;{{ $deposit->receipt_number }}</td>
        </tr>
        <tr>
            <td width="55%" class="fontksr11">&nbsp;E-mail : {{ $deposit->branch->email }}</td>
            <td align="right" class="fontksr12" width="15%" >&nbsp;</td>
            <td align="left" class="fontksr12" width="20%" >&nbsp;</td>
        </tr>
        <tr>
            <td width="100%" align="center" colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td width="100%" align="center" colspan="4">&nbsp;<b>DANA TALANGAN</b>&nbsp;</td>
        </tr>
        
        <tr class="fontksr14">
            <td width="100%" align="left" colspan="4">&nbsp;Telah Terima dari&nbsp;:&nbsp;{{ $deposit->received_from_company }}</td>
        </tr>
    </table>
    <table width="100%" border="1" cellpadding="0" cellspacing="0" >
        <tr class="fontksr12" height="25">
            <td width="5%" align="center"><b>No</b></td>
            <td width="75%" align="center"><b>Keterangan</b></td>
            <td width="20%" align="center"><b>Jumlah ( RP )</b></td>
        </tr>
        <tr class="fontksr12" height="23">
            <td width="5%" align="center">&nbsp;&nbsp;1&nbsp;&nbsp;</td>
            <td width="75%" align="left">&nbsp;{{ $deposit->description }}</td>
            <td width="20%" align="right">{{ number_format($deposit->value) }}</td>
        </tr>
        <tr class="fontksr12">
            <td align="right" colspan="2"><b>Total&nbsp;:&nbsp;</b></td>
            <td width="20%" align="right">{{ number_format($deposit->value) }}</td>
        </tr>
    </table>
    <table  width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="100%" colspan="3" valign="middle" align="center" height="20">&nbsp;</td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="100%" colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr class="fontksr12">
            <td width="80%" align="left" style="font-size: 15px;">Terbilang&nbsp;:&nbsp;#&nbsp;{{ $deposit->terbilang }}&nbsp;#</td>
            <td width="20%" align="center">Diterima Oleh,</td>
        </tr>
        <tr>
            <td width="80%" align="left" style="font-size: 15px;">&nbsp;<b>{{ $deposit->branch->account_number }}</b></td>
            <td width="20%" align="center">&nbsp</td>
        </tr>
        <tr>
            <td width="100%" colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
            <td width="100%" colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
            <td width="100%" colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr >
            <td width="80%" align="center" class="fontksr12">&nbsp;</td>
            <td width="20%" align="center" class="fontksr12">&nbsp;(&nbsp;{{ $deposit->signer_name }}&nbsp;)&nbsp;</td>
        </tr>
    </table>
</body>
</html>