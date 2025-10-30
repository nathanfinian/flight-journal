<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>Flight Scheduling</x-ui.heading>
        <x-ui.text class="opacity-50">pengaturan jadwal pesawat per minggu</x-ui.text>

        <x-ui.separator class="my-2"/>
     
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('flight-schedule')">Scheduling</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Flight Scheduling</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <form 
                wire:submit="saveChanges"
                class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
            >
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                        <x-ui.label>Flight Number</x-ui.label>
                            <x-ui.input
                                x-mask="******"
                                placeholder="JT660"
                                wire:model="flight_number"
                                x-on:input="$el.value=$el.value.toUpperCase()"
                            />
                        <x-ui.error name="flight_number" />
                    </x-ui.field>
                    </div>
                    <div class="col-span-6">    
                    </div>
                </div>
                <div class="flex justify-items-center gap-6">
                    <x-ui.field required>
                        <x-ui.label>Cabang</x-ui.label>
                        <x-ui.select 
                            placeholder="Select branch..."
                            icon="ps:airplane-takeoff"
                            wire:model="branch_id"
                            >
                            @foreach($branches as $branch)
                                <x-ui.select.option value="{{ $branch->id }}">
                                {{ $branch->name }}
                                </x-ui.select.option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.error name="branch_id" />
                    </x-ui.field>

                    <x-ui.field required>
                        <x-ui.label>Flight Route and Airline</x-ui.label>
                        <x-ui.select 
                            placeholder="Select flight route..."
                            icon="ps:airplane-takeoff"
                            wire:model="airline_route_id"
                        >
                            @foreach($flightRoutes as $id => $label)
                                <x-ui.select.option value="{{ $id }}">
                                    {{ $label }}
                                </x-ui.select.option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.error name="airline_route_id" />
                    </x-ui.field>
                </div>

                <div class="flex justify-items-center items-center mt-6 gap-6">

                    <x-ui.field>
                        <x-ui.label>Equipment</x-ui.label>
                        <x-ui.select 
                            placeholder="Select equipment..."
                            icon="ps:airplane-takeoff"
                            wire:model="equipment_id"
                            >
                            @foreach($equipments as $eqp)
                                <x-ui.select.option value="{{ $eqp->id }}">
                                {{ $eqp->registration }} - {{ $eqp->airline->name }}
                                </x-ui.select.option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.error name="equipment_id" />
                    </x-ui.field>
                    <x-ui.field required>
                        <x-ui.label>ETD</x-ui.label>
                            <x-ui.input 
                                x-mask="99:99"
                                placeholder="14:25"
                                wire:model="sched_dep"
                            />
                        <x-ui.error name="sched_dep" />
                    </x-ui.field>
                    <x-ui.field required>
                        <x-ui.label>ETA</x-ui.label>
                            <x-ui.input 
                                x-mask="99:99"
                                placeholder="15:25"
                                wire:model="sched_arr"
                            />
                        <x-ui.error name="sched_arr" />
                    </x-ui.field>
                </div>
                <x-ui.error name="time_format" />

                <div class="mt-6 gap-6">
                    <!-- Group manages array state - bind model to GROUP only -->
                    <x-ui.label class="my-2">Pilih hari</x-ui.label>
                    <x-ui.checkbox.group wire:model="days" variant="pills" class="flex flex-wrap gap-2">
                       @foreach($dayList as $day)
                            <x-ui.checkbox label="{{ $day->day_name }}" value="{{ $day->id }}" />
                        @endforeach
                    </x-ui.checkbox.group>
                    <x-ui.error name="days" />
                </div>
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