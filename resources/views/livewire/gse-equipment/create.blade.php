<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>GSE Equipment</x-ui.heading>
        <x-ui.text class="opacity-50">Kelola data equipment GSE.</x-ui.text>

        <x-ui.separator class="my-2"/>

        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('dashboard')">Dashboard</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('gseequipment')">List GSE Equipment</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>{{ $isEdit ? 'Modify GSE Equipment' : 'Create GSE Equipment' }}</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <form
                wire:submit="saveChanges"
                class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
            >
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-4">
                        <x-ui.field required>
                            <x-ui.label>Equipment Code</x-ui.label>
                            <x-ui.input
                                wire:model.defer="equipment_code"
                                maxlength="100"
                                placeholder="PNK-GPU-001"
                            />
                            <x-ui.error name="equipment_code" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-4">
                        <x-ui.field required>
                            <x-ui.label>Name</x-ui.label>
                            <x-ui.input
                                wire:model.defer="name"
                                maxlength="255"
                                placeholder="GPU Unit 01"
                            />
                            <x-ui.error name="name" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-4">
                        <x-ui.field required>
                            <x-ui.label>Status</x-ui.label>
                            <x-ui.select wire:model.live="status">
                                <x-ui.select.option value="ACTIVE">ACTIVE</x-ui.select.option>
                                <x-ui.select.option value="INACTIVE">INACTIVE</x-ui.select.option>
                                <x-ui.select.option value="MAINTENANCE">MAINTENANCE</x-ui.select.option>
                            </x-ui.select>
                            <x-ui.error name="status" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>GSE Type</x-ui.label>
                            <x-ui.select
                                searchable
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
                            <x-ui.label>Branch</x-ui.label>
                            <x-ui.select
                                searchable
                                placeholder="Select branch..."
                                wire:model.live="branch_id"
                            >
                                @foreach($branches as $branch)
                                    <x-ui.select.option value="{{ $branch->id }}">
                                        {{ $branch->name }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="branch_id" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-4">
                        <x-ui.field>
                            <x-ui.label>Serial Number</x-ui.label>
                            <x-ui.input
                                wire:model.defer="serial_number"
                                maxlength="100"
                                placeholder="Serial number"
                            />
                            <x-ui.error name="serial_number" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-4">
                        <x-ui.field>
                            <x-ui.label>Asset Number</x-ui.label>
                            <x-ui.input
                                wire:model.defer="asset_number"
                                maxlength="100"
                                placeholder="Asset number"
                            />
                            <x-ui.error name="asset_number" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-4">
                        <x-ui.field>
                            <x-ui.label>Manufacture Year</x-ui.label>
                            <x-ui.input
                                min="1900"
                                max="{{ now()->year + 1 }}"
                                wire:model.defer="manufacture_year"
                                placeholder="{{ now()->year }}"
                            />
                            <x-ui.error name="manufacture_year" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field>
                            <x-ui.label>Purchase Date</x-ui.label>
                            <x-ui.input
                                type="date"
                                wire:model.defer="purchase_date"
                                class="dark:scheme-dark dark:[&::-webkit-calendar-picker-indicator]:invert"
                            />
                            <x-ui.error name="purchase_date" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field>
                            <x-ui.label>Total Hours Used</x-ui.label>
                            <x-ui.input
                                min="0"
                                wire:model.defer="total_hours_used"
                                placeholder="0"
                            />
                            <x-ui.error name="total_hours_used" />
                        </x-ui.field>
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
        heading="Delete GSE Equipment"
        description="Yakin ingin menghapus equipment GSE ini?"
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
