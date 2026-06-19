<table>
    <thead>
        <tr>
            <th colspan="12">GSE Inventory Transaction History</th>
        </tr>
        <tr>
            <th>Branch</th>
            <td colspan="11">{{ $branchName ?: 'All branches' }}</td>
        </tr>
        <tr>
            <th>Sub Category</th>
            <td colspan="11">{{ $subCategoryLabel ?: 'All sub categories' }}</td>
        </tr>
        <tr>
            <th>Item</th>
            <td colspan="11">{{ $itemLabel ?: 'All items' }}</td>
        </tr>
        <tr>
            <th>Date From</th>
            <td colspan="11">{{ $dateFrom ?: '-' }}</td>
        </tr>
        <tr>
            <th>Date To</th>
            <td colspan="11">{{ $dateTo ?: '-' }}</td>
        </tr>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Branch</th>
            <th>Category</th>
            <th>Sub Category</th>
            <th>Item</th>
            <th>Type</th>
            <th>Qty</th>
            <th>Balance</th>
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
                <td>{{ $loop->iteration }}</td>
                <td>{{ $movement->movement_date?->format('Y-m-d H:i') ?? '-' }}</td>
                <td>{{ $movement->branch?->name ?? '-' }}</td>
                <td>{{ $item?->subCategory?->category?->category_name ?? '-' }}</td>
                <td>{{ $item?->subCategory?->sub_category_name ?? '-' }}</td>
                <td>{{ $item?->name ?? '-' }}</td>
                <td>{{ $movement->movement_type }}</td>
                <td>{{ (int) $movement->quantity }}</td>
                <td>{{ (int) $movement->balance }}</td>
                <td>{{ $unitLabel ?: '-' }}</td>
                <td>{{ $movement->gseEquipment ? $movement->gseEquipment->equipment_code . ' - ' . $movement->gseEquipment->name : '-' }}</td>
                <td>{{ $movement->notes ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12">No transaction history found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
