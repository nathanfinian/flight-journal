<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           Equipment List
        </div>
        <div class="flex items-center">
            <x-ui.button 
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('settings.equipment.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>

    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Equipment List</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <div class="mt-4">
        {{ $this->table }}
    </div>
</div>
