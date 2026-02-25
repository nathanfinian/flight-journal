<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           Charter Deposit
        </div>
        <div class="flex items-center  gap-2">
            <x-ui.button 
                size="sm"
                variant="outline"
                icon="ps:invoice"
                wire:click="generateInvoice"
            >
                Create
            </x-ui.button>
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
                <th class="px-4 py-3 text-left">Receipt</th>
                <th class="px-4 py-3 text-left">Cabang</th>
                <th class="px-4 py-3 text-left">Perusahaan</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Deskripsi</th>
                <th class="px-4 py-3 text-left">Harga</th>
                <th class="px-4 py-3 text-left"></th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
            @forelse($deposits as $index => $deposit)
                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer" wire:click="openEdit({{ $deposit->id }})">
                    <td class="px-4 py-3">{{ $index + 1 }}</td>

                    <td class="px-4 py-3">{{ $deposit->receipt_number}}</td>
                    <td class="px-4 py-3">{{ $deposit->branch->name ?? '—' }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $deposit->received_from_company ?? '-'}}</td>

                    <td class="px-4 py-3 font-semibold">{{ $deposit->receipt_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $deposit->description ?? '-'}}</td>
                    <td class="px-4 py-3 font-semibold">{{ number_format($deposit->value) ?? '-'}}</td>
                    {{-- This is the print button --}}
                    <td class="px-4 py-3 font-semibold text-center" wire:click.stop>
                        <x-ui.link 
                            href="{{ route('deposit.print', $deposit->receipt_number) }}" 
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-800 transition"
                            openInNewTab
                        >
                            <x-ui.icon name="printer" class="w-5 h-5 text-gray-600 dark:text-white"/>
                        </x-ui.link>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-4 text-center text-gray-400 dark:text-neutral-500">
                        No deposits found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>