<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           History
        </div>
        <div class="flex items-center">
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>

    {{-- 🔼 End filters --}}
    <div class="flex flex-wrap items-center gap-4 justify-between">
        {{-- Left group: existing filters --}}
        <div class="flex flex-wrap items-center gap-4">
            <x-ui.label>Cabang</x-ui.label>
            <x-ui.select
                placeholder="Semua Cabang"
                wire:model.live="selectedBranch"
                class="mt-1 block w-48 sm:text-sm"
            >
                <x-ui.select.option value="">Semua Cabang</x-ui.select.option>
                @foreach($branches as $branch)
                    <x-ui.select.option value="{{ $branch->id }}">{{ $branch->name }}</x-ui.select.option>
                @endforeach
            </x-ui.select>

            <x-ui.label>Airline</x-ui.label>
            <x-ui.select
                placeholder="Semua Airline"
                wire:model.live="selectedAirline"
                class="mt-1 block w-48 sm:text-sm"
            >
                <x-ui.select.option value="">Semua Airline</x-ui.select.option>
                @foreach($airlines as $airline)
                    <x-ui.select.option value="{{ $airline->id }}">{{ $airline->name }}</x-ui.select.option>
                @endforeach
            </x-ui.select>
        </div>

        {{-- Right group: date range --}}
        <div class="ml-auto flex items-center gap-2">
            <x-ui.label class="whitespace-nowrap">Tanggal</x-ui.label>

            <x-ui.input
                type="date"
                wire:model.live="dateFrom"
                class="w-40"
                placeholder="Dari"
            />

            <span class="text-gray-400">–</span>

            <x-ui.input
                type="date"
                wire:model.live="dateTo"
                class="w-40"
                placeholder="Sampai"
            />
        </div>
    </div>


   <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Branch</th>
                <th class="px-4 py-3 text-left">Flight No</th>
                <th class="px-4 py-3 text-left">Equipment</th>
                <th class="px-4 py-3 text-left">Airline</th>
                <th class="px-4 py-3 text-left">Route</th>
                <th class="px-4 py-3 text-left">Time (ATD - ATA)</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
            @forelse($actualFlights as $index => $flight)
                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer" wire:click="openEdit({{ $flight->id }})">
                    <td class="px-4 py-3">{{ $index + 1 }}</td>

                    <td class="px-4 py-3">{{ $flight->branch->name ?? '—' }}</td>

                    <td class="px-4 py-3 font-semibold">{{ $flight->flight_no }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $flight->equipment->registration ?? '—' }}</td>

                    <td class="px-4 py-3">{{ $flight->airlineRoute->airline->name ?? '—' }}</td>

                    <td class="px-4 py-3">
                        {{ $flight->airlineRoute->airportRoute->origin->iata ?? '---' }}
                        →
                        {{ $flight->airlineRoute->airportRoute->destination->iata ?? '---' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ substr($flight->actual_dep, 0, 5) }} – {{ substr($flight->actual_arr, 0, 5) }}
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
    
</div>