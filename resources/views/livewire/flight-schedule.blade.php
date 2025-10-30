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

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Branch</th>
                <th class="px-4 py-3 text-left">Flight No</th>
                <th class="px-4 py-3 text-left">Airline</th>
                <th class="px-4 py-3 text-left">Route</th>
                <th class="px-4 py-3 text-left">Time (ETD - ETA)</th>
                <th class="px-4 py-3 text-left">Days</th>
                {{-- <th class="px-4 py-3 text-left"></th> --}}
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
            @forelse($flights as $index => $flight)
                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer" wire:click="openEdit({{ $flight->id }})">
                    <td class="px-4 py-3">{{ $index + 1 }}</td>

                    <td class="px-4 py-3">{{ $flight->branch->name ?? '—' }}</td>

                    <td class="px-4 py-3 font-semibold">{{ $flight->flight_no }}</td>

                    <td class="px-4 py-3">{{ $flight->airlineRoute->airline->name ?? '—' }}</td>

                    <td class="px-4 py-3">
                        {{ $flight->airlineRoute->airportRoute->origin->iata ?? '---' }}
                        →
                        {{ $flight->airlineRoute->airportRoute->destination->iata ?? '---' }}
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

                    {{-- <td class="px-4 py-3 text-right">
                        <x-ui.button
                            wire:navigate
                            :href="route('flight-schedule.edit', ['scheduled' => $flight->id])"
                            size="sm"
                            variant="soft"
                        >
                            Edit
                        </x-ui.button>
                    </td> --}}
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
</div>