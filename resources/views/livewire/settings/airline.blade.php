<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           Airline List
        </div>
        <div class="flex items-center">
            <x-ui.button 
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('settings.airline.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>
     <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Airline List</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
            <th class="px-4 py-3 text-left">#</th>
            <th class="px-4 py-3 text-left">Name</th>
            <th class="px-4 py-3 text-left">ICAO</th>
            <th class="px-4 py-3 text-left">IATA</th>
            <th class="px-4 py-3 text-left">Callsign</th>
            <th class="px-4 py-3 text-left">Country</th>
            <th class="px-4 py-3 text-left">Status</th>

            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            @forelse ($airline as $airline)
            <tr 
                class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                wire:click="openEdit({{ $airline->id }})"
                >
                <td class="px-4 py-3">
                {{-- Works for both paginate() and get() --}}
                @php
                    $num = method_exists($airline, 'firstItem') && $airline->firstItem()
                        ? $airline->firstItem() + $loop->index   // paginated
                        : $loop->iteration;                        // non-paginated
                @endphp
                {{ $num }}
                </td>
                <td class="px-4 py-3">{{ $airline->name }}</td>
                <td class="px-4 py-3">
                    <x-ui.text :class="blank($airline->icao_code) ? 'opacity-50' : ''">
                        {{ filled($airline->icao_code) ? $airline->icao_code : 'Empty' }}
                    </x-ui.text>
                </td>
                <td class="px-4 py-3">
                    <x-ui.text :class="blank($airline->iata_code) ? 'opacity-50' : ''">
                        {{ filled($airline->iata_code) ? $airline->iata_code : 'Empty' }}
                    </x-ui.text>
                </td>
                <td class="px-4 py-3">
                    <x-ui.text :class="blank($airline->callsign) ? 'opacity-50' : ''">
                        {{ filled($airline->callsign) ? $airline->callsign : 'Empty' }}
                    </x-ui.text>
                </td>
                <td class="px-4 py-3">{{ $airline->country }}</td>
                <td class="px-4 py-3">{{ $airline->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                No airline found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

