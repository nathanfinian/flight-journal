<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
            GSE Equipment List
        </div>
        <div class="flex items-center">
            <x-ui.button
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('gseequipment.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>

    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('dashboard')">Dashboard</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>GSE Equipment List</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Code</th>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Type</th>
                <th class="px-4 py-3 text-left">Branch</th>
                <th class="px-4 py-3 text-left">Serial</th>
                <th class="px-4 py-3 text-left">Asset</th>
                <th class="px-4 py-3 text-left">Hours</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Transaksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            @forelse ($equipment as $row)
                <tr
                    class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                    wire:click="openEdit({{ $row->gse_equipment_id }})"
                >
                    <td class="px-4 py-3">
                        @php
                            $num = method_exists($equipment, 'firstItem') && $equipment->firstItem()
                                ? $equipment->firstItem() + $loop->index
                                : $loop->iteration;
                        @endphp
                        {{ $num }}
                    </td>
                    <td class="px-4 py-3 font-medium">{{ $row->equipment_code }}</td>
                    <td class="px-4 py-3">{{ $row->name }}</td>
                    <td class="px-4 py-3">{{ $row->gseType?->type_name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $row->branch?->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $row->serial_number ?: '-' }}</td>
                    <td class="px-4 py-3">{{ $row->asset_number ?: '-' }}</td>
                    <td class="px-4 py-3">{{ $row->total_hours_used !== null ? number_format((float) $row->total_hours_used, 2, ',', '.') : '-' }}</td>
                    <td class="px-4 py-3">{{ $row->status }}</td>
                    <td class="px-4 py-3">{{ $row->stock_movements_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-4 py-6 text-center text-gray-500">
                        No GSE equipment found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 dark:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700">
        {{ $equipment->links() }}
    </div>
</div>
