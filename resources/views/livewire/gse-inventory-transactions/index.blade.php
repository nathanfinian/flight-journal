<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
            Inventory Transactions
        </div>
        <div class="flex items-center">
            <x-ui.button
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('gsetransactions.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>

    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('dashboard')">Dashboard</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>Inventory Transactions</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <div class="mt-4 grid grid-cols-12 gap-4 rounded-lg border border-black/10 bg-white p-4 dark:border-white/10 dark:bg-neutral-900">
        <div class="col-span-3">
            <x-ui.field>
                <x-ui.label>Category</x-ui.label>
                <x-ui.select
                    searchable
                    position="bottom-start"
                    placeholder="All categories"
                    wire:model.live="category_id"
                >
                    <x-ui.select.option value="">All categories</x-ui.select.option>
                    @foreach ($categories as $category)
                        <x-ui.select.option value="{{ $category->category_id }}">
                            {{ $category->category_name }}
                        </x-ui.select.option>
                    @endforeach
                </x-ui.select>
            </x-ui.field>
        </div>

        <div class="col-span-3">
            <x-ui.field>
                <x-ui.label>Sub Category</x-ui.label>
                <x-ui.select
                    searchable
                    position="bottom-start"
                    placeholder="All sub categories"
                    wire:model.live="sub_category_id"
                >
                    <x-ui.select.option value="">All sub categories</x-ui.select.option>
                    @foreach ($subCategories as $subCategory)
                        <x-ui.select.option value="{{ $subCategory->sub_category_id }}">
                            {{ $subCategory->sub_category_name }}
                        </x-ui.select.option>
                    @endforeach
                </x-ui.select>
            </x-ui.field>
        </div>

        <div class="col-span-3">
            <x-ui.field>
                <x-ui.label>Branch</x-ui.label>
                <x-ui.select
                    searchable
                    position="bottom-start"
                    placeholder="All branches"
                    wire:model.live="branch_id"
                >
                    <x-ui.select.option value="">All branches</x-ui.select.option>
                    @foreach ($branches as $branch)
                        <x-ui.select.option value="{{ $branch->id }}">
                            {{ $branch->name }}
                        </x-ui.select.option>
                    @endforeach
                </x-ui.select>
            </x-ui.field>
        </div>

        <div class="col-span-3">
            <x-ui.field>
                <x-ui.label>Item Name</x-ui.label>
                <x-ui.input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search item..."
                />
            </x-ui.field>
        </div>

        <div class="col-span-12 flex justify-end">
            <x-ui.button type="button" variant="outline" wire:click="resetFilters">
                Reset Filters
            </x-ui.button>
        </div>
    </div>

    <div class="mt-4 rounded-xl border border-black/10 shadow-lg dark:border-white/10">
        <div>
            <div
                class="grid bg-gray-200 text-sm font-medium dark:bg-neutral-800"
                style="grid-template-columns: 56px minmax(140px,1fr) minmax(140px,1fr) minmax(180px,1.4fr) minmax(260px,2fr);"
            >
                <div class="px-4 py-3">#</div>
                <div class="px-4 py-3">Category</div>
                <div class="px-4 py-3">Sub Category</div>
                <div class="px-4 py-3">Item Name</div>
                <div class="px-4 py-3">Stock By Branch</div>
            </div>

            <x-ui.accordion>
                @forelse ($items as $item)
                    @php
                        $num = method_exists($items, 'firstItem') && $items->firstItem()
                            ? $items->firstItem() + $loop->index
                            : $loop->iteration;
                        $stockByBranch = $item->stocks->keyBy('branch_id');
                        $stockBranches = $visibleBranches->filter(fn ($branch) => $stockByBranch->has($branch->id));
                        $unitLabel = $item->unit?->unit_symbol ?: $item->unit?->unit_name;
                    @endphp

                    <x-ui.accordion.item>
                        <x-ui.accordion.trigger class="px-0 py-0 text-sm">
                            <div
                                class="grid w-full items-center hover:bg-gray-50 dark:hover:bg-neutral-800/60"
                                style="grid-template-columns: 56px minmax(140px,1fr) minmax(140px,1fr) minmax(180px,1.4fr) minmax(260px,2fr);"
                            >
                                <div class="px-4 py-3">{{ $num }}</div>
                                <div class="px-4 py-3">{{ $item->subCategory?->category?->category_name ?? '-' }}</div>
                                <div class="px-4 py-3">{{ $item->subCategory?->sub_category_name ?? '-' }}</div>
                                <div class="px-4 py-3 font-medium">{{ $item->name }}</div>
                                <div class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse ($stockBranches as $branch)
                                            <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                                                {{ $branch->name }}: {{ number_format((int) ($stockByBranch->get($branch->id)?->quantity ?? 0)) }} {{ $unitLabel }}
                                            </span>
                                        @empty
                                            <span class="text-gray-500">-</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </x-ui.accordion.trigger>

                        <x-ui.accordion.content>
                            <div class="rounded-lg border border-black/10 bg-white dark:border-white/10 dark:bg-neutral-900">
                                <div class="border-b border-black/10 px-4 py-3 text-sm font-medium dark:border-white/10">
                                    Last 5 Transactions
                                </div>
                                <div class="divide-y divide-black/10 dark:divide-white/10">
                                    @forelse (($recentMovements->get($item->item_id) ?? collect()) as $movement)
                                        <div class="grid grid-cols-12 items-center gap-3 px-4 py-3 text-sm">
                                            <div class="col-span-2">
                                                {{ $movement->movement_date?->format('d M Y H:i') ?? '-' }}
                                            </div>
                                            <div class="col-span-2">
                                                <span @class([
                                                    'inline-flex rounded-full px-2 py-0.5 text-xs font-medium',
                                                    'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200' => $movement->movement_type === 'INPUT',
                                                    'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-200' => $movement->movement_type === 'OUTPUT',
                                                ])>
                                                    {{ $movement->movement_type }}
                                                </span>
                                            </div>
                                            <div class="col-span-2">{{ $movement->branch?->name ?? '-' }}</div>
                                            <div class="col-span-2">{{ number_format((int) $movement->quantity) }}</div>
                                            <div class="col-span-2">{{ $movement->reference_no ?? '-' }}</div>
                                            <div class="col-span-2 text-right">
                                                <x-ui.button
                                                    wire:navigate.hover
                                                    size="sm"
                                                    variant="outline"
                                                    :href="route('gsetransactions.edit', $movement->movement_id)"
                                                >
                                                    Edit
                                                </x-ui.button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-4 py-6 text-center text-sm text-gray-500">
                                            No transaction found.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </x-ui.accordion.content>
                    </x-ui.accordion.item>
                @empty
                    <div class="px-4 py-6 text-center text-sm text-gray-500">
                        No item found.
                    </div>
                @endforelse
            </x-ui.accordion>
        </div>
    </div>

    <div class="mt-4 dark:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700">
        {{ $items->links() }}
    </div>
</div>
