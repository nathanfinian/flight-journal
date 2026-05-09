<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           GSE Rates List
        </div>
        <div class="flex items-center">
            <x-ui.button
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('rategse.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>
    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>GSE Rates List</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Service</th>
                <th class="px-4 py-3 text-left">Charge Type</th>
                <th class="px-4 py-3 text-left">Rate</th>
                <th class="px-4 py-3 text-left">Effective From</th>
                <th class="px-4 py-3 text-left">Effective To</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            @forelse ($gseRates as $gseRate)
                <tr
                    class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                    wire:click="openEdit({{ $gseRate->id }})"
                >
                    <td class="px-4 py-3">
                        @php
                            $num = method_exists($gseRates, 'firstItem') && $gseRates->firstItem()
                                ? $gseRates->firstItem() + $loop->index
                                : $loop->iteration;
                        @endphp
                        {{ $num }}
                    </td>
                    <td class="px-4 py-3">{{ $gseRate->gseType->type_name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $gseRate->charge_type }}</td>
                    <td class="px-4 py-3">Rp. {{ number_format((float) $gseRate->service_rate, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">{{ $gseRate->effective_from?->format('Y-m-d') ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $gseRate->effective_to?->format('Y-m-d') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                        No GSE rate found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
