<?php

namespace App\Livewire\GseInvoices;

use App\Livewire\Forms\GseInvoiceForm;
use App\Models\Airline;
use App\Models\Branch;
use App\Models\GseRecap;
use App\Models\GseType;
use App\Models\GseTypeRate;
use App\Models\Invoice_gse as GseInvoice;
use App\Support\GseDuration;
use App\Traits\GeneratesGseInvoiceNumber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    use GeneratesGseInvoiceNumber;

    public GseInvoiceForm $form;

    public $gseTypes;
    public $branches;
    public $airlines;
    public bool $hasCombinedGseTypes = false;

    public ?GseInvoice $invoice = null;

    public array $availableRecaps = [];
    public array $selectedRecapIds = [];
    public array $pivotRows = [];

    public bool $isEdit = false;

    public function mount(?int $id = null): void
    {
        $this->gseTypes = GseType::query()
            ->orderBy('type_name')
            ->get(['id', 'type_name']);
        $this->hasCombinedGseTypes = $this->hasGpuAndAttTypes();

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
                'recaps.gseType:id,type_name',
                'recaps.branch:id,name',
                'recaps.airline:id,name',
                'recaps.equipment:id,registration',
                'recaps.gpuDetail:id,gse_recap_id,start_time,end_time',
                'recaps.pushbackDetail:id,gse_recap_id,start_ps,end_ps',
            ])
            ->findOrFail($id);

        $this->form->setInvoice($this->invoice);

        if ($this->hasMixedGpuAttRecaps($this->invoice->recaps)) {
            $this->form->gse_type_id = GseInvoiceForm::COMBINED_GPU_ATT;
        }

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
            $this->form->invoice_number = $value === GseInvoiceForm::COMBINED_GPU_ATT
                ? $this->generateCombinedGpuAttInvoiceNumber()
                : $this->generateGseInvoiceNumber((int) $value);
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

        $recaps = GseRecap::query()
            ->with([
                'gseType:id,type_name',
                'branch:id,name',
                'airline:id,name',
                'equipment:id,registration',
                'gpuDetail:id,gse_recap_id,start_time,end_time',
                'pushbackDetail:id,gse_recap_id,start_ps,end_ps',
            ])
            ->whereIn('gse_type_id', $this->selectedGseTypeIds())
            ->where('branch_id', $this->form->branch_id)
            ->where('airline_id', $this->form->airline_id)
            ->whereBetween('service_date', [$this->form->dateFrom, $this->form->dateTo])
            ->orderBy('service_date')
            ->orderBy('flight_number')
            ->get();

        $existingSelection = $keepExistingSelection
            ? collect($this->selectedRecapIds)->map(fn ($id) => (string) $id)->all()
            : [];

        $this->pivotRows = $this->buildPivotRows($recaps, preserveExisting: $keepExistingSelection);
        $this->availableRecaps = $this->mapRecapsForDisplay($recaps);

        $recapIds = collect($this->availableRecaps)->where('has_rate', true)->pluck('id')->all();
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

        $eligibleRecaps = GseRecap::query()
            ->with(['gpuDetail', 'pushbackDetail'])
            ->whereIn('id', $selectedRecapIds)
            ->whereIn('gse_type_id', $this->selectedGseTypeIds())
            ->where('branch_id', $this->form->branch_id)
            ->where('airline_id', $this->form->airline_id)
            ->whereBetween('service_date', [$this->form->dateFrom, $this->form->dateTo])
            ->get();

        if ($eligibleRecaps->count() !== $selectedRecapIds->count()) {
            $this->addError('selectedRecapIds', 'Ada recap terpilih yang tidak sesuai dengan GSE type, cabang, airline, atau rentang tanggal invoice.');

            return null;
        }

        $quantityRules = [];
        foreach ($selectedRecapIds as $recapId) {
            $quantityRules["pivotRows.{$recapId}.quantity"] = ['required', 'numeric', 'min:0', 'max:9999999999.99'];
        }
        $this->validate($quantityRules);

        return DB::transaction(function () use ($eligibleRecaps) {
            GseType::whereIn('id', $eligibleRecaps->pluck('gse_type_id')->unique())
                ->orderBy('id')->lockForUpdate()->get();

            $this->form->record?->load('recaps');
            $billingRows = $this->buildPivotRows($eligibleRecaps);
            $syncData = [];

            foreach ($billingRows as $recapId => $row) {
                if (blank($row['charge_type'])) {
                    throw ValidationException::withMessages([
                        'selectedRecapIds' => 'Ada recap terpilih yang belum memiliki satu rate aktif untuk airline, cabang, GSE type, dan tanggal service.',
                    ]);
                }

                $quantity = $this->normalizeDecimal($this->pivotRows[$recapId]['quantity']);
                $saved = $this->form->record?->recaps->firstWhere('id', $recapId)?->pivot;
                $loaded = $this->pivotRows[$recapId];
                if (! $saved && (
                    (string) $row['gse_type_rate_id'] !== (string) ($loaded['gse_type_rate_id'] ?? '')
                    || $row['charge_type'] !== ($loaded['charge_type'] ?? '')
                    || (float) $row['service_rate'] !== (float) ($loaded['service_rate'] ?? 0)
                )) {
                    throw ValidationException::withMessages([
                        'selectedRecapIds' => 'Rate telah berubah. Muat ulang recap sebelum menyimpan invoice.',
                    ]);
                }

                $amount = $saved && $quantity === (float) $saved->quantity
                    ? (float) $saved->amount
                    : round($quantity * $row['service_rate'], 2);

                if ($amount > 9999999999.99) {
                    throw ValidationException::withMessages([
                        "pivotRows.{$recapId}.quantity" => 'Jumlah tagihan melebihi batas yang dapat disimpan.',
                    ]);
                }

                $syncData[$recapId] = [
                    'gse_type_rate_id' => $row['gse_type_rate_id'] ?: null,
                    'charge_type' => $row['charge_type'],
                    'service_rate' => $row['service_rate'],
                    'quantity' => $quantity,
                    'amount' => $amount,
                ];
            }

            $invoice = $this->form->persist();

            $this->invoice = $invoice;
            $this->isEdit = true;

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
                $row = $this->pivotRows[(string) $recap->getKey()];

                return [
                    'id' => (string) $recap->getKey(),
                    'service_date' => $recap->service_date?->format('Y-m-d') ?? '-',
                    'er_number' => $recap->er_number,
                    'flight_number' => $recap->flight_number,
                    'airline' => $recap->airline?->name ?? '-',
                    'branch' => $recap->branch?->name ?? '-',
                    'equipment' => $recap->equipment?->registration ?? '-',
                    'service' => $recap->gseType?->type_name ?? '-',
                    'has_rate' => filled($row['charge_type']),
                ];
            })
            ->all();
    }

    private function buildPivotRows(Collection $recaps, bool $preserveExisting = false): array
    {
        $rows = [];

        foreach ($recaps as $recap) {
            $recapId = (string) $recap->getKey();

            $saved = $this->form->record?->recaps->firstWhere('id', $recap->getKey())?->pivot;
            if ($saved) {
                $rows[$recapId] = [
                    'gse_type_rate_id' => (string) ($saved->gse_type_rate_id ?? ''),
                    'charge_type' => (string) $saved->charge_type,
                    'service_rate' => (float) $saved->service_rate,
                    'quantity' => (float) $saved->quantity,
                    'amount' => (float) $saved->amount,
                ];
                continue;
            }

            $rate = $this->resolveRateForRecap($recap);
            $quantity = $preserveExisting && isset($this->pivotRows[$recapId])
                ? $this->normalizeDecimal($this->pivotRows[$recapId]['quantity'])
                : $this->calculateQuantity($recap, $rate?->charge_type);
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
        $rates = GseTypeRate::forRecap($recap)->limit(2)->get();

        return $rates->count() === 1 ? $rates->first() : null;
    }

    private function calculateQuantity(GseRecap $recap, ?string $chargeType): float
    {
        if ($chargeType !== 'HOURLY') {
            return 1.0;
        }

        $start = $recap->gpuDetail?->start_time ?? $recap->pushbackDetail?->start_ps;
        $end = $recap->gpuDetail?->end_time ?? $recap->pushbackDetail?->end_ps;

        return GseDuration::billableHours($start, $end);
    }

    private function normalizeDecimal(mixed $value): float
    {
        return round((float) $value, 2);
    }

    private function selectedGseTypeIds(): array
    {
        if ($this->form->gse_type_id === GseInvoiceForm::COMBINED_GPU_ATT) {
            return $this->combinedGpuAttTypeIds()
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return [(int) $this->form->gse_type_id];
    }

    private function combinedGpuAttTypeIds(): Collection
    {
        return GseType::query()
            ->where(function (Builder $query): void {
                $query
                    ->whereRaw('LOWER(type_name) LIKE ?', ['%gpu%'])
                    ->orWhereRaw('LOWER(type_name) LIKE ?', ['%att%']);
            })
            ->orderByRaw("CASE WHEN LOWER(type_name) LIKE '%gpu%' THEN 0 ELSE 1 END")
            ->pluck('id');
    }

    private function hasGpuAndAttTypes(): bool
    {
        $serviceNames = GseType::query()
            ->where(function (Builder $query): void {
                $query
                    ->whereRaw('LOWER(type_name) LIKE ?', ['%gpu%'])
                    ->orWhereRaw('LOWER(type_name) LIKE ?', ['%att%']);
            })
            ->pluck('type_name')
            ->map(fn (string $serviceName): string => strtolower($serviceName));

        return $serviceNames->contains(fn (string $serviceName): bool => str_contains($serviceName, 'gpu'))
            && $serviceNames->contains(fn (string $serviceName): bool => str_contains($serviceName, 'att'));
    }

    private function hasMixedGpuAttRecaps(Collection $recaps): bool
    {
        $serviceNames = $recaps
            ->map(fn (GseRecap $recap): string => strtolower((string) $recap->gseType?->type_name))
            ->filter();

        return $serviceNames->contains(fn (string $serviceName): bool => str_contains($serviceName, 'gpu'))
            && $serviceNames->contains(fn (string $serviceName): bool => str_contains($serviceName, 'att'));
    }

    private function generateCombinedGpuAttInvoiceNumber(): string
    {
        return DB::transaction(function (): string {
            $period = now()->format('Ym');

            $last = GseInvoice::withTrashed()
                ->where('invoice_number', 'like', "INV-GSE-GPUATT-{$period}-%")
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $next = $last
                ? ((int) substr($last->invoice_number, -6)) + 1
                : 1;

            return sprintf('INV-GSE-GPUATT-%s-%06d', $period, $next);
        });
    }

    public function render()
    {
        return view('livewire.gse-invoices.create');
    }
}

