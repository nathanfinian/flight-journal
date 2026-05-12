<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>Inventory Item</x-ui.heading>
        <x-ui.text class="opacity-50">Kelola barang inventory dan stok awal per cabang.</x-ui.text>

        <x-ui.separator class="my-2"/>

        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('dashboard')">Dashboard</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('gseitems')">Inventory Items</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>{{ $isEdit ? 'Modify Item' : 'Create Item' }}</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <form
                wire:submit="saveChanges"
                class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
            >
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-4">
                        <x-ui.field required>
                            <x-ui.label>Code</x-ui.label>
                            <x-ui.input
                                wire:model.defer="code"
                                maxlength="100"
                                placeholder="ITEM-001"
                            />
                            <x-ui.error name="code" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-4">
                        <x-ui.field required>
                            <x-ui.label>Name</x-ui.label>
                            <x-ui.input
                                wire:model.defer="name"
                                maxlength="255"
                                placeholder="Nama barang"
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
                            </x-ui.select>
                            <x-ui.error name="status" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-4">
                        <x-ui.field required>
                            <x-ui.label>Sub Category</x-ui.label>
                            <x-ui.select
                                searchable
                                placeholder="Select sub category..."
                                wire:model.live="sub_category_id"
                            >
                                @foreach($subCategories as $subCategory)
                                    <x-ui.select.option value="{{ $subCategory['id'] }}">
                                        {{ $subCategory['category'] }} - {{ $subCategory['name'] }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="sub_category_id" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-4">
                        <x-ui.field required>
                            <x-ui.label>Unit</x-ui.label>
                            <x-ui.select
                                searchable
                                placeholder="Select unit..."
                                wire:model.live="unit_id"
                            >
                                @foreach($units as $unit)
                                    <x-ui.select.option value="{{ $unit['id'] }}">
                                        {{ $unit['name'] }} ({{ $unit['symbol'] }})
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="unit_id" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-4">
                        <x-ui.field required>
                            <x-ui.label>Minimum Stock</x-ui.label>
                            <x-ui.input
                                type="number"
                                min="0"
                                step="1"
                                wire:model.defer="minimum_stock"
                                placeholder="0"
                            />
                            <x-ui.error name="minimum_stock" />
                        </x-ui.field>
                    </div>
                </div>

                <div class="rounded-lg border border-black/10 dark:border-white/10">
                    <div class="flex items-center justify-between border-b border-black/10 px-4 py-3 dark:border-white/10">
                        <div>
                            <x-ui.text class="font-medium">Stock By Branch</x-ui.text>
                            <x-ui.text class="opacity-60 text-sm">Satu cabang hanya boleh memiliki satu baris stok untuk barang ini.</x-ui.text>
                        </div>
                        <x-ui.button type="button" size="sm" variant="outline" icon="plus" wire:click="addStock">
                            Add
                        </x-ui.button>
                    </div>

                    <div class="overflow-visible">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 dark:bg-neutral-800/60">
                                <tr>
                                    <th class="px-4 py-3 text-left">Branch</th>
                                    <th class="px-4 py-3 text-left">Starting Quantity</th>
                                    <th class="px-4 py-3 text-left"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/10 dark:divide-white/10">
                                @foreach ($stocks as $index => $stock)
                                    <tr class="{{ ($stock['delete'] ?? false) ? 'opacity-50' : '' }}">
                                        <td class="px-4 py-3">
                                            <x-ui.field required>
                                                <x-ui.select
                                                    searchable
                                                    position="bottom-start"
                                                    placeholder="Select branch..."
                                                    wire:model.live="stocks.{{ $index }}.branch_id"
                                                    :disabled="$stock['delete'] ?? false"
                                                >
                                                    @foreach($branches as $branch)
                                                        <x-ui.select.option value="{{ $branch['id'] }}">
                                                            {{ $branch['name'] }}
                                                        </x-ui.select.option>
                                                    @endforeach
                                                </x-ui.select>
                                                <x-ui.error name="stocks.{{ $index }}.branch_id" />
                                            </x-ui.field>
                                        </td>
                                        <td class="px-4 py-3">
                                            <x-ui.input
                                                type="number"
                                                min="0"
                                                step="1"
                                                wire:model.defer="stocks.{{ $index }}.quantity"
                                                :disabled="$stock['delete'] ?? false"
                                            />
                                            <x-ui.error name="stocks.{{ $index }}.quantity" />
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if ($stock['delete'] ?? false)
                                                <x-ui.button type="button" size="sm" variant="outline" wire:click="restoreStock({{ $index }})">
                                                    Restore
                                                </x-ui.button>
                                            @else
                                                <x-ui.button type="button" size="sm" variant="ghost" wire:click="removeStock({{ $index }})">
                                                    <x-ui.icon name="ps:trash" variant="thin" class="text-red-600 dark:text-red-500"/>
                                                </x-ui.button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3">
                        <x-ui.error name="stocks" />
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
        heading="Delete Item"
        description="Yakin ingin menghapus barang ini?"
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
