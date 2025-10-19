<div class="mx-auto max-w-2xl space-y-20 mt-20">
    <div>
        <x-ui.heading level="h1" size="xl">Modify Aircraft</x-ui.heading>
        <x-ui.text class="opacity-50">update data kelengkapan aircraft disini</x-ui.text>

        <x-ui.separator class="my-2"/>
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.aircraft')">Aircraft List</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Modify Aircraft</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <div class="w-full max-w-xl mx-auto space-y-4">
                <form 
                    wire:submit="saveChanges"
                    class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
                >
                @csrf
                    {{-- Type Name --}}
                    <x-ui.field required>
                        <x-ui.label>Type Name</x-ui.label>
                        <x-ui.input
                            wire:model.defer="type_name"
                            maxlength="40"
                            placeholder="ATR 72-600"
                            clearable
                        />
                        <x-ui.error name="type_name" />
                    </x-ui.field>

                    {{-- ICAO Code (up to 4, uppercase) --}}
                    <x-ui.field required>
                        <x-ui.label>ICAO Code</x-ui.label>
                        <x-ui.input
                            wire:model.lazy="icao_code"
                            maxlength="4"
                            placeholder="AT76"
                            x-on:input="$el.value=$el.value.toUpperCase()"
                            clearable
                        />
                        <x-ui.error name="icao_code" />
                    </x-ui.field>

                    {{-- IATA Code (up to 3, uppercase) --}}
                    <x-ui.field>
                        <x-ui.label>IATA Code</x-ui.label>
                        <x-ui.input
                            wire:model.lazy="iata_code"
                            maxlength="3"
                            placeholder="ATR"
                            x-on:input="$el.value=$el.value.toUpperCase()"
                            clearable
                        />
                        <x-ui.error name="iata_code" />
                    </x-ui.field>

                    {{-- Seat Capacity (0–65535) --}}
                    <x-ui.field required>
                        <x-ui.label>Seat Capacity</x-ui.label>
                        <x-ui.input
                            wire:model.defer="seat_capacity"
                            type="number"
                            min="1"
                            max="65535"
                            placeholder="70"
                            clearable
                        />
                        <x-ui.error name="seat_capacity" />
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
    heading="Delete Aircraft"
    description="Yakin ingin menghapus aircraft ini?"
>
    <div class="mt-4 flex justify-end space-x-2">
        <x-ui.button 
            variant="outline" 
            wire:click="$emit('close-modal', 'delete-modal')"
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