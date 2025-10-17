<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           Aircraft List
        </div>
        <div class="flex items-center">
            <x-ui.button 
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('settings.aircraft.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>
     <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Aircraft List</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
            <th class="px-4 py-3 text-left">#</th>
            <th class="px-4 py-3 text-left">Type</th>
            <th class="px-4 py-3 text-left">ICAO</th>
            <th class="px-4 py-3 text-left">IATA</th>
            <th class="px-4 py-3 text-left">Seats</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            @forelse ($aircraft as $aircraft)
            <tr 
                class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                wire:click="openEdit({{ $aircraft->id }})"
                >
                <td class="px-4 py-3">
                {{-- Works for both paginate() and get() --}}
                @php
                    $num = method_exists($aircraft, 'firstItem') && $aircraft->firstItem()
                        ? $aircraft->firstItem() + $loop->index   // paginated
                        : $loop->iteration;                        // non-paginated
                @endphp
                {{ $num }}
                </td>
                <td class="px-4 py-3">{{ $aircraft->type_name }}</td>
                <td class="px-4 py-3">{{ $aircraft->icao_code }}</td>
                <td class="px-4 py-3">{{ $aircraft->iata_code }}</td>
                <td class="px-4 py-3">{{ $aircraft->seat_capacity }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                No aircraft found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>