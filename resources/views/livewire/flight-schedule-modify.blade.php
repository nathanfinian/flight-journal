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
                <div class="flex justify-items-center gap-6">
                    <x-ui.field required>
                        <x-ui.label>Cabang</x-ui.label>
                        <x-ui.select 
                            placeholder="Select branch..."
                            icon="ps:airplane-takeoff"
                            wire:model="branches"
                            >
                            @foreach($branches as $branch)
                                <x-ui.select.option value="{{ $branch->id }}">
                                {{ $branch->name }}
                                </x-ui.select.option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.error name="branches" />
                    </x-ui.field>

                    <x-ui.field required>
                        <x-ui.label>Flight Route and Airline</x-ui.label>
                        <x-ui.select 
                            placeholder="Select branch..."
                            icon="ps:airplane-takeoff"
                            wire:model="flightRoute"
                            >
                            @foreach($flightRoute as $id => $label)
                                <x-ui.select.option value="{{ $id }}">
                                {{ $label }}
                                </x-ui.select.option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.error name="flightRoute" />
                    </x-ui.field>
                </div>

                <div class="flex justify-items-center items-center mt-6 gap-6">

                    <x-ui.field required>
                        <x-ui.label>Equipment</x-ui.label>
                        <x-ui.select 
                            placeholder="Select equipment..."
                            icon="ps:airplane-takeoff"
                            wire:model="equipment"
                            >
                            @foreach($equipments as $eqp)
                                <x-ui.select.option value="{{ $eqp->id }}">
                                {{ $eqp->registration }} - {{ $eqp->airline->name }}
                                </x-ui.select.option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.error name="equipment" />
                    </x-ui.field>
                    <x-ui.field required>
                        <x-ui.label>ETD</x-ui.label>
                            <x-ui.input 
                                x-mask="99:99"
                                placeholder="14:25"
                            />
                        <x-ui.error name="sched_dep" />
                    </x-ui.field>
                    <x-ui.field required>
                        <x-ui.label>ETA</x-ui.label>
                            <x-ui.input 
                                x-mask="99:99"
                                placeholder="15:25"
                            />
                        <x-ui.error name="sched_arr" />
                    </x-ui.field>
                </div>

                <div class="flex justify-items-center items-center mt-6 gap-6">
                    <!-- Group manages array state - bind model to GROUP only -->
                    <x-ui.checkbox 
                        wire:model="agreed"
                        label="I agree to the terms and conditions"
                        description="By checking this box, you agree to abide by our terms and conditions."
                    />
                </div>
                
                <x-ui.button 
                    type="submit"
                >Save changes</x-ui.button>
            </form>
        </div>
    </div>
</div>