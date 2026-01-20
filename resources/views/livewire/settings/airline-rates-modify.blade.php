<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>Flight Scheduling</x-ui.heading>
        <x-ui.text class="opacity-50">pengaturan jadwal pesawat per minggu</x-ui.text>

        <x-ui.separator class="my-2"/>
     
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.airlineRates')">List Rate Airline</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Modify Rate Airline</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <form 
                wire:submit="saveChanges"
                class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
            >
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Airline</x-ui.label>
                            <x-ui.select 
                                placeholder="Select airline..."
                                icon="ps:airplane-takeoff"
                                wire:model.live="airline_id"
                                >
                                @foreach($airlines as $airline)
                                    <x-ui.select.option value="{{ $airline->id }}">
                                    {{ $airline->name }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="airline_id" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        {{-- Empty --}}
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Title</x-ui.label>
                            <x-ui.input
                            wire:model.defer="charge_name"
                            maxlength="100"
                            placeholder="Lion Air Palangkaraya Sept 2025"
                            />
                            <x-ui.error name="charge_name" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Bill Code</x-ui.label>
                            <x-ui.input
                            wire:model.defer="charge_code"
                            maxlength="100"
                            placeholder="LNPKY2509"
                            />
                            <x-ui.error name="charge_code" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Ground Handling Fee</x-ui.label>
                            <x-ui.input 
                                wire:model.defer="ground_fee"
                                x-mask:dynamic="$money($input, ',')" 
                                placeholder="0.00">
                                <x-slot name="prefix">Rp</x-slot>
                            </x-ui.input>
                            <x-ui.error name="ground_fee" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field>
                            <x-ui.label>Cargo (per KG)</x-ui.label>
                            <x-ui.input 
                                wire:model.defer="cargo_fee"
                                x-mask:dynamic="$money($input, ',')" 
                                placeholder="0.00">
                                <x-slot name="prefix">Rp</x-slot>
                            </x-ui.input>
                            <x-ui.error name="cargo_fee" />
                        </x-ui.field>
                    </div>
                </div>
                @foreach ($flightTypes as $type)
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                    <x-ui.field>
                        <x-ui.label>{{ $type->name }}</x-ui.label>
                        <x-ui.description>(Persentase dikalikan dengan ground handling fee)</x-ui.description>

                        {{-- hidden flight type id --}}
                        <input 
                            type="hidden"
                            wire:model="percentages.{{ $type->id }}.flight_type_id"
                            value="{{ $type->id }}"
                        />

                        {{-- percentage input --}}
                        <x-ui.input
                            wire:model.defer="percentages.{{ $type->id }}.percentage"
                            placeholder="0"
                            inputmode="decimal"
                        >
                            <x-slot name="suffix">%</x-slot>
                        </x-ui.input>

                        <x-ui.error name="percentages.{{ $type->id }}.percentage" />
                    </x-ui.field>

                    </div>
                    <div class="col-span-6">
                        <x-ui.label>&nbsp;</x-ui.label>
                        <div class="flex justify-start">
                            <x-ui.button 
                                size="sm"
                                variant="outline"
                                icon="ps:trash-simple"
                                wire:click="deletePivot({{ $type->id }})"
                                class="mt-9"
                            >
                            </x-ui.button>
                            <x-ui.text class="opacity-50 mt-10 ml-3">(Kosongkan atau click untuk menghapus)</x-ui.text>
                        </div>
                        
                    </div>
                </div>
                @endforeach
                
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