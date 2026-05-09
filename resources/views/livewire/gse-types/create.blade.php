<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>GSE Type</x-ui.heading>
        <x-ui.text class="opacity-50">Tipe-tipe GSE</x-ui.text>

        <x-ui.separator class="my-2"/>

        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('dashboard')">Dashboard</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('gsetype')">List GSE Type</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>{{ $isEdit ? 'Modify GSE Type' : 'Create GSE Type' }}</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <form
                wire:submit="saveChanges"
                class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
            >
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Type Name</x-ui.label>
                            <x-ui.input
                                wire:model.defer="type_name"
                                maxlength="255"
                                placeholder="GPU"
                            />
                            <x-ui.error name="type_name" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12">
                        <x-ui.field>
                            <x-ui.label>Description</x-ui.label>
                            <x-ui.textarea
                                wire:model.defer="description"
                                rows="4"
                                placeholder="Deskripsi tipe GSE"
                            />
                            <x-ui.error name="description" />
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
        heading="Delete GSE Type"
        description="Yakin ingin menghapus tipe GSE ini?"
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
