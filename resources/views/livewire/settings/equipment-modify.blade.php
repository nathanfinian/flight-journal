<div class="mx-auto max-w-2xl space-y-20 mt-20">
  <div>
    <x-ui.heading level="h1" size="xl">Modify Equipment</x-ui.heading>
    <x-ui.text class="opacity-50">Add or update aircraft equipment data here</x-ui.text>

    <x-ui.separator class="my-2"/>
    <x-ui.breadcrumbs>
      <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
      <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.equipment')">Equipment List</x-ui.breadcrumbs.item>
      <x-ui.breadcrumbs.item>Modify Equipment</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <div class="grow">
      <div class="w-full max-w-xl mx-auto space-y-4">
        <form
          wire:submit="saveChanges"
          class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
        >
          @csrf

          {{-- Airline Type --}}
          <x-ui.field required>
            <x-ui.label>Airline Type</x-ui.label>
            <x-ui.select 
              placeholder="Select airline"
              icon="ps:notebook"
              wire:model="airline_id"
              searchable
            >
              @foreach($airlines as $airline)
                <x-ui.select.option value="{{ $airline->id }}">
                  {{ $airline->name }}
                </x-ui.select.option>
              @endforeach
            </x-ui.select>
            <x-ui.error name="airline_id" />
          </x-ui.field>

          {{-- Registration --}}
          <x-ui.field required>
            <x-ui.label>Registration</x-ui.label>
            <x-ui.input
              wire:model.defer="registration"
              x-mask="**-****"
              maxlength="8"
              placeholder="PK-LAO"
              x-on:input="$el.value=$el.value.toUpperCase()"
              clearable
            />
            <x-ui.error name="registration" />
          </x-ui.field>

           {{-- Aircraft Type --}}
          <x-ui.field required>
            <x-ui.label>Aircraft Type</x-ui.label>
            <x-ui.select 
              placeholder="Select aircraft type..."
              icon="ps:airplane"
              wire:model="aircraft_id"
              searchable
            >
              @foreach($aircrafts as $aircraft)
                <x-ui.select.option icon="ps:airplane" value="{{ $aircraft->id }}">
                  {{ $aircraft->type_name }}
                </x-ui.select.option>
              @endforeach
            </x-ui.select>
            <x-ui.error name="aircraft_id" />
          </x-ui.field>

          {{-- Status --}}
          <x-ui.field required>
            <x-ui.label>Status</x-ui.label>
            <x-ui.select icon="check-circle" wire:model="status">
              <x-ui.select.option value="ACTIVE" icon="check-circle">Active</x-ui.select.option>
              <x-ui.select.option value="RETIRED" icon="x-circle">Retired</x-ui.select.option>
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
      heading="Delete Equipment"
      description="Are you sure you want to delete this equipment?"
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
