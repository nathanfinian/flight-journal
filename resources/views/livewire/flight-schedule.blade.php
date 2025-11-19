<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           Flight Scheduling
        </div>
        <div class="flex items-center">
            <x-ui.button 
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('flight-schedule.create')" 
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>

    <div class="flex items-center gap-4">
        <x-ui.label>
            Cabang
        </x-ui.label>
        <x-ui.select
            placeholder="Semua Cabang"
            wire:model.live="selectedBranch"
            class="mt-1 block w-48 sm:text-sm"
            searchable
        >
            <x-ui.select.option value="">Semua Cabang</x-ui.select.option>
            @foreach($branches as $branch)
                <x-ui.select.option value="{{ $branch->id }}">{{ $branch->name }}</x-ui.select.option>
            @endforeach
        </x-ui.select>

        <x-ui.label>
            Airline
        </x-ui.label>
        <x-ui.select
            placeholder="Semua Airline"
            wire:model.live="selectedAirline"
            class="mt-1 block w-72 sm:text-sm"
            searchable
        >
            <x-ui.select.option value="">Semua Airline</x-ui.select.option>
            @foreach($airlines as $airline)
                <x-ui.select.option value="{{ $airline->id }}">{{ $airline->name }}</x-ui.select.option>
            @endforeach
        </x-ui.select>
    </div>
    {{-- 🔼 End filters --}}
    <x-ui.separator class="my-2"/>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Branch</th>
                <th class="px-4 py-3 text-left">Origin</th>
                <th class="px-4 py-3 text-left">Departure</th>
                <th class="px-4 py-3 text-left">Equipment</th>
                <th class="px-4 py-3 text-left">Airline</th>
                <th class="px-4 py-3 text-left">Origin Route</th>
                <th class="px-4 py-3 text-left">Departure Route</th>
                <th class="px-4 py-3 text-left">Time (ETD - ETA)</th>
                <th class="px-4 py-3 text-left">Days</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
            @forelse($flights as $index => $flight)
                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer" wire:click="openEdit({{ $flight->id }})">
                    <td class="px-4 py-3">{{ $index + 1 }}</td>

                    <td class="px-4 py-3">{{ $flight->branch->name ?? '—' }}</td>

                    <td class="px-4 py-3 font-semibold">{{ $flight->origin_flight_no }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $flight->departure_flight_no }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $flight->equipment->registration ?? '—' }}</td>

                    <td class="px-4 py-3">{{ $flight->originAirlineRoute->airline->name ?? '—' }}</td>

                    <td class="px-4 py-3">
                        {{ $flight->originAirportRoute->origin->iata ?? '---' }}
                        →
                        {{ $flight->originAirportRoute->destination->iata ?? '---' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $flight->departureAirportRoute->origin->iata ?? '---' }}
                        →
                        {{ $flight->departureAirportRoute->destination->iata ?? '---' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ substr($flight->sched_dep, 0, 5) }} – {{ substr($flight->sched_arr, 0, 5) }}
                    </td>

                    <td class="px-4 py-3">
                        @foreach($flight->days as $day)
                            <span class="inline-block bg-blue-100 text-blue-700 dark:bg-blue-700/20 dark:text-blue-300 text-xs font-medium px-2 py-1 rounded-full mr-1">
                               {{ substr($day->day_name, 0, 3) }}
                            </span>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-4 text-center text-gray-400 dark:text-neutral-500">
                        No scheduled flights found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4 dark:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700">
        {{ $flights->links() }}
    </div>
</div>