<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           GSE Recaps
        </div>
        <div class="flex items-center  gap-2">
            <x-ui.modal.trigger id="monthly-gse-export">
                <x-ui.button size="sm" variant="outline" icon="ps:microsoft-excel-logo"
                    title="Export Excel bulanan" aria-label="Export Excel bulanan" />
            </x-ui.modal.trigger>
            <x-ui.button
                size="sm"
                variant="outline"
                icon="ps:plus"
                wire:click="generateInvoice"
            >
                Tambah
            </x-ui.button>
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>

    <x-ui.modal id="monthly-gse-export" position="center" heading="Export GSE Bulanan">
        <form wire:submit="exportMonthlyRecaps" class="space-y-4">
            <x-ui.field required>
                <x-ui.label for="gse-export-month">Bulan</x-ui.label>
                <x-ui.input id="gse-export-month" type="month" wire:model="exportMonth" required />
                <x-ui.error name="exportMonth" />
            </x-ui.field>
            <x-ui.field required>
                <x-ui.label>GSE Type</x-ui.label>
                <x-ui.select searchable wire:model="exportGseType" placeholder="Pilih GSE Type">
                    <x-ui.select.option value="">Pilih GSE Type</x-ui.select.option>
                    @foreach ($gseTypes as $gseType)
                        <x-ui.select.option value="{{ $gseType->id }}">{{ $gseType->type_name }}</x-ui.select.option>
                    @endforeach
                </x-ui.select>
                <x-ui.error name="exportGseType" />
            </x-ui.field>
            <x-ui.error name="selectedBranch" />
            <x-ui.field>
                <x-ui.label>Airline (opsional)</x-ui.label>
                <x-ui.select searchable wire:model="exportAirline" placeholder="Semua Airline">
                    <x-ui.select.option value="">Semua Airline</x-ui.select.option>
                    @foreach ($airlines as $airline)
                        <x-ui.select.option value="{{ $airline->id }}">{{ $airline->name }}</x-ui.select.option>
                    @endforeach
                </x-ui.select>
                <x-ui.error name="exportAirline" />
            </x-ui.field>
            <div class="flex justify-end gap-2">
                <x-ui.button variant="outline" x-on:click="$data.close()">Batal</x-ui.button>
                <x-ui.button type="submit" icon="ps:microsoft-excel-logo"
                    wire:loading.attr="disabled" wire:target="exportMonthlyRecaps">Export Excel</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <div class="flex flex-wrap items-center gap-4 justify-between">
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

            <x-ui.label>GSE Type</x-ui.label>
            <x-ui.select
                placeholder="Semua Type"
                wire:model.live="selectedGseType"
                class="mt-1 block w-48 sm:text-sm"
            >
                <x-ui.select.option value="">Semua Type</x-ui.select.option>
                @foreach($gseTypes as $gseType)
                    <x-ui.select.option value="{{ $gseType->id }}">{{ $gseType->type_name }}</x-ui.select.option>
                @endforeach
            </x-ui.select>
        </div>

        <div class="ml-auto flex items-center gap-2">
            <x-ui.label class="whitespace-nowrap">Tanggal</x-ui.label>
            <x-ui.input
                type="date"
                wire:model.live="dateFrom"
                class="w-40"
                placeholder="Dari"
            />
            <span class="text-gray-400">-</span>
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
                <th class="px-4 py-3 text-left">Tanggal Service</th>
                <th class="px-4 py-3 text-left">Nomor ER</th>
                <th class="px-4 py-3 text-left">Airline</th>
                <th class="px-4 py-3 text-left">Registration</th>
                <th class="px-4 py-3 text-left">Aircraft</th>
                <th class="px-4 py-3 text-left">Operator</th>
                <th class="px-4 py-3 text-left">Type</th>
                <th class="px-4 py-3 text-left">Cabang</th>
                {{-- <th class="px-4 py-3 text-left"></th> --}}
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
            @forelse($recaps as $index => $recap)
                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer" wire:click="openEdit({{ $recap->id }})">
                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $recap->service_date?->format('Y-m-d') ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $recap->er_number ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $recap->airline->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $recap->equipment->registration ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $recap->equipment->aircraft->type_name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $recap->operator_name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $recap->gseType->type_name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $recap->branch->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-4 text-center text-gray-400 dark:text-neutral-500">
                        No GSE recap found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
