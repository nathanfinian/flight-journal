<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           Airport List
        </div>
        <div class="flex items-center">
            <x-ui.button 
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('settings.airport.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>
     <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Airport List</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
            <th class="px-4 py-3 text-left">#</th>
            <th class="px-4 py-3 text-left">City</th>
            <th class="px-4 py-3 text-left">ICAO</th>
            <th class="px-4 py-3 text-left">IATA</th>
            <th class="px-4 py-3 text-left">Country</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            @forelse ($airports as $airport)
            <tr 
                class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                wire:click="openEdit({{ $airport->id }})"
                >
                <td class="px-4 py-3">
                @php
                    $num = method_exists($airport, 'firstItem') && $airport->firstItem()
                        ? $airport->firstItem() + $loop->index   // paginated
                        : $loop->iteration;                        // non-paginated
                @endphp
                {{ $num }}
                </td>
                <td class="px-4 py-3">{{ $airport->city }}</td>
                <td class="px-4 py-3">
                    <x-ui.text :class="blank($airport->icao) ? 'opacity-50' : ''">
                        {{ filled($airport->icao) ? $airport->icao : 'Empty' }}
                    </x-ui.text>
                </td>
                <td class="px-4 py-3">
                    <x-ui.text :class="blank($airport->iata) ? 'opacity-50' : ''">
                        {{ filled($airport->iata) ? $airport->iata : 'Empty' }}
                    </x-ui.text>
                </td>
                <td class="px-4 py-3">{{ $airport->country }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                No airport found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

