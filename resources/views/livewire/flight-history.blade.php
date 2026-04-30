<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           History
        </div>
        <div class="flex items-center  gap-2">
            <x-ui.button 
                size="sm"
                variant="outline"
                icon="ps:microsoft-excel-logo"
                onclick="window.open('{{ route('export-flight-history', [
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'branch' => $selectedBranch,
                    'branchName' => $branchName,
                    'airline' => $selectedAirline,
                    'flightNo' => $flightNo,
                    'airlineName' => $airlineName,
                    'type' => $selectedType,
                    'typeName' => $typeName,
                ]) }}', '_blank')"
            >
            </x-ui.button>
            <x-ui.button 
                size="sm"
                variant="outline"
                icon="ps:printer"
                onclick="window.open('{{ route('export-flight-print', [
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'branch' => $selectedBranch,
                    'branchName' => $branchName,
                    'airline' => $selectedAirline,
                    'flightNo' => $flightNo,
                    'airlineName' => $airlineName,
                    'type' => $selectedType,
                    'typeName' => $typeName,
                ]) }}', '_blank')"
            >
            </x-ui.button>
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>

    {{-- 🔼 End filters --}}
    <div class="w-full">

        <div class="grid w-full grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,0.8fr)_minmax(24rem,1.6fr)]">
            <div>
                <x-ui.field>
                    <x-ui.label>Cabang</x-ui.label>
                    <x-ui.select
                        searchable
                        placeholder="Semua Cabang"
                        wire:model.live="selectedBranch"
                        class="mt-1 block w-full sm:text-sm"
                    >
                        <x-ui.select.option value="">Semua Cabang</x-ui.select.option>
                        @foreach($branches as $branch)
                            <x-ui.select.option value="{{ $branch->id }}">{{ $branch->name }}</x-ui.select.option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>
            </div>
            <div>
                <x-ui.field>
                    <x-ui.label>Airline</x-ui.label>
                    <x-ui.select
                        searchable
                        placeholder="Semua Airline"
                        wire:model.live="selectedAirline"
                        class="mt-1 block w-full sm:text-sm"
                    >
                        <x-ui.select.option value="">Semua Airline</x-ui.select.option>
                        @foreach($airlines as $airline)
                            <x-ui.select.option value="{{ $airline->id }}">{{ $airline->name }}</x-ui.select.option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>
            </div>
            <div>
                <x-ui.field>
                    <x-ui.label>Type</x-ui.label>
                    <x-ui.select
                        searchable
                        placeholder="Semua Type"
                        wire:model.live="selectedType"
                        class="mt-1 block w-full sm:text-sm"
                    >
                        <x-ui.select.option value="">Semua Type</x-ui.select.option>
                        @foreach($types as $type)
                            <x-ui.select.option value="{{ $type->id }}">{{ $type->name }}</x-ui.select.option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>
            </div>
            <div>
                <x-ui.label>Flight No</x-ui.label>
                <x-ui.input
                    wire:model.live.debounce.400ms="flightNo"
                    maxlength="10"
                    placeholder="Flight No"
                />
            </div>
            <div class="min-w-0">
                <x-ui.label>Tanggal</x-ui.label>
                <div class="mt-1 grid grid-cols-2 gap-2">
                    <x-ui.input
                        type="date"
                        wire:model.live="dateFrom"
                        class="w-full"
                        placeholder="Dari"
                    />
                    <x-ui.input
                        type="date"
                        wire:model.live="dateTo"
                        class="w-full"
                        placeholder="Sampai"
                    />
                </div>
            </div>
        </div>

    </div>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Branch</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Arrival</th>
                <th class="px-4 py-3 text-left">Departure</th>
                <th class="px-4 py-3 text-left">Airline</th>
                <th class="px-4 py-3 text-left">Type</th>
                <th class="px-4 py-3 text-left">Origin</th>
                <th class="px-4 py-3 text-left">Departure</th>
                <th class="px-4 py-3 text-left">Time (ATA - ATD)</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
            @forelse($actualFlights as $index => $flight)
                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer" wire:click="openEdit({{ $flight->id }})">
                    <td class="px-4 py-3">{{ $index + 1 }}</td>

                    <td class="px-4 py-3">{{ $flight->branch->name ?? '—' }}</td>

                    <td class="px-4 py-3 font-semibold">{{ $flight->service_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $flight->origin_flight_no ?? '—' }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $flight->departure_flight_no ?? '—' }}</td>

                    <td class="px-4 py-3">{{ $flight->originAirlineRoute->airline->name ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $flight->flightType->name ?? '—' }}</td>

                    <td class="px-4 py-3">
                        {{ $flight->originAirlineRoute->airportRoute->origin->iata ?? '---' }}
                        →
                        {{ $flight->originAirlineRoute->airportRoute->destination->iata ?? '---' }}
                    </td>
                    <td class="px-4 py-3">
                        {{ $flight->departureAirlineRoute->airportRoute->origin->iata ?? '---' }}
                        →
                        {{ $flight->departureAirlineRoute->airportRoute->destination->iata ?? '---' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ substr($flight->actual_arr, 0, 5) }} – {{ substr($flight->actual_dep, 0, 5) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-4 text-center text-gray-400 dark:text-neutral-500">
                        No flights found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>