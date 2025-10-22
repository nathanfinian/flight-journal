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
                {{-- change to create later --}}
                :href="route('flight-schedule')" 
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Branch</th>
                <th class="px-4 py-3 text-left">Airline</th>
                <th class="px-4 py-3 text-left">Route</th>
                <th class="px-4 py-3 text-left">Time</th>
                <th class="px-4 py-3 text-left">Days</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            {{-- @forelse ($flight-schedule as $flight-schedule)
            <tr 
                class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                wire:click="openEdit({{ $flight-schedule->id }})"
                >
                <td class="px-4 py-3">
                @php
                    $num = method_exists($flight-schedule, 'firstItem') && $flight-schedule->firstItem()
                        ? $flight-schedule->firstItem() + $loop->index   // paginated
                        : $loop->iteration;                        // non-paginated
                @endphp
                {{ $num }}
                </td>
                <td class="px-4 py-3">{{ $flight-schedule->airline->name }}</td>
                <td class="px-4 py-3">{{ $flight-schedule->registration }}</td>
                <td class="px-4 py-3">{{ $flight-schedule->aircraft->type_name }}</td>
                <td class="px-4 py-3">{{ $flight-schedule->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                No flight-schedule found.
                </td>
            </tr>
            @endforelse --}}
        </tbody>
    </table>
</div>