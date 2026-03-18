<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>GSE Recap</x-ui.heading>
        <x-ui.text class="opacity-50">Input recap operasional GSE</x-ui.text>

        <x-ui.separator class="my-2"/>

        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('rekapgse')">List GSE Recap</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>GSE Recap</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <form wire:submit="saveChanges" class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
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
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Tanggal Service</x-ui.label>
                            <x-ui.input
                                type="date"
                                wire:model="form.service_date"
                                class="dark:[color-scheme:dark] dark:[&::-webkit-calendar-picker-indicator]:invert"
                            />
                            <x-ui.error name="form.service_date" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>ER Number</x-ui.label>
                            <x-ui.input wire:model.defer="form.er_number" maxlength="255" />
                            <x-ui.error name="form.er_number" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Flight Number</x-ui.label>
                            <x-ui.input wire:model.defer="form.flight_number" maxlength="10" />
                            <x-ui.error name="form.flight_number" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Equipment</x-ui.label>
                            <x-ui.select searchable placeholder="Select equipment..." wire:model.live="form.equipment_id">
                                @foreach($equipments as $equipment)
                                    <x-ui.select.option value="{{ $equipment->id }}">
                                        {{ $equipment->registration }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="form.equipment_id" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Airline</x-ui.label>
                            <x-ui.select placeholder="Auto pilih equipment..." wire:model.live="form.airline_id" disabled>
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
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Cabang</x-ui.label>
                            <x-ui.select placeholder="Select branch..." wire:model.live="form.branch_id">
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
                        <x-ui.field required>
                            <x-ui.label>Operator Name</x-ui.label>
                            <x-ui.input wire:model.defer="form.operator_name" maxlength="255" />
                            <x-ui.error name="form.operator_name" />
                        </x-ui.field>
                    </div>
                </div>

                @if ($currentDetailType === 'gpu')
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-6">
                            <x-ui.field required>
                                <x-ui.label>Start Time</x-ui.label>
                                <x-ui.input type="time" wire:model.defer="form.start_time" />
                                <x-ui.error name="form.start_time" />
                            </x-ui.field>
                        </div>
                        <div class="col-span-6">
                            <x-ui.field required>
                                <x-ui.label>End Time</x-ui.label>
                                <x-ui.input type="time" wire:model.defer="form.end_time" />
                                <x-ui.error name="form.end_time" />
                            </x-ui.field>
                        </div>
                    </div>
                @elseif ($currentDetailType === 'pushback')
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-6">
                            <x-ui.field required>
                                <x-ui.label>Start PS</x-ui.label>
                                <x-ui.input wire:model.defer="form.start_ps" maxlength="20" />
                                <x-ui.error name="form.start_ps" />
                            </x-ui.field>
                        </div>
                        <div class="col-span-6">
                            <x-ui.field required>
                                <x-ui.label>End PS</x-ui.label>
                                <x-ui.input wire:model.defer="form.end_ps" maxlength="20" />
                                <x-ui.error name="form.end_ps" />
                            </x-ui.field>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-6">
                            <x-ui.field required>
                                <x-ui.label>ATA</x-ui.label>
                                <x-ui.input x-mask="99:99" wire:model.defer="form.ata" />
                                <x-ui.error name="form.ata" />
                            </x-ui.field>
                        </div>
                        <div class="col-span-6">
                            <x-ui.field required>
                                <x-ui.label>ATD</x-ui.label>
                                <x-ui.input x-mask="99:99" wire:model.defer="form.atd" />
                                <x-ui.error name="form.atd" />
                            </x-ui.field>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12">
                        <x-ui.field>
                            <x-ui.label>Remarks</x-ui.label>
                            <x-ui.input wire:model.defer="form.remarks" maxlength="255" />
                            <x-ui.error name="form.remarks" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <x-ui.button type="submit">Simpan Recap</x-ui.button>
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
        heading="Delete GSE Recap"
        description="Yakin ingin menghapus recap ini?"
    >
        <div class="mt-4 flex justify-end space-x-2">
            <x-ui.button variant="outline" x-on:click="$data.close();">
                Cancel
            </x-ui.button>
            <x-ui.button variant="danger" wire:click.stop="delete">
                Delete
            </x-ui.button>
        </div>
    </x-ui.modal>
</div>
