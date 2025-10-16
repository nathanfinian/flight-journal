<div>
    <x-ui.layout.header class="flex justify-between dark:border-b-neutral-800 dark:bg-neutral-900">
        <div class="flex items-center">
            <x-ui.button
                wire:navigate.hover
                size="sm"
                variant="ghost"
                icon="arrow-left"
                :href="route('settings')"
                class="mr-6"
            />
            Aircraft List
    </div>
        <div class="flex items-center">
            <x-ui.button 
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('settings.aircraft-modify')"
            />
        </div>
    </x-ui.layout.header>

    <table class="border-collapse border border-gray-400 w-full mt-4">
        <thead>
            <tr>
            <th class="border border-gray-300 ...">State</th>
            <th class="border border-gray-300 ...">City</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <td class="border border-gray-300 ...">Indiana</td>
            <td class="border border-gray-300 ...">Indianapolis</td>
            </tr>
            <tr>
            <td class="border border-gray-300 ...">Ohio</td>
            <td class="border border-gray-300 ...">Columbus</td>
            </tr>
            <tr>
            <td class="border border-gray-300 ...">Michigan</td>
            <td class="border border-gray-300 ...">Detroit</td>
            </tr>
        </tbody>
    </table>
</div>