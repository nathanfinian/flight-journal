<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
            Inventory Transaction History
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="ps:arrows-left-right"
                :href="route('gsetransactions')"
            >
                Transaksi
            </x-ui.button>
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>

    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('dashboard')">Dashboard</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>History</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <div class="relative z-20 mt-4 overflow-visible rounded-lg border border-black/10 bg-white px-3 py-2 dark:border-white/10 dark:bg-neutral-900">
        <div class="flex min-w-max flex-nowrap items-end gap-3">
        <div class="w-48 shrink-0">
            <x-ui.field>
                <x-ui.label>Branch</x-ui.label>
                <x-ui.select
                    searchable
                    size="sm"
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

        <div class="w-60 shrink-0">
            <x-ui.field>
                <x-ui.label>Sub Category</x-ui.label>
                <x-ui.select
                    searchable
                    size="sm"
                    position="bottom-start"
                    placeholder="All sub categories"
                    wire:model.live="sub_category_id"
                >
                    <x-ui.select.option value="">All sub categories</x-ui.select.option>
                    @foreach ($subCategories as $subCategory)
                        <x-ui.select.option value="{{ $subCategory->sub_category_id }}">
                            {{ $subCategory->category?->category_name ?? '-' }} - {{ $subCategory->sub_category_name }}
                        </x-ui.select.option>
                    @endforeach
                </x-ui.select>
            </x-ui.field>
        </div>

        <div class="w-40 shrink-0">
            <x-ui.field>
                <x-ui.label>Date From</x-ui.label>
                <x-ui.input
                    size="sm"
                    type="date"
                    wire:model.live="dateFrom"
                    class="dark:scheme-dark dark:[&::-webkit-calendar-picker-indicator]:invert"
                />
            </x-ui.field>
        </div>

        <div class="w-40 shrink-0">
            <x-ui.field>
                <x-ui.label>Date To</x-ui.label>
                <x-ui.input
                    size="sm"
                    type="date"
                    wire:model.live="dateTo"
                    class="dark:scheme-dark dark:[&::-webkit-calendar-picker-indicator]:invert"
                />
            </x-ui.field>
        </div>

        <div class="shrink-0">
            <x-ui.button type="button" size="sm" variant="outline" wire:click="resetFilters">
                Reset Filters
            </x-ui.button>
        </div>
        </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-xl border border-black/10 shadow-lg dark:border-white/10">
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse text-sm">
                <thead class="bg-gray-200 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Branch</th>
                        <th class="px-4 py-3 text-left">Sub Category</th>
                        <th class="px-4 py-3 text-left">Item</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-right">Qty</th>
                        <th class="px-4 py-3 text-left">Equipment</th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                    @forelse ($movements as $movement)
                        @php
                            $item = $movement->item;
                            $unitLabel = $item?->unit?->unit_symbol ?: $item?->unit?->unit_name;
                            $num = method_exists($movements, 'firstItem') && $movements->firstItem()
                                ? $movements->firstItem() + $loop->index
                                : $loop->iteration;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/60">
                            <td class="px-4 py-3">{{ $num }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $movement->movement_date?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $movement->branch?->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div>{{ $item?->subCategory?->sub_category_name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item?->subCategory?->category?->category_name ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $item?->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'inline-flex rounded-full px-2 py-0.5 text-xs font-medium',
                                    'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200' => $movement->movement_type === 'INPUT',
                                    'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-200' => $movement->movement_type === 'OUTPUT',
                                ])>
                                    {{ $movement->movement_type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">{{ number_format((int) $movement->quantity) }} {{ $unitLabel }}</td>
                            <td class="px-4 py-3">
                                {{ $movement->gseEquipment ? $movement->gseEquipment->equipment_code . ' - ' . $movement->gseEquipment->name : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <x-ui.button
                                    wire:navigate.hover
                                    size="sm"
                                    variant="outline"
                                    :href="route('gsetransactions.edit', $movement->movement_id)"
                                >
                                    Edit
                                </x-ui.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-6 text-center text-gray-500 dark:text-neutral-400">
                                No transaction history found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 dark:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700">
        {{ $movements->links() }}
    </div>
</div>
