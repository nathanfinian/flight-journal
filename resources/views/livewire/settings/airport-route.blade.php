<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           Airport Route List
        </div>
        <div class="flex items-center">
            <x-ui.button 
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('settings.airport-route.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>
    
    <div class="flex justify-between gap-3">
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Airport Route List</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>
        <div class="flex items-center gap-3">
            <x-ui.label>
                Airport
            </x-ui.label>
            <x-ui.select
                placeholder="Semua Airport"
                wire:model.live="selectedAirport"
                class="mt-1 block w-72 sm:text-sm"
                searchable
            >
                <x-ui.select.option value="">Semua Airport</x-ui.select.option>
                @foreach($airports as $airport)
                    <x-ui.select.option value="{{ $airport->id }}">{{ $airport->city }} - {{ $airport->iata }}</x-ui.select.option>
                @endforeach
            </x-ui.select>
        </div>
    </div>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
            <th class="px-4 py-3 text-left">#</th>
            <th class="px-4 py-3 text-left">Route (IATA)</th>
            <th class="px-4 py-3 text-left">Airlines</th>
            <th class="px-4 py-3 text-left">Status</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            @forelse ($routes as $route)
            <tr 
                class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                wire:click="openEdit({{ $route->id }})"
                >
                <td class="px-4 py-3">
                @php
                    $num = method_exists($route, 'firstItem') && $route->firstItem()
                        ? $route->firstItem() + $loop->index   // paginated
                        : $loop->iteration;                        // non-paginated
                @endphp
                {{ $num }}
                </td>
                <td class="px-4 py-3">{{ $route->code_pair }}</td>
                {{-- <td class="px-4 py-3">{{ $route->city_pair }}</td> --}}
                <td class="px-4 py-3"> 
                    @foreach ($route->airlines as $airline)
                        <span class="inline-block bg-gray-100 dark:bg-neutral-800 text-sm px-2 py-1 rounded">
                            {{ $airline->name }}
                            <span class="text-gray-500 text-xs">({{ $airline->icao_code }})</span>
                        </span>
                    @endforeach
                </td>
                <td class="px-4 py-3">{{ $route->status}}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                No route found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4 dark:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700">
        {{ $routes->links() }}
    </div>
</div>

