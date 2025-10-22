<div class="mx-auto max-w-2xl space-y-20 mt-20">
  <div>
    <x-ui.heading level="h1" size="xl">Modify Branch</x-ui.heading>
    <x-ui.text class="opacity-50">Update data kelengkapan cabang di sini</x-ui.text>

    <x-ui.separator class="my-2"/>
    <x-ui.breadcrumbs>
      <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
      <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.branch')">Branch List</x-ui.breadcrumbs.item>
      <x-ui.breadcrumbs.item>Modify Branch</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <div class="grow">
      <div class="w-full max-w-xl mx-auto space-y-4">
        <form
          wire:submit="saveChanges"
          class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
        >
            @csrf

            {{-- Branch Name --}}
            <x-ui.field required>
                <x-ui.label>Branch Name</x-ui.label>
                <x-ui.input
                wire:model.defer="name"
                maxlength="80"
                placeholder="Palangkaraya"
                clearable
                />
                <x-ui.error name="name" />
            </x-ui.field>

            {{-- Status --}}
            <x-ui.field required>
                <x-ui.label>Status</x-ui.label>
                <x-ui.select 
                    placeholder="Pilih status..."
                    icon="flag"
                    wire:model="status">
                        <x-ui.select.option value="ACTIVE" icon="check-circle">Active</x-ui.select.option>
                        <x-ui.select.option value="INACTIVE" icon="x-circle">Inactive</x-ui.select.option>
                </x-ui.select>
                <x-ui.error name="status" />
            </x-ui.field>

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
  </div>

<x-ui.modal 
    id="delete-modal"
    position="center"
    heading="Delete Branch"
    description="Yakin ingin menghapus maskapai ini?"
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
