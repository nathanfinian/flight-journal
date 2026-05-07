<div>
    <x-ui.heading level="h1" size="xl" class="flex items-center justify-between">
        <div class="flex items-center">
            GSE Invoice List
        </div>
        <div class="flex items-center">
            <x-ui.button
                wire:navigate.hover
                size="sm"
                variant="outline"
                icon="plus"
                :href="route('invoicegse.create')"
            />
        </div>
    </x-ui.heading>

    <x-ui.separator class="my-2"/>

    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.item wire:navigate.hover :href="route('dashboard')">Dashboard</x-ui.breadcrumbs.item>
        <x-ui.breadcrumbs.item>GSE Invoice List</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <table class="border border-collapse min-w-full text-sm rounded-xl overflow-hidden mt-4 shadow-lg">
        <thead class="bg-gray-200 dark:bg-neutral-800">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Invoice Number</th>
                <th class="px-4 py-3 text-left">Service</th>
                <th class="px-4 py-3 text-left">Date Range</th>
                <th class="px-4 py-3 text-left">Recaps</th>
                <th class="px-4 py-3 text-left">Total</th>
                <th class="px-4 py-3 text-left">Export</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-400 dark:divide-neutral-800">
            @forelse ($invoices as $invoice)
                @php
                    $recapServices = $invoice->recaps
                        ->pluck('gseType.service_name')
                        ->filter()
                        ->unique()
                        ->values();
                    $serviceDisplay = $recapServices->count() > 1
                        ? $recapServices->implode(' + ')
                        : ($invoice->gseType?->service_name ?? '-');
                @endphp
                <tr
                    class="hover:bg-gray-50 dark:hover:bg-neutral-800/60 cursor-pointer"
                    wire:click="openEdit({{ $invoice->id }})"
                >
                    <td class="px-4 py-3">
                        @php
                            $num = method_exists($invoices, 'firstItem') && $invoices->firstItem()
                                ? $invoices->firstItem() + $loop->index
                                : $loop->iteration;
                        @endphp
                        {{ $num }}
                    </td>
                    <td class="px-4 py-3">{{ $invoice->invoice_number }}</td>
                    <td class="px-4 py-3">{{ $serviceDisplay }}</td>
                    <td class="px-4 py-3">{{ $invoice->dateRange ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $invoice->recaps_count }}</td>
                    <td class="px-4 py-3">Rp. {{ number_format((float) ($invoice->invoice_recaps_sum_amount ?? 0), 2, ',', '.') }}</td>
                    <td class="px-4 py-3" x-on:click.stop>
                        <x-ui.button
                            as="a"
                            size="sm"
                            variant="outline"
                            icon="ps:microsoft-excel-logo"
                            href="{{ route('invoicegse.export-recap', $invoice) }}"
                        >
                            Excel
                        </x-ui.button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                        No GSE invoice found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 dark:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700">
        {{ $invoices->links() }}
    </div>
</div>
