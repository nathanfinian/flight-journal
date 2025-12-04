<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           Flight Types
        </div>
        <div class="flex items-center">
            <x-ui.button 
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('settings.flight-type.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>
     <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Flight Type List</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
            <th class="px-4 py-3 text-left">#</th>
            <th class="px-4 py-3 text-left">Name</th>
            <th class="px-4 py-3 text-left">Type Code</th>
            <th class="px-4 py-3 text-left">Dibuat Oleh</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            @forelse ($types as $type)
            <tr 
                class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                wire:click="openEdit({{ $type->id }})"
                >
                <td class="px-4 py-3">
                {{-- Works for both paginate() and get() --}}
                @php
                    $num = method_exists($type, 'firstItem') && $type->firstItem()
                        ? $type->firstItem() + $loop->index   // paginated
                        : $loop->iteration;                        // non-paginated
                @endphp
                {{ $num }}
                </td>
                <td class="px-4 py-3">{{ $type->name }}</td>
                <td class="px-4 py-3">{{ $type->type_code }}</td>
                <td class="px-4 py-3">{{ $type->createdBy->name ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                No Data found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

