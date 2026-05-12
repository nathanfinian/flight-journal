<?php

namespace App\Livewire\GseInventoryTransactions;

use App\Models\Branch;
use App\Models\GseEquipment;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class TransactionForm extends Component
{
    public array $items = [];
    public array $branches = [];
    public array $equipment = [];

    public ?int $movementId = null;
    public string $item_id = '';
    public string $branch_id = '';
    public string $gse_equipment_id = '';
    public string $movement_type = 'INPUT';
    public string $quantity = '1';
    public string $movement_date = '';
    public string $reference_no = '';
    public string $notes = '';
    public bool $isEdit = false;

    public function mount(?int $id = null): void
    {
        $this->items = Item::query()
            ->with('subCategory.category:category_id,category_name')
            ->where('items.status', 'ACTIVE')
            ->join('sub_categories', 'sub_categories.sub_category_id', '=', 'items.sub_category_id')
            ->join('categories', 'categories.category_id', '=', 'sub_categories.category_id')
            ->orderBy('categories.category_name')
            ->orderBy('sub_categories.sub_category_name')
            ->orderBy('items.name')
            ->get(['items.item_id', 'items.name', 'sub_categories.sub_category_name', 'categories.category_name'])
            ->map(fn (Item $item): array => [
                'id' => $item->item_id,
                'label' => ($item->category_name ?? '-') . ' - ' . ($item->sub_category_name ?? '-') . ' - ' . $item->name,
            ])
            ->all();

        $this->branches = Branch::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Branch $branch): array => [
                'id' => $branch->id,
                'name' => $branch->name,
            ])
            ->all();

        $this->equipment = GseEquipment::query()
            ->with('branch:id,name')
            ->orderBy('equipment_code')
            ->get(['gse_equipment_id', 'branch_id', 'equipment_code', 'name'])
            ->map(fn (GseEquipment $equipment): array => [
                'id' => $equipment->gse_equipment_id,
                'label' => $equipment->equipment_code . ' - ' . $equipment->name . ' (' . ($equipment->branch?->name ?? '-') . ')',
            ])
            ->all();

        $this->movement_date = now()->format('Y-m-d\TH:i');

        if ($id === null) {
            return;
        }

        $movement = StockMovement::query()->findOrFail($id);

        $this->isEdit = true;
        $this->movementId = $movement->getKey();
        $this->item_id = (string) $movement->item_id;
        $this->branch_id = (string) $movement->branch_id;
        $this->gse_equipment_id = (string) ($movement->gse_equipment_id ?? '');
        $this->movement_type = (string) $movement->movement_type;
        $this->quantity = (string) $movement->quantity;
        $this->movement_date = $movement->movement_date?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i');
        $this->reference_no = (string) ($movement->reference_no ?? '');
        $this->notes = (string) ($movement->notes ?? '');
    }

    protected function rules(): array
    {
        return [
            'item_id' => ['required', 'integer', 'exists:items,item_id'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'gse_equipment_id' => [
                Rule::excludeIf($this->gse_equipment_id === ''),
                'nullable',
                'integer',
                'exists:gse_equipment,gse_equipment_id',
            ],
            'movement_type' => ['required', 'in:INPUT,OUTPUT'],
            'quantity' => ['required', 'integer', 'min:1'],
            'movement_date' => ['required', 'date'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function saveChanges()
    {
        $payload = $this->validate();

        DB::transaction(function () use ($payload): void {
            if ($this->movementId) {
                $originalMovement = StockMovement::query()->findOrFail($this->movementId);
                $this->applyStockDelta(
                    (int) $originalMovement->item_id,
                    (int) $originalMovement->branch_id,
                    (string) $originalMovement->movement_type,
                    (int) $originalMovement->quantity,
                    true
                );

                StockMovement::query()
                    ->whereKey($this->movementId)
                    ->update($this->movementPayload($payload));
            } else {
                $movement = StockMovement::query()->create($this->movementPayload($payload) + [
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                ]);

                $this->movementId = $movement->getKey();
            }

            $this->applyStockDelta(
                (int) $payload['item_id'],
                (int) $payload['branch_id'],
                (string) $payload['movement_type'],
                (int) $payload['quantity']
            );
        });

        session()->flash('notify', [
            'content' => 'Transaksi inventory berhasil disimpan!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('gsetransactions', navigate: true);
    }

    public function delete()
    {
        $movement = StockMovement::query()->find($this->movementId);

        if (! $movement) {
            session()->flash('notify', [
                'content' => 'Transaksi tidak ditemukan.',
                'type' => 'error',
            ]);

            return;
        }

        DB::transaction(function () use ($movement): void {
            $this->applyStockDelta(
                (int) $movement->item_id,
                (int) $movement->branch_id,
                (string) $movement->movement_type,
                (int) $movement->quantity,
                true
            );

            $movement->delete();
        });

        session()->flash('notify', [
            'content' => 'Transaksi inventory berhasil dihapus!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('gsetransactions', navigate: true);
    }

    public function render()
    {
        return view('livewire.gse-inventory-transactions.transaction-form');
    }

    private function movementPayload(array $payload): array
    {
        return [
            'item_id' => $payload['item_id'],
            'branch_id' => $payload['branch_id'],
            'gse_equipment_id' => filled($payload['gse_equipment_id'] ?? null) ? $payload['gse_equipment_id'] : null,
            'movement_type' => $payload['movement_type'],
            'quantity' => (int) $payload['quantity'],
            'movement_date' => Carbon::parse($payload['movement_date'])->format('Y-m-d H:i:s'),
            'reference_no' => $payload['reference_no'] ?: null,
            'notes' => $payload['notes'] ?: null,
        ];
    }

    private function applyStockDelta(int $itemId, int $branchId, string $type, int $quantity, bool $reverse = false): void
    {
        $stock = ItemStock::query()->firstOrCreate(
            ['item_id' => $itemId, 'branch_id' => $branchId],
            ['quantity' => 0]
        );

        $delta = $type === 'INPUT' ? $quantity : -$quantity;

        if ($reverse) {
            $delta *= -1;
        }

        $newQuantity = (int) $stock->quantity + $delta;

        if ($newQuantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Stok cabang ini tidak cukup untuk transaksi output.',
            ]);
        }

        $stock->update(['quantity' => $newQuantity]);
    }
}
