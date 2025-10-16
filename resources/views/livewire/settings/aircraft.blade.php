<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           Aircraft List
        </div>
        <div class="flex items-center">
            <x-ui.button 
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('settings.aircraft.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>
     <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings')">Settings</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Aircraft List</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

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