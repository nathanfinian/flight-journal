<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>GSE Rates</x-ui.heading>
        <x-ui.text class="opacity-50">pengaturan biaya layanan GSE</x-ui.text>

        <x-ui.separator class="my-2"/>

        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('rategse')">List Rate GSE</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Modify Rate GSE</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <form
                wire:submit="saveChanges"
                class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
            >
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>GSE Type</x-ui.label>
                            <x-ui.select
                                placeholder="Select GSE type..."
                                wire:model.live="gse_type_id"
                            >
                                @foreach($gseTypes as $gseType)
                                    <x-ui.select.option value="{{ $gseType->id }}">
                                        {{ $gseType->type_name }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="gse_type_id" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Charge Type</x-ui.label>
                            <x-ui.select
                                placeholder="Select charge type..."
                                wire:model.live="charge_type"
                            >
                                <x-ui.select.option value="HOURLY">HOURLY</x-ui.select.option>
                                <x-ui.select.option value="PER_HANDLING">PER_HANDLING</x-ui.select.option>
                            </x-ui.select>
                            <x-ui.error name="charge_type" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Effective From</x-ui.label>
                            <x-ui.input
                                type="date"
                                wire:model.defer="effective_from"
                                class="dark:[color-scheme:dark] dark:[&::-webkit-calendar-picker-indicator]:invert"
                            />
                            <x-ui.error name="effective_from" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field>
                            <x-ui.label>Effective To</x-ui.label>
                            <x-ui.input
                                type="date"
                                wire:model.defer="effective_to"
                                class="dark:[color-scheme:dark] dark:[&::-webkit-calendar-picker-indicator]:invert"
                            />
                            <x-ui.error name="effective_to" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Service Rate</x-ui.label>
                            <x-ui.input
                                wire:model.defer="service_rate"
                                x-mask:dynamic="$money($input, ',')"
                                placeholder="0.00"
                            >
                                <x-slot name="prefix">Rp</x-slot>
                            </x-ui.input>
                            <x-ui.error name="service_rate" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                    </div>
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
        heading="Delete GSE Rate"
        description="Yakin ingin menghapus rate GSE ini?"
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
