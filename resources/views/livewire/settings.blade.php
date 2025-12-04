<div>
    <x-ui.heading level="h1" size="xl">
        Settings Page
    </x-ui.heading>
    <x-ui.separator class="my-2"/>

    <!-- Page content -->
    <div class="p-6">
        <div class="w-full max-w-xl mx-auto space-y-4">
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.account')"
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <div class="flex">
                        <x-ui.icon name="ps:user" variant="light" class="text-gray-800 dark:text-white mr-2"/>
                        <span>my account</span>
                    </div>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.branch')" 
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <div class="flex">
                        <x-ui.icon name="ps:building-office" variant="light" class="text-gray-800 dark:text-white mr-2"/>
                        <span>daftar cabang</span>
                    </div>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.airport')" 
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <div class="flex">
                        <x-ui.icon name="ps:air-traffic-control" variant="light" class="text-gray-800 dark:text-white mr-2"/>
                        <span>bandara</span>
                    </div>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.flight-type')" 
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <div class="flex">
                        <x-ui.icon name="ps:rows" variant="light" class="text-gray-800 dark:text-white mr-2"/>
                        <span>tipe flight</span>
                    </div>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.airline')"
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <div class="flex">
                        <x-ui.icon name="ps:clipboard-text" variant="light" class="text-gray-800 dark:text-white mr-2"/>
                        <span>daftar airlines</span>
                    </div>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.aircraft')"
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <div class="flex">
                        <x-ui.icon name="ps:airplane" variant="light" class="text-gray-800 dark:text-white mr-2"/>
                        <span>tipe pesawat</span>
                    </div>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.equipment')" 
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <div class="flex">
                        <x-ui.icon name="ps:airplane-tilt" variant="light" class="text-gray-800 dark:text-white mr-2"/>
                        <span>equipment airline</span>
                    </div>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.airport-route')" 
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <div class="flex">
                        <x-ui.icon name="ps:path" variant="light" class="text-gray-800 dark:text-white mr-2"/>
                        <span>rute penerbangan</span>
                    </div>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
        </div>
    </div>
</div>