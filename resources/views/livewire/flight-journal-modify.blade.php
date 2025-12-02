@php
    use Carbon\Carbon;
@endphp
<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>Flight</x-ui.heading>
        <x-ui.text class="opacity-50">konfirmasi input flight harian</x-ui.text>

        <x-ui.separator class="my-2"/>
     
        <x-ui.breadcrumbs>
            @if ($this->type === 'scheduled')
                <x-ui.breadcrumbs.item wire:navigate.hover :href="route('flight-journal')">Journal</x-ui.breadcrumbs.item>
            @elseif ($this->type === 'actual')
                <x-ui.breadcrumbs.item wire:navigate.hover :href="route('flight-journal.actual')">Journal</x-ui.breadcrumbs.item>
            @endif
            
            <x-ui.breadcrumbs.item>Flight Journal</x-ui.breadcrumbs.item>
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
                    <div class="col-span-6">
                        <x-ui.field>
                            <x-ui.label>Tanggal</x-ui.label>
                            <x-ui.input 
                                x-mask="9999-99-99"
                                placeholder="YYYY/MM/DD"
                                wire:model="form.service_date"
                            />
                        </x-ui.field>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Arrival Flight Number</x-ui.label>
                                <x-ui.input
                                    x-mask="******"
                                    placeholder="JT660"
                                    wire:model="form.origin_flight_number"
                                    x-on:input="$el.value=$el.value.toUpperCase()"
                                />
                            <x-ui.error name="form.origin_flight_number" />
                            <x-ui.error name="origin_flight_number" />
                            <x-ui.error name="same_flight_no" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Departure Flight Number</x-ui.label>
                                <x-ui.input
                                    x-mask="******"
                                    placeholder="JT660"
                                    wire:model="form.departure_flight_number"
                                    x-on:input="$el.value=$el.value.toUpperCase()"
                                />
                            <x-ui.error name="form.departure_flight_number" />
                            <x-ui.error name="departure_flight_number" />
                        </x-ui.field>
                    </div>
                </div>
                
                {{-- Routes Input Group --}}
                <div class="flex justify-items-center gap-6">
                    <x-ui.field required>
                        <x-ui.label>Origin</x-ui.label>
                        <x-ui.select 
                            placeholder="Select flight route..."
                            icon="ps:airplane-takeoff"
                            wire:model="form.origin_route"
                            searchable
                        >
                            @foreach($airlineRoutes as $id => $label)
                                <x-ui.select.option value="{{ $id }}">
                                    {{ $label }}
                                </x-ui.select.option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.error name="form.origin_route" />
                        <x-ui.error name="origin_route" />
                        <x-ui.error name="same_route" />
                    </x-ui.field>
                    <x-ui.field required>
                        <x-ui.label>Departure</x-ui.label>
                        <x-ui.select 
                            placeholder="Select flight route..."
                            icon="ps:airplane-takeoff"
                            wire:model="form.departure_route"
                            searchable
                        >
                            @foreach($airlineRoutes as $id => $label)
                                <x-ui.select.option value="{{ $id }}">
                                    {{ $label }}
                                </x-ui.select.option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.error name="form.departure_route" />
                        <x-ui.error name="departure_route" />
                    </x-ui.field>
                </div>

                {{-- Equipment input block  --}}
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Origin Equipment</x-ui.label>
                            <x-ui.select 
                                placeholder="Select equipment..."
                                icon="ps:airplane-takeoff"
                                wire:model="form.origin_equipment"
                                clearable
                                searchable
                                >
                                @foreach($equipments as $eqp)
                                    <x-ui.select.option value="{{ $eqp->id }}">
                                    {{ $eqp->registration }} - {{ $eqp->airline->name }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="form.origin_equipment" />
                            <x-ui.error name="origin_equipment" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                       <x-ui.field required>
                            <x-ui.label>Departure Equipment</x-ui.label>
                            <x-ui.select 
                                placeholder="Select equipment..."
                                icon="ps:airplane-takeoff"
                                wire:model="form.departure_equipment"
                                clearable
                                searchable
                                >
                                @foreach($equipments as $eqp)
                                    <x-ui.select.option value="{{ $eqp->id }}">
                                    {{ $eqp->registration }} - {{ $eqp->airline->name }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="form.departure_equipment" />
                            <x-ui.error name="departure_equipment" />
                        </x-ui.field>
                    </div>
                </div>

                {{-- Time inputs --}}
                <div class="flex justify-items-center items-center mt-6 gap-6">
                    <x-ui.field required>
                        <x-ui.label>ETA</x-ui.label>
                            <x-ui.input 
                                x-mask="99:99"
                                placeholder="13:25"
                                wire:model="form.sched_arr"
                            />
                    </x-ui.field>
                    <x-ui.field required>
                        <x-ui.label>ETD</x-ui.label>
                            <x-ui.input 
                                x-mask="99:99"
                                placeholder="14:25"
                                wire:model="form.sched_dep"
                            />
                    </x-ui.field>
                    <x-ui.field required>
                        <x-ui.label>ATA</x-ui.label>
                        <x-ui.input 
                            x-mask="99:99"
                            placeholder="13:25"
                            wire:model="form.actual_arrival"
                        />
                    </x-ui.field>
                    <x-ui.field required>
                        <x-ui.label>ATD</x-ui.label>
                        <x-ui.input 
                            x-mask="99:99"
                            placeholder="14:25"
                            wire:model="form.actual_departure"
                        />
                    </x-ui.field>
                </div>
                {{-- Time Inputs Error handler --}}
                <x-ui.error name="form.sched_arr" />
                <x-ui.error name="form.sched_dep" />
                <x-ui.error name="form.actual_arrival" />
                <x-ui.error name="form.actual_departure" />
                <x-ui.error name="time_format" />

                {{-- Cabang Input Group --}}
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Cabang</x-ui.label>
                            <x-ui.select 
                                placeholder="Select branch..."
                                icon="ps:airplane-takeoff"
                                wire:model="form.branch_id"
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
                    <div class="col-span-6">
                        {{-- Empty --}}
                    </div>
                </div>
                
                {{-- Pax and Ground Time --}}
                <div class="flex justify-items-center items-center mt-6 gap-6">
                    <x-ui.field>
                        <x-ui.label>PAX</x-ui.label>
                            <x-ui.input 
                                x-mask="999"
                                placeholder="150"
                                wire:model="form.pax"
                            />
                    </x-ui.field>
                    <x-ui.field>
                        <x-ui.label>Ground Time (Minutes)</x-ui.label>
                        <x-ui.input 
                            x-mask="9999"
                            placeholder="Minutes"
                            wire:model="form.ground_time"
                        />
                    </x-ui.field>
                </div>
                {{-- Time Inputs Error handler --}}
                <x-ui.error name="form.pax" />
                <x-ui.error name="form.ground_time" />

                {{-- Pilot in Command --}}
                <div class="grid grid-cols-12 gap-6 mt-6">
                    <div class="col-span-6">
                        <x-ui.field>
                            <x-ui.label>PIC</x-ui.label>
                            <x-ui.input
                                placeholder="Nama"
                                wire:model="form.pic" 
                            />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                       {{-- empty --}}
                    </div>
                </div>
                <x-ui.error name="form.pic" />

                {{-- Notes --}}
                <div class="grid grid-cols-12 gap-6 mt-6">
                    <div class="col-span-6">
                        <x-ui.textarea 
                            wire:model="form.notes" 
                            placeholder="notes"
                            rows="2"
                        />
                    </div>
                    <div class="col-span-6">
                       {{-- empty --}}
                    </div>
                </div>
                <x-ui.error name="form.notes" />

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