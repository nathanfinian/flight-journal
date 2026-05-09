<div class="space-y-10">
    <div>
        <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
            <div class="flex items-center">
                Inventory Categories
            </div>
            <div class="flex items-center">
                <x-ui.button
                    wire:navigate.hover
                    size="sm"
                    variant="outline"
                    icon="plus"
                    :href="route('gsecategories.create')"
                >
                    Category
                </x-ui.button>
            </div>
        </x-ui.heading>

        <x-ui.separator class="my-2"/>

        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('dashboard')">Dashboard</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Inventory Categories</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>
    </div>

    <div>
        <x-ui.heading level="h2" size="lg">Categories</x-ui.heading>

        <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
            <thead class="bg-gray-200 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Sub Categories</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
                @forelse ($categories as $category)
                    <tr
                        class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                        wire:click="openCategoryEdit({{ $category->category_id }})"
                    >
                        <td class="px-4 py-3">
                            @php
                                $num = method_exists($categories, 'firstItem') && $categories->firstItem()
                                    ? $categories->firstItem() + $loop->index
                                    : $loop->iteration;
                            @endphp
                            {{ $num }}
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $category->category_name }}</td>
                        <td class="px-4 py-3">{{ $category->status }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse ($category->subCategories as $subCategory)
                                    <x-ui.badge size="sm" variant="outline" color="indigo" pill>
                                        {{ $subCategory->sub_category_name }}
                                    </x-ui.badge>
                                @empty
                                    <span class="text-gray-500">-</span>
                                @endforelse
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                            No category found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4 dark:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700">
            {{ $categories->links() }}
        </div>
    </div>
</div>
