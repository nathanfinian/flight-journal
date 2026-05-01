<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>GSE Invoice</x-ui.heading>
        <x-ui.text class="opacity-50">Buat invoice GSE berdasarkan recap dalam rentang tanggal.</x-ui.text>

        <x-ui.separator class="my-2"/>

        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('invoicegse')">List GSE Invoice</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>{{ $isEdit ? 'Modify GSE Invoice' : 'Create GSE Invoice' }}</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <form
                wire:submit="saveChanges"
                class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
            >
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-3">
                        <x-ui.field required>
                            <x-ui.label>Invoice Number</x-ui.label>
                            <x-ui.input wire:model.defer="form.invoice_number" readonly />
                            <x-ui.error name="form.invoice_number" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-3">
                        <x-ui.field required>
                            <x-ui.label>GSE Type</x-ui.label>
                            <x-ui.select
                                placeholder="Select GSE type..."
                                wire:model.live="form.gse_type_id"
                            >
                                @foreach($gseTypes as $gseType)
                                    <x-ui.select.option value="{{ $gseType->id }}">
                                        {{ $gseType->service_name }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="form.gse_type_id" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-3">
                        <x-ui.field required>
                            <x-ui.label>Cabang</x-ui.label>
                            <x-ui.select
                                searchable
                                placeholder="Select branch..."
                                wire:model.live="form.branch_id"
                            >
                                @foreach($branches as $branch)
                                    <x-ui.select.option value="{{ $branch->id }}">
                                        {{ $branch->name }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="form.branch_id" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-3">
                        <x-ui.field required>
                            <x-ui.label>Airline</x-ui.label>
                            <x-ui.select
                                searchable
                                placeholder="Select airline..."
                                wire:model.live="form.airline_id"
                            >
                                @foreach($airlines as $airline)
                                    <x-ui.select.option value="{{ $airline->id }}">
                                        {{ $airline->name }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="form.airline_id" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-5">
                        <x-ui.field required>
                            <x-ui.label>Date From</x-ui.label>
                            <x-ui.input
                                type="date"
                                wire:model.defer="form.dateFrom"
                                class="dark:scheme-dark dark:[&::-webkit-calendar-picker-indicator]:invert"
                            />
                            <x-ui.error name="form.dateFrom" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-5">
                        <x-ui.field required>
                            <x-ui.label>Date To</x-ui.label>
                            <x-ui.input
                                type="date"
                                wire:model.defer="form.dateTo"
                                class="dark:scheme-dark dark:[&::-webkit-calendar-picker-indicator]:invert"
                            />
                            <x-ui.error name="form.dateTo" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-2 flex items-end">
                        <x-ui.button type="button" variant="outline" wire:click="loadRecaps">
                            Load Recaps
                        </x-ui.button>
                    </div>
                </div>

                <div class="rounded-lg border border-black/10 dark:border-white/10">
                    <div class="flex items-center justify-between border-b border-black/10 px-4 py-3 dark:border-white/10">
                        <div>
                            <x-ui.text class="font-medium">Recap Selection</x-ui.text>
                            <x-ui.text class="opacity-60 text-sm">Recaps in this date range will be attached through the pivot table.</x-ui.text>
                        </div>
                        <x-ui.text class="font-medium">
                            Total: Rp. {{ number_format($this->selectedTotal, 2, ',', '.') }}
                        </x-ui.text>
                    </div>

                    @if (count($availableRecaps))
                        <x-ui.checkbox.group wire:model.live="selectedRecapIds">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-100 dark:bg-neutral-800/60">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Pick</th>
                                            <th class="px-4 py-3 text-left">Date</th>
                                            <th class="px-4 py-3 text-left">ER</th>
                                            <th class="px-4 py-3 text-left">Flight</th>
                                            <th class="px-4 py-3 text-left">Airline</th>
                                            <th class="px-4 py-3 text-left">Equipment</th>
                                            <th class="px-4 py-3 text-left">Charge Type</th>
                                            <th class="px-4 py-3 text-left">Rate</th>
                                            <th class="px-4 py-3 text-left">Qty</th>
                                            <th class="px-4 py-3 text-left">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-black/10 dark:divide-white/10">
                                        @foreach ($availableRecaps as $recap)
                                            <tr class="align-top">
                                                <td class="px-4 py-3">
                                                    <x-ui.checkbox value="{{ $recap['id'] }}" />
                                                </td>
                                                <td class="px-4 py-3">{{ $recap['service_date'] }}</td>
                                                <td class="px-4 py-3">
                                                    <div>{{ $recap['er_number'] }}</div>
                                                    <div class="text-xs text-neutral-500">{{ $recap['branch'] }}</div>
                                                </td>
                                                <td class="px-4 py-3">{{ $recap['flight_number'] }}</td>
                                                <td class="px-4 py-3">{{ $recap['airline'] }}</td>
                                                <td class="px-4 py-3">{{ $recap['equipment'] }}</td>
                                                <td class="px-4 py-3">
                                                    {{ $pivotRows[$recap['id']]['charge_type'] ?: '-' }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    Rp. {{ number_format((float) ($pivotRows[$recap['id']]['service_rate'] ?? 0), 2, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <x-ui.input
                                                        type="number"
                                                        min="0"
                                                        step="0.01"
                                                        wire:model.live="pivotRows.{{ $recap['id'] }}.quantity"
                                                        class="min-w-24"
                                                    />
                                                </td>
                                                <td class="px-4 py-3">
                                                    Rp. {{ number_format((float) ($pivotRows[$recap['id']]['amount'] ?? 0), 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </x-ui.checkbox.group>
                    @else
                        <div class="px-4 py-8 text-center text-neutral-500">
                            Belum ada recap yang dimuat. Pilih GSE type, cabang, airline, dan rentang tanggal, lalu klik Load Recaps.
                        </div>
                    @endif
                </div>

                <x-ui.error name="selectedRecapIds"/>

                <div class="flex items-center justify-between mt-6">
                    <x-ui.button type="submit">Save changes</x-ui.button>
                    @if ($this->isEdit)
                        <x-ui.modal.trigger id="delete-modal">
                            <x-ui.button variant="ghost">
                                <x-ui.icon name="ps:trash" variant="thin" class="text-red-600 dark:text-red-500"/>
                            </x-ui.button>
                        </x-ui.modal.trigger>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <x-ui.modal
        id="delete-modal"
        position="center"
        heading="Delete GSE Invoice"
        description="Yakin ingin menghapus invoice GSE ini?"
    >
        <div class="mt-4 flex justify-end space-x-2">
            <x-ui.button
                variant="outline"
                x-on:click="$data.close();"
            >
                Cancel
            </x-ui.button>
            <x-ui.button
                variant="danger"
                wire:click.stop="delete"
            >
                Delete
            </x-ui.button>
        </div>
    </x-ui.modal>
</div>
