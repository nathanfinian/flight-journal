<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>Airline Invoice</x-ui.heading>
        <x-ui.text class="opacity-50">Generate invoice untuk airline</x-ui.text>

        <x-ui.separator class="my-2"/>
     
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('invoice')">List Invoice</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Invoice</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <form 
                wire:submit="saveChanges"
                class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
            >
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Title</x-ui.label>
                            <x-ui.input
                            wire:model.defer="form.title"
                            maxlength="80"
                            placeholder="Tagihan Lion Januari 2024"
                            />
                            <x-ui.error name="form.title" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Invoice Number</x-ui.label>
                            <x-ui.input
                            disabled
                            wire:model.defer="form.invoice_number"
                            maxlength="80"
                            />
                            <x-ui.error name="form.invoice_number" />
                        </x-ui.field>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Tanggal Tagihan</x-ui.label>
                            <x-ui.input
                                type="date"
                                wire:model="form.date"
                                placeholder="Date"
                                class="
                                    dark:[color-scheme:dark]
                                    dark:[&::-webkit-calendar-picker-indicator]:invert
                                "
                            />
                            <x-ui.error name="form.date" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Tanggal Tenggat Tagihan</x-ui.label>
                            <x-ui.input
                                type="date"
                                wire:model="form.due_date"
                                placeholder="Date"
                                class="
                                    dark:[color-scheme:dark]
                                    dark:[&::-webkit-calendar-picker-indicator]:invert
                                "
                            />
                            <x-ui.error name="form.due_date" />
                        </x-ui.field>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Rate Airline</x-ui.label>
                            <x-ui.select 
                                placeholder="Select rate airline..."
                                icon="ps:airplane-takeoff"
                                wire:model.live="form.airline_rates_id"
                                >
                                @foreach($rates as $rate)
                                    <x-ui.select.option value="{{ $rate->id }}">
                                    {{ $rate->charge_name }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="form.airline_rates_id" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        {{-- empty --}}
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field>
                            <x-ui.label>Ground Fee</x-ui.label>
                            <x-ui.input 
                                disabled
                                wire:model="ground_fee"
                                placeholder="0.00">
                                <x-slot name="prefix">Rp</x-slot>
                            </x-ui.input>
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field>
                            <x-ui.label>Cargo Fee</x-ui.label>
                            <x-ui.input 
                                disabled
                                wire:model="cargo_fee"
                                placeholder="0.00">
                                <x-slot name="prefix">Rp</x-slot>
                            </x-ui.input>
                        </x-ui.field>
                    </div>
                </div>
                @if ($flightTypesPercent)
                @foreach ($flightTypesPercent as $type)
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-6">
                            <x-ui.field>
                                <x-ui.label>{{ $type['typeName'] }}</x-ui.label>
                                <x-ui.input 
                                    disabled
                                    :value="$type['percentage']"
                                    >
                                    <x-slot name="suffix">%</x-slot>
                                </x-ui.input>
                            </x-ui.field>
                        </div>
                        <div class="col-span-6">
                            <x-ui.label>&nbsp;</x-ui.label>
                            <x-ui.text class="opacity-50 mt-4">(Persentase Tarif dari ground handling fee)</x-ui.text>
                        </div>
                    </div>
                @endforeach
                @endif
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field>
                            <x-ui.label>Airline</x-ui.label>
                            <x-ui.select 
                                placeholder="Select airline..."
                                icon="ps:airplane-takeoff"
                                wire:model.live="form.airline_id"
                                disabled
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
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Cabang</x-ui.label>
                            <x-ui.select 
                                placeholder="Select branch..."
                                icon="ps:airplane-takeoff"
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
                </div>
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Tanggal Mulai Flight</x-ui.label>
                            <x-ui.input
                                type="date"
                                wire:model="form.dateFrom"
                                placeholder="Dari"
                                class="
                                    dark:[color-scheme:dark]
                                    dark:[&::-webkit-calendar-picker-indicator]:invert
                                "
                            />
                            <x-ui.error name="form.dateFrom" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Tanggal Akhir Flight</x-ui.label>
                            <x-ui.input
                                type="date"
                                wire:model="form.dateTo"
                                placeholder="Sampai"
                                class="
                                    dark:[color-scheme:dark]
                                    dark:[&::-webkit-calendar-picker-indicator]:invert
                                "
                            />
                            <x-ui.error name="form.dateTo" />
                        </x-ui.field>
                    </div>
                </div>
                <div class="mt-6 gap-6">
                    
                </div>
                
                <div class="flex items-center justify-between mt-6">
                    <x-ui.button type="submit">Generate Invoice</x-ui.button>
                </div>
            </form>
        </div>
    </div>
    <x-ui.modal 
        id="delete-modal"
        position="center"
        heading="Delete Branch"
        description="Yakin ingin menghapus jadwal flight ini?"
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