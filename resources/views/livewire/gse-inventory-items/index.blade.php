<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
            Inventory Items
        </div>
        <div class="flex items-center">
            <x-ui.button
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('gseitems.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>

    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('dashboard')">Dashboard</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Inventory Items</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Code</th>
                <th class="px-4 py-3 text-left">Category</th>
                <th class="px-4 py-3 text-left">Sub Category</th>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Unit</th>
                <th class="px-4 py-3 text-left">Stock By Branch</th>
                <th class="px-4 py-3 text-left">Total</th>
                <th class="px-4 py-3 text-left">Status</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            @forelse ($items as $item)
                <tr
                    class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                    wire:click="openEdit({{ $item->item_id }})"
                >
                    <td class="px-4 py-3">
                        @php
                            $num = method_exists($items, 'firstItem') && $items->firstItem()
                                ? $items->firstItem() + $loop->index
                                : $loop->iteration;
                        @endphp
                        {{ $num }}
                    </td>
                    <td class="px-4 py-3 font-medium">{{ $item->code }}</td>
                    <td class="px-4 py-3">{{ $item->subCategory?->category?->category_name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $item->subCategory?->sub_category_name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $item->name }}</td>
                    <td class="px-4 py-3">{{ $item->unit?->unit_symbol ?? $item->unit?->unit_name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1.5">
                            @forelse ($item->stocks as $stock)
                                <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                                    {{ $stock->branch?->name ?? '-' }}: {{ number_format((int) $stock->quantity) }}
                                </span>
                            @empty
                                <span class="text-gray-500">-</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ number_format((int) ($item->stocks_sum_quantity ?? 0)) }}</td>
                    <td class="px-4 py-3">{{ $item->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                        No item found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 dark:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700">
        {{ $items->links() }}
    </div>
</div>
