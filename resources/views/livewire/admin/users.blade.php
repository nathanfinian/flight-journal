<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           Users
        </div>
        <div class="flex items-center">
            <x-ui.button
                wire:navigate.hover
                variant="outline"
                icon="user-plus"
                size="sm"
                :href="route('admin.users.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>
     <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('admin.index')">Admin</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Users</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <div class="mt-4">
        {{ $this->table }}
    </div>
</div>
