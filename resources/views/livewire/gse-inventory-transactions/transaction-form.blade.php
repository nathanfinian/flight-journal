<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>Inventory Transaction</x-ui.heading>
        <x-ui.text class="opacity-50">Kelola transaksi masuk dan keluar stok inventory.</x-ui.text>

        <x-ui.separator class="my-2"/>

        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('dashboard')">Dashboard</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('gsetransactions')">Inventory Transactions</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>{{ $isEdit ? 'Modify Transaction' : 'Create Transaction' }}</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <form
                wire:submit="saveChanges"
                class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
            >
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Item</x-ui.label>
                            <x-ui.select
                                searchable
                                placeholder="Select item..."
                                wire:model.live="item_id"
                            >
                                @foreach($items as $item)
                                    <x-ui.select.option value="{{ $item['id'] }}">
                                        {{ $item['label'] }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="item_id" />
                        </x-ui.field>
                    </div>

                    <div class="col-span-3">
                        <x-ui.field required>
                            <x-ui.label>Branch</x-ui.label>
                            <x-ui.select
                                searchable
                                position="bottom-start"
                                placeholder="Select branch..."
                                wire:model.live="branch_id"
                            >
                                @foreach($branches as $branch)
                                    <x-ui.select.option value="{{ $branch['id'] }}">
                                        {{ $branch['name'] }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="branch_id" />
                        </x-ui.field>
                    </div>

                    <div class="col-span-3">
                        <x-ui.field required>
                            <x-ui.label>Type</x-ui.label>
                            <x-ui.select wire:model.live="movement_type">
                                <x-ui.select.option value="INPUT">INPUT</x-ui.select.option>
                                <x-ui.select.option value="OUTPUT">OUTPUT</x-ui.select.option>
                            </x-ui.select>
                            <x-ui.error name="movement_type" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-3">
                        <x-ui.field required>
                            <x-ui.label>Quantity</x-ui.label>
                            <x-ui.input
                                type="number"
                                min="1"
                                step="1"
                                wire:model.defer="quantity"
                                placeholder="1"
                            />
                            <x-ui.error name="quantity" />
                        </x-ui.field>
                    </div>

                    <div class="col-span-3">
                        <x-ui.field required>
                            <x-ui.label>Date</x-ui.label>
                            <x-ui.input
                                type="datetime-local"
                                wire:model.defer="movement_date"
                            />
                            <x-ui.error name="movement_date" />
                        </x-ui.field>
                    </div>

                    <div class="col-span-3">
                        <x-ui.field>
                            <x-ui.label>Reference No</x-ui.label>
                            <x-ui.input
                                wire:model.defer="reference_no"
                                maxlength="100"
                                placeholder="REF-001"
                            />
                            <x-ui.error name="reference_no" />
                        </x-ui.field>
                    </div>

                    <div class="col-span-3">
                        <x-ui.field>
                            <x-ui.label>GSE Equipment</x-ui.label>
                            <x-ui.select
                                searchable
                                position="bottom-start"
                                placeholder="Select equipment..."
                                wire:model.live="gse_equipment_id"
                            >
                                <x-ui.select.option value="">
                                    -
                                </x-ui.select.option>
                                @foreach($equipment as $row)
                                    <x-ui.select.option value="{{ $row['id'] }}">
                                        {{ $row['label'] }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="gse_equipment_id" />
                        </x-ui.field>
                    </div>
                </div>

                <div>
                    <x-ui.field>
                        <x-ui.label>Notes</x-ui.label>
                        <x-ui.textarea
                            wire:model.defer="notes"
                            rows="3"
                            placeholder="Catatan transaksi"
                        />
                        <x-ui.error name="notes" />
                    </x-ui.field>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <x-ui.button type="submit">Save changes</x-ui.button>
                    @if ($isEdit)
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

    @if ($isEdit)
        <x-ui.modal
            id="delete-modal"
            position="center"
            heading="Delete Transaction"
            description="Are you sure you want to delete this transaction? The stock quantity will be adjusted back."
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
    @endif
</div>
