<div>
    <x-ui.heading level="h1" size="xl">
        Admin Page
    </x-ui.heading>
    <x-ui.separator class="my-2"/>

    <!-- Page content -->
    <div class="p-6">
        <div class="w-full max-w-xl mx-auto space-y-4">
            <x-ui.link 
                wire:navigate.hover
                :href="route('admin.users')" 
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <div class="flex">
                        <x-ui.icon name="users" variant="light" class="text-gray-800 dark:text-white mr-2"/>
                        <span>users</span>
                    </div>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('admin.roles')" 
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <div class="flex">
                        <x-ui.icon name="ps:user-square" variant="light" class="text-gray-800 dark:text-white mr-2"/>
                        <span>roles</span>
                    </div>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('admin.branch')" 
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <div class="flex">
                        <x-ui.icon name="ps:graph" variant="light" class="text-gray-800 dark:text-white mr-2"/>
                        <span>branches</span>
                    </div>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
        </div>
    </div>
</div>