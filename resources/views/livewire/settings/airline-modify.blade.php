<div class="mx-auto max-w-2xl space-y-20 mt-20">
  <div>
    <x-ui.heading level="h1" size="xl">Modify Airline</x-ui.heading>
    <x-ui.text class="opacity-50">Update data kelengkapan maskapai di sini</x-ui.text>

    <x-ui.separator class="my-2"/>
    <x-ui.breadcrumbs>
      <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
      <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.airline')">Airline List</x-ui.breadcrumbs.item>
      <x-ui.breadcrumbs.item>Modify Airline</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <div class="grow">
      <div class="w-full max-w-xl mx-auto space-y-4">
        <form
          wire:submit="saveChanges"
          class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
        >
            @csrf

            {{-- Airline Name --}}
            <x-ui.field required>
                <x-ui.label>Airline Name</x-ui.label>
                <x-ui.input
                wire:model.defer="name"
                maxlength="80"
                placeholder="Garuda Indonesia"
                clearable
                />
                <x-ui.error name="name" />
            </x-ui.field>

            {{-- IATA Code (2 letters, uppercase, optional) --}}
            <x-ui.field>
                <x-ui.label>IATA Code</x-ui.label>
                <x-ui.input
                wire:model.lazy="iata_code"
                maxlength="2"
                placeholder="GA"
                x-on:input="$el.value=$el.value.toUpperCase()"
                clearable
                />
                <x-ui.text size="xs" class="opacity-60 mt-1">2 huruf, opsional.</x-ui.text>
                <x-ui.error name="iata_code" />
            </x-ui.field>

            {{-- ICAO Code (3 letters, uppercase, optional) --}}
            <x-ui.field>
                <x-ui.label>ICAO Code</x-ui.label>
                <x-ui.input
                wire:model.lazy="icao_code"
                maxlength="3"
                placeholder="GIA"
                x-on:input="$el.value=$el.value.toUpperCase()"
                clearable
                />
                <x-ui.text size="xs" class="opacity-60 mt-1">3 huruf, opsional.</x-ui.text>
                <x-ui.error name="icao_code" />
            </x-ui.field>

            {{-- Callsign (optional) --}}
            <x-ui.field>
                <x-ui.label>Callsign</x-ui.label>
                <x-ui.input
                wire:model.defer="callsign"
                maxlength="30"
                placeholder="GARUDA"
                x-on:input="$el.value=$el.value.toUpperCase()"
                clearable
                />
                <x-ui.error name="callsign" />
            </x-ui.field>

            {{-- Country (optional) --}}
            <x-ui.field required>
                <x-ui.label>Country</x-ui.label>
                <x-ui.select 
                    placeholder="Cari negara..."
                    icon="map-pin"
                    searchable
                    wire:model="country">
                        <x-ui.select.option value="IDN">Indonesia</x-ui.select.option>
                        <x-ui.select.option value="MYS">Malaysia</x-ui.select.option>
                        <x-ui.select.option value="SGP">Singapore</x-ui.select.option>
                </x-ui.select>
                <x-ui.error name="country" />
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
    heading="Delete Airline"
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
