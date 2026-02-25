<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
           Branch List
        </div>
        <div class="flex items-center">
            <x-ui.button 
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('settings.branch.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>
     <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('settings.index')">Settings</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Branch List</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
            <th class="px-4 py-3 text-left">#</th>
            <th class="px-4 py-3 text-left">Name</th>
            <th class="px-4 py-3 text-left">Airport</th>
            <th class="px-4 py-3 text-left">Address</th>
            <th class="px-4 py-3 text-left">Phone</th>
            <th class="px-4 py-3 text-left">Email</th>
            <th class="px-4 py-3 text-left">Rekening</th>
            <th class="px-4 py-3 text-left">Status</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            @forelse ($branches as $branch)
            <tr 
                class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                wire:click="openEdit({{ $branch->id }})"
                >
                <td class="px-4 py-3">
                {{-- Works for both paginate() and get() --}}
                @php
                    $num = method_exists($branch, 'firstItem') && $branch->firstItem()
                        ? $branch->firstItem() + $loop->index   // paginated
                        : $loop->iteration;                        // non-paginated
                @endphp
                {{ $num }}
                </td>
                <td class="px-4 py-3">{{ $branch->name }}</td>
                <td class="px-4 py-3">{{ $branch->airport->iata }}</td>
                <td class="px-4 py-3">{{ $branch->address }}</td>
                <td class="px-4 py-3">{{ $branch->phone_number }}</td>
                <td class="px-4 py-3">{{ $branch->email }}</td>
                <td class="px-4 py-3">{{ $branch->account_number ?? '-' }}</td>
                <td class="px-4 py-3">{{ $branch->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                No branch found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

