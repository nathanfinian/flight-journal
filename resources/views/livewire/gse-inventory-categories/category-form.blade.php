<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>Inventory Category</x-ui.heading>
        <x-ui.text class="opacity-50">Kelola kategori inventory GSE.</x-ui.text>

        <x-ui.separator class="my-2"/>

        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('dashboard')">Dashboard</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('gsecategories')">Inventory Categories</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>{{ $isEdit ? 'Modify Category' : 'Create Category' }}</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <form
                wire:submit="saveChanges"
                class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
            >
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Category Name</x-ui.label>
                            <x-ui.input
                                wire:model.defer="category_name"
                                maxlength="100"
                                placeholder="Spare Part"
                            />
                            <x-ui.error name="category_name" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
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

                <div class="rounded-lg border border-black/10 dark:border-white/10">
                    <div class="flex items-center justify-between border-b border-black/10 px-4 py-3 dark:border-white/10">
                        <div>
                            <x-ui.text class="font-medium">Sub Categories</x-ui.text>
                            <x-ui.text class="opacity-60 text-sm">Sub kategori akan tersimpan pada kategori ini.</x-ui.text>
                        </div>
                        <x-ui.button type="button" size="sm" variant="outline" icon="plus" wire:click="addSubCategory">
                            Add
                        </x-ui.button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 dark:bg-neutral-800/60">
                                <tr>
                                    <th class="px-4 py-3 text-left">Name</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/10 dark:divide-white/10">
                                @foreach ($subCategories as $index => $subCategory)
                                    <tr class="{{ ($subCategory['delete'] ?? false) ? 'opacity-50' : '' }}">
                                        <td class="px-4 py-3">
                                            <x-ui.input
                                                wire:model.defer="subCategories.{{ $index }}.name"
                                                maxlength="100"
                                                placeholder="Nama sub kategori"
                                                :disabled="$subCategory['delete'] ?? false"
                                            />
                                            <x-ui.error name="subCategories.{{ $index }}.name" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <x-ui.select
                                                wire:model.live="subCategories.{{ $index }}.status"
                                                :disabled="$subCategory['delete'] ?? false"
                                            >
                                                <x-ui.select.option value="ACTIVE">ACTIVE</x-ui.select.option>
                                                <x-ui.select.option value="INACTIVE">INACTIVE</x-ui.select.option>
                                            </x-ui.select>
                                            <x-ui.error name="subCategories.{{ $index }}.status" />
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if ($subCategory['delete'] ?? false)
                                                <x-ui.button type="button" size="sm" variant="outline" wire:click="restoreSubCategory({{ $index }})">
                                                    Restore
                                                </x-ui.button>
                                            @else
                                                <x-ui.button type="button" size="sm" variant="ghost" wire:click="removeSubCategory({{ $index }})">
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
                        <x-ui.error name="subCategories" />
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
        heading="Delete Category"
        description="Yakin ingin menghapus kategori ini?"
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
