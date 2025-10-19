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
                    <span>my account</span>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.aircraft')"
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <span>tipe pesawat</span>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.equipment')" 
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <span>daftar equipment airline</span>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.index')" 
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <span>daftar bandara</span>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.index')" 
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <span>daftar rute penerbangan</span>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
            <x-ui.link 
                wire:navigate.hover
                :href="route('settings.index')" 
                variant="ghost"
            >
                <x-ui.card class="flex items-center justify-between mb-4" size="xl">
                    <span>daftar flight reguler</span>
                    <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                </x-ui.card>
            </x-ui.link>
        </div>
    </div>
</div>