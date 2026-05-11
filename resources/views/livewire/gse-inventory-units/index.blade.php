<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
            Unit Types
        </div>
        <div class="flex items-center">
            <x-ui.button
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('gseunits.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>

    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('dashboard')">Dashboard</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Unit Types</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Symbol</th>
                <th class="px-4 py-3 text-left">Items</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            @forelse ($units as $unit)
                <tr
                    class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                    wire:click="openEdit({{ $unit->unit_id }})"
                >
                    <td class="px-4 py-3">
                        @php
                            $num = method_exists($units, 'firstItem') && $units->firstItem()
                                ? $units->firstItem() + $loop->index
                                : $loop->iteration;
                        @endphp
                        {{ $num }}
                    </td>
                    <td class="px-4 py-3 font-medium">{{ $unit->unit_name }}</td>
                    <td class="px-4 py-3">{{ $unit->unit_symbol }}</td>
                    <td class="px-4 py-3">{{ $unit->items_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                        No unit type found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 dark:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700">
        {{ $units->links() }}
    </div>
</div>
