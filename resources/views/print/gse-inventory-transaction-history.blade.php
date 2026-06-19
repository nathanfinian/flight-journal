<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GSE Inventory Transaction History</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo-black.png') }}">

    <style>
        body {
            color: #222;
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        h2, p {
            margin: 0;
            padding: 0;
        }

        .toolbar {
            margin-bottom: 14px;
            text-align: right;
        }

        .print-button {
            background: #fff;
            border: 1px solid #777;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            padding: 6px 10px;
        }

        .header {
            margin-bottom: 14px;
            text-align: center;
        }

        .meta {
            color: #555;
            font-size: 11px;
            margin-top: 4px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #999;
            padding: 6px 7px;
            vertical-align: top;
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

        .nowrap {
            white-space: nowrap;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <button type="button" class="print-button" onclick="window.print()">Print</button>
    </div>

    <div class="header">
        <h2>GSE Inventory Transaction History</h2>
        <p class="meta">Branch: {{ $branchName ?: 'All branches' }}</p>
        <p class="meta">Sub Category: {{ $subCategoryLabel ?: 'All sub categories' }}</p>
        <p class="meta">Item: {{ $itemLabel ?: 'All items' }}</p>
        <p class="meta">Date From: {{ $dateFrom ?: '-' }} | Date To: {{ $dateTo ?: '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 36px;">#</th>
                <th class="nowrap">Date</th>
                <th>Branch</th>
                <th>Category</th>
                <th>Sub Category</th>
                <th>Item</th>
                <th class="text-center">Type</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Balance</th>
                <th>Unit</th>
                <th>Equipment</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movements as $movement)
                @php
                    $item = $movement->item;
                    $unitLabel = $item?->unit?->unit_symbol ?: $item?->unit?->unit_name;
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="nowrap">{{ $movement->movement_date?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>{{ $movement->branch?->name ?? '-' }}</td>
                    <td>{{ $item?->subCategory?->category?->category_name ?? '-' }}</td>
                    <td>{{ $item?->subCategory?->sub_category_name ?? '-' }}</td>
                    <td>{{ $item?->name ?? '-' }}</td>
                    <td class="text-center">{{ $movement->movement_type }}</td>
                    <td class="text-end">{{ number_format((int) $movement->quantity) }}</td>
                    <td class="text-end">{{ number_format((int) $movement->balance) }}</td>
                    <td>{{ $unitLabel ?: '-' }}</td>
                    <td>{{ $movement->gseEquipment ? $movement->gseEquipment->equipment_code . ' - ' . $movement->gseEquipment->name : '-' }}</td>
                    <td>{{ $movement->notes ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">No transaction history found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
