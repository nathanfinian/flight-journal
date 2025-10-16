<div>
    <x-ui.layout.header class="dark:border-b-neutral-800 dark:bg-neutral-900">
        <h1 class="dark:border-b-neutral-800 dark:bg-neutral-900">Settings Page</h1>
    </x-ui.layout.header>
    
    <!-- Page content -->
    <div class="p-6 mx-auto space-y-10">
        <x-ui.link 
            wire:navigate.hover
            :href="route('settings.aircraft')"
            variant="ghost"
        >
            <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                <span>Aircraft List</span>
                <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
            </x-ui.card>
        </x-ui.link>
        <x-ui.link 
            wire:navigate.hover
            :href="route('flight-journal')" 
            variant="ghost"
        >
            <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                <span>Equipment List</span>
                <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
            </x-ui.card>
        </x-ui.link>
        <x-ui.link 
            wire:navigate.hover
            :href="route('flight-journal')" 
            variant="ghost"
        >
            <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                <span>Airport List</span>
                <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
            </x-ui.card>
        </x-ui.link>
        <x-ui.link 
            wire:navigate.hover
            :href="route('flight-journal')" 
            variant="ghost"
        >
            <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                <span>Routes List</span>
                <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
            </x-ui.card>
        </x-ui.link>
        <x-ui.link 
            wire:navigate.hover
            :href="route('flight-journal')" 
            variant="ghost"
        >
            <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                <span>Regular Flights List</span>
                <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
            </x-ui.card>
        </x-ui.link>
    </div>
</div>