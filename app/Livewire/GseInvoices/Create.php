<?php

namespace App\Livewire\GseInvoices;

use App\Livewire\Forms\GseInvoiceForm;
use App\Models\Airline;
use App\Models\Branch;
use App\Models\GseRecap;
use App\Models\GseType;
use App\Models\GseTypeRate;
use App\Models\Invoice_gse as GseInvoice;
use App\Traits\GeneratesGseInvoiceNumber;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    use GeneratesGseInvoiceNumber;

    public GseInvoiceForm $form;

    public $gseTypes;
    public $branches;
    public $airlines;

    public ?GseInvoice $invoice = null;

    public array $availableRecaps = [];
    public array $selectedRecapIds = [];
    public array $pivotRows = [];

    public bool $isEdit = false;

    public function mount(?int $id = null): void
    {
        $this->gseTypes = GseType::query()
            ->orderBy('service_name')
            ->get(['id', 'service_name']);

        $this->branches = Branch::query()
            ->where('status', 'ACTIVE')
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->airlines = Airline::query()
            ->where('status', 'ACTIVE')
            ->orderBy('name')
            ->get(['id', 'name']);

        $today = now()->format('Y-m-d');
        $this->form->dateFrom = $today;
        $this->form->dateTo = $today;

        if ($id === null) {
            return;
        }

        $this->isEdit = true;
        $this->invoice = GseInvoice::query()
            ->with([
                'recaps.gseType:id,service_name',
                'recaps.branch:id,name',
                'recaps.airline:id,name',
                'recaps.equipment:id,registration',
                'recaps.gpuDetail:id,gse_recap_id,start_time,end_time',
                'recaps.pushbackDetail:id,gse_recap_id,start_ps,end_ps',
            ])
            ->findOrFail($id);

        $this->form->setInvoice($this->invoice);

        if (filled($this->form->branch_id) && filled($this->form->airline_id)) {
            $this->loadRecaps(keepExistingSelection: true);
        }

        $this->hydratePivotRowsFromInvoice();
    }

    public function updated($field): void
    {
        if (str_starts_with($field, 'form.')) {
            $this->form->validateOnly(str_replace('form.', '', $field), $this->form->rules());
        }
    }

    public function updatedFormGseTypeId($value): void
    {
        if (! $this->isEdit && filled($value)) {
            $this->form->invoice_number = $this->generateGseInvoiceNumber((int) $value);
        }

        // A GSE type change can point to different rates and eligible recaps.
        $this->availableRecaps = [];
        $this->selectedRecapIds = [];
        $this->pivotRows = [];
    }

    public function updatedFormBranchId(): void
    {
        $this->availableRecaps = [];
        $this->selectedRecapIds = [];
        $this->pivotRows = [];
    }

    public function updatedFormAirlineId(): void
    {
        $this->availableRecaps = [];
        $this->selectedRecapIds = [];
        $this->pivotRows = [];
    }

    public function updatedPivotRows($value, $path): void
    {
        $segments = explode('.', $path);
        $recapId = $segments[0] ?? null;
        $field = $segments[1] ?? null;

        if (($field !== 'quantity') || blank($recapId) || ! isset($this->pivotRows[$recapId])) {
            return;
        }

        $quantity = $this->normalizeDecimal($value);
        $serviceRate = $this->normalizeDecimal($this->pivotRows[$recapId]['service_rate'] ?? 0);

        $this->pivotRows[$recapId]['quantity'] = $quantity;
        $this->pivotRows[$recapId]['amount'] = round($quantity * $serviceRate, 2);
    }

    public function loadRecaps(bool $keepExistingSelection = false): void
    {
        $this->form->validate($this->form->headerRules());

        // Only show recaps that have an active rate for their own service date.
        $recaps = GseRecap::query()
            ->with([
                'gseType:id,service_name',
                'branch:id,name',
                'airline:id,name',
                'equipment:id,registration',
                'gpuDetail:id,gse_recap_id,start_time,end_time',
                'pushbackDetail:id,gse_recap_id,start_ps,end_ps',
            ])
            ->where('gse_type_id', $this->form->gse_type_id)
            ->where('branch_id', $this->form->branch_id)
            ->where('airline_id', $this->form->airline_id)
            ->whereBetween('service_date', [$this->form->dateFrom, $this->form->dateTo])
            ->whereHas('gseType.rates', function (Builder $query): void {
                $query
                    ->whereColumn('gse_type_rates.gse_type_id', 'gse_recaps.gse_type_id')
                    ->whereDate('gse_type_rates.effective_from', '<=', DB::raw('DATE(gse_recaps.service_date)'))
                    ->where(function (Builder $subQuery): void {
                        $subQuery
                            ->whereNull('gse_type_rates.effective_to')
                            ->orWhereDate('gse_type_rates.effective_to', '>=', DB::raw('DATE(gse_recaps.service_date)'));
                    });
            })
            ->orderBy('service_date')
            ->orderBy('flight_number')
            ->get();

        $existingSelection = $keepExistingSelection
            ? collect($this->selectedRecapIds)->map(fn ($id) => (string) $id)->all()
            : [];

        $this->availableRecaps = $this->mapRecapsForDisplay($recaps);
        $this->pivotRows = $this->buildPivotRows($recaps, preserveExisting: $keepExistingSelection);

        $recapIds = $recaps->pluck('id')->map(fn ($id) => (string) $id)->all();
        $this->selectedRecapIds = $keepExistingSelection
            ? array_values(array_intersect($existingSelection, $recapIds))
            : $recapIds;
    }

    public function saveChanges()
    {
        $this->form->validate();

        $payload = $this->validate([
            'selectedRecapIds' => ['required', 'array', 'min:1'],
            'selectedRecapIds.*' => ['integer', 'exists:gse_recaps,id'],
            'pivotRows' => ['array'],
        ]);

        $selectedRecapIds = collect($payload['selectedRecapIds'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $eligibleRecapCount = GseRecap::query()
            ->whereIn('id', $selectedRecapIds)
            ->where('gse_type_id', $this->form->gse_type_id)
            ->where('branch_id', $this->form->branch_id)
            ->where('airline_id', $this->form->airline_id)
            ->whereBetween('service_date', [$this->form->dateFrom, $this->form->dateTo])
            ->count();

        if ($eligibleRecapCount !== $selectedRecapIds->count()) {
            $this->addError('selectedRecapIds', 'Ada recap terpilih yang tidak sesuai dengan GSE type, cabang, airline, atau rentang tanggal invoice.');

            return null;
        }

        foreach ($payload['selectedRecapIds'] as $recapId) {
            $row = $this->pivotRows[$recapId] ?? null;

            if (! $row || blank($row['gse_type_rate_id']) || blank($row['charge_type'])) {
                $this->addError('selectedRecapIds', 'Ada recap terpilih yang tidak memiliki rate aktif untuk GSE type dan tanggal servicenya.');

                return null;
            }
        }

        return DB::transaction(function () use ($payload) {
            $invoice = $this->form->persist();

            $this->invoice = $invoice;
            $this->isEdit = true;

            // Store the computed billing details on the pivot so later rate
            // changes do not rewrite historical invoice amounts.
            $syncData = collect($payload['selectedRecapIds'])
                ->mapWithKeys(function ($recapId): array {
                    $row = $this->pivotRows[$recapId] ?? [];

                    return [
                        $recapId => [
                            'gse_type_rate_id' => $row['gse_type_rate_id'] ?: null,
                            'charge_type' => $row['charge_type'],
                            'service_rate' => $this->normalizeDecimal($row['service_rate']),
                            'quantity' => $this->normalizeDecimal($row['quantity']),
                            'amount' => $this->normalizeDecimal($row['amount']),
                        ],
                    ];
                })
                ->all();

            $invoice->recaps()->sync($syncData);

            session()->flash('notify', [
                'content' => $invoice->invoice_number . ' berhasil disave',
                'type' => 'success',
            ]);

            return $this->redirectRoute('invoicegse', navigate: true);
        });
    }

    public function delete()
    {
        if (! $this->form->record) {
            return;
        }

        $invoiceNumber = $this->form->record->invoice_number;
        $this->form->delete();

        session()->flash('notify', [
            'content' => $invoiceNumber . ' berhasil dihapus!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('invoicegse', navigate: true);
    }

    public function getSelectedTotalProperty(): float
    {
        return collect($this->selectedRecapIds)
            ->sum(fn ($recapId) => $this->normalizeDecimal($this->pivotRows[$recapId]['amount'] ?? 0));
    }

    private function hydratePivotRowsFromInvoice(): void
    {
        if (! $this->form->record) {
            return;
        }

        // Editing starts from the saved pivot values, not recalculated rates.
        foreach ($this->form->record->recaps as $recap) {
            $recapId = (string) $recap->getKey();

            $this->pivotRows[$recapId] = [
                'gse_type_rate_id' => (string) ($recap->pivot->gse_type_rate_id ?? ''),
                'charge_type' => (string) $recap->pivot->charge_type,
                'service_rate' => (float) $recap->pivot->service_rate,
                'quantity' => (float) $recap->pivot->quantity,
                'amount' => (float) $recap->pivot->amount,
            ];
        }

        $this->selectedRecapIds = $this->form->record->recaps
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }

    private function mapRecapsForDisplay(Collection $recaps): array
    {
        return $recaps
            ->map(function (GseRecap $recap): array {
                $rate = $this->resolveRateForRecap($recap);
                $quantity = $this->calculateQuantity($recap, $rate?->charge_type);
                $serviceRate = (float) ($rate?->service_rate ?? 0);

                return [
                    'id' => (string) $recap->getKey(),
                    'service_date' => $recap->service_date?->format('Y-m-d') ?? '-',
                    'er_number' => $recap->er_number,
                    'flight_number' => $recap->flight_number,
                    'airline' => $recap->airline?->name ?? '-',
                    'branch' => $recap->branch?->name ?? '-',
                    'equipment' => $recap->equipment?->registration ?? '-',
                    'service' => $recap->gseType?->service_name ?? '-',
                    'charge_type' => (string) ($rate?->charge_type ?? '-'),
                    'service_rate' => $serviceRate,
                    'quantity' => $quantity,
                    'amount' => round($quantity * $serviceRate, 2),
                ];
            })
            ->all();
    }

    private function buildPivotRows(Collection $recaps, bool $preserveExisting = false): array
    {
        $rows = $preserveExisting ? $this->pivotRows : [];

        foreach ($recaps as $recap) {
            $recapId = (string) $recap->getKey();

            if ($preserveExisting && isset($rows[$recapId])) {
                continue;
            }

            $rate = $this->resolveRateForRecap($recap);
            $quantity = $this->calculateQuantity($recap, $rate?->charge_type);
            $serviceRate = (float) ($rate?->service_rate ?? 0);

            $rows[$recapId] = [
                'gse_type_rate_id' => (string) ($rate?->getKey() ?? ''),
                'charge_type' => (string) ($rate?->charge_type ?? ''),
                'service_rate' => $serviceRate,
                'quantity' => $quantity,
                'amount' => round($quantity * $serviceRate, 2),
            ];
        }

        return $rows;
    }

    private function resolveRateForRecap(GseRecap $recap): ?GseTypeRate
    {
        return GseTypeRate::query()
            ->where('gse_type_id', $recap->gse_type_id)
            ->whereDate('effective_from', '<=', $recap->service_date)
            ->where(function (Builder $query) use ($recap): void {
                $query
                    ->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $recap->service_date);
            })
            ->orderByDesc('effective_from')
            ->first();
    }

    private function calculateQuantity(GseRecap $recap, ?string $chargeType): float
    {
        if ($chargeType !== 'HOURLY') {
            return 1.0;
        }

        // Hourly charges use the detail table times and round partial hours up.
        $start = $recap->gpuDetail?->start_time ?? $recap->pushbackDetail?->start_ps;
        $end = $recap->gpuDetail?->end_time ?? $recap->pushbackDetail?->end_ps;

        if (blank($start) || blank($end)) {
            return 1.0;
        }

        try {
            $startTime = Carbon::createFromFormat(strlen((string) $start) === 8 ? 'H:i:s' : 'H:i', (string) $start);
            $endTime = Carbon::createFromFormat(strlen((string) $end) === 8 ? 'H:i:s' : 'H:i', (string) $end);
        } catch (\Throwable) {
            return 1.0;
        }

        $minutes = $startTime->diffInMinutes($endTime, false);

        if ($minutes <= 0) {
            return 1.0;
        }

        return (float) ceil($minutes / 60);
    }

    private function normalizeDecimal(mixed $value): float
    {
        return round((float) $value, 2);
    }

    public function render()
    {
        return view('livewire.gse-invoices.create');
    }
}

