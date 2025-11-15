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

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
            <th class="px-4 py-3 text-left">#</th>
            <th class="px-4 py-3 text-left">Username</th>
            <th class="px-4 py-3 text-left">Name</th>
            <th class="px-4 py-3 text-left">Role</th>
            <th class="px-4 py-3 text-left">Branch</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            @forelse ($users as $user)
            <tr 
                class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                wire:click="openEdit({{ $user->id }})"
                >
                <td class="px-4 py-3">
                @php
                    $num = method_exists($user, 'firstItem') && $user->firstItem()
                        ? $user->firstItem() + $loop->index   // paginated
                        : $loop->iteration;                        // non-paginated
                @endphp
                {{ $num }}
                </td>
                <td class="px-4 py-3">{{ $user->username }}</td>
                <td class="px-4 py-3">{{ $user->name }}</td>
                <td class="px-4 py-3">{{ $user->role->label ?? 'Empty'}}</td>
                <td class="px-4 py-3">{{ $user->branch->name ?? 'Empty' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                No user found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4 dark:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700">
        {{ $users->links() }}
    </div>
</div>

