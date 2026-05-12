<?php

namespace App\Livewire\GseInventoryItems;

use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\SubCategory;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ItemForm extends Component
{
    public array $subCategories = [];
    public array $units = [];
    public array $branches = [];

    public ?int $itemId = null;
    public string $code = '';
    public string $sub_category_id = '';
    public string $name = '';
    public string $unit_id = '';
    public string $minimum_stock = '0';
    public string $status = 'ACTIVE';
    public array $stocks = [];
    public bool $isEdit = false;

    public function mount(?int $id = null): void
    {
        $this->subCategories = SubCategory::query()
            ->with('category:category_id,category_name')
            ->where('sub_categories.status', 'ACTIVE')
            ->join('categories', 'categories.category_id', '=', 'sub_categories.category_id')
            ->orderBy('categories.category_name')
            ->orderBy('sub_category_name')
            ->get(['sub_categories.sub_category_id', 'sub_categories.category_id', 'sub_categories.sub_category_name'])
            ->map(fn (SubCategory $subCategory): array => [
                'id' => $subCategory->sub_category_id,
                'name' => $subCategory->sub_category_name,
                'category' => $subCategory->category?->category_name ?? 'Uncategorized',
            ])
            ->sortBy(fn (array $row): string => $row['category'] . ' - ' . $row['name'])
            ->values()
            ->all();

        $this->units = Unit::query()
            ->orderBy('unit_name')
            ->get(['unit_id', 'unit_name', 'unit_symbol'])
            ->map(fn (Unit $unit): array => [
                'id' => $unit->unit_id,
                'name' => $unit->unit_name,
                'symbol' => $unit->unit_symbol,
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

        if ($id === null) {
            $this->addStock();

            return;
        }

        $item = Item::query()
            ->with(['stocks' => fn ($query) => $query->with('branch:id,name')->orderBy('branch_id')])
            ->findOrFail($id);

        $this->isEdit = true;
        $this->itemId = $item->getKey();
        $this->code = (string) $item->code;
        $this->sub_category_id = (string) $item->sub_category_id;
        $this->name = (string) $item->name;
        $this->unit_id = (string) $item->unit_id;
        $this->minimum_stock = (string) $item->minimum_stock;
        $this->status = (string) $item->status;
        $this->stocks = $item->stocks
            ->map(fn (ItemStock $stock): array => [
                'id' => $stock->getKey(),
                'branch_id' => (string) $stock->branch_id,
                'quantity' => (string) $stock->quantity,
                'delete' => false,
            ])
            ->values()
            ->all();

        if ($this->stocks === []) {
            $this->addStock();
        }
    }

    protected function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('items', 'code')->ignore($this->itemId, 'item_id'),
            ],
            'sub_category_id' => ['required', 'exists:sub_categories,sub_category_id'],
            'name' => ['required', 'string', 'max:255'],
            'unit_id' => ['required', 'exists:units,unit_id'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'max:20'],
            'stocks' => ['array'],
            'stocks.*.id' => ['nullable', 'integer', 'exists:item_stocks,item_stock_id'],
            'stocks.*.branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'stocks.*.quantity' => ['nullable', 'integer', 'min:0'],
            'stocks.*.delete' => ['boolean'],
        ];
    }

    public function saveChanges()
    {
        $payload = $this->validate();
        $this->validateStockRows();

        DB::transaction(function () use ($payload): void {
            $itemPayload = [
                'code' => $payload['code'],
                'sub_category_id' => $payload['sub_category_id'],
                'name' => $payload['name'],
                'unit_id' => $payload['unit_id'],
                'minimum_stock' => (int) $payload['minimum_stock'],
                'status' => $payload['status'],
            ];

            if ($this->itemId) {
                Item::query()
                    ->whereKey($this->itemId)
                    ->update($itemPayload);

                $item = Item::query()->findOrFail($this->itemId);
            } else {
                $item = Item::query()->create($itemPayload);
                $this->itemId = $item->getKey();
            }

            $this->persistStocks($item);
        });

        session()->flash('notify', [
            'content' => 'Barang berhasil disimpan!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('gseitems', navigate: true);
    }

    public function addStock(): void
    {
        $this->stocks[] = [
            'id' => null,
            'branch_id' => '',
            'quantity' => '0',
            'delete' => false,
        ];
    }

    public function removeStock(int $index): void
    {
        if (! isset($this->stocks[$index])) {
            return;
        }

        if ($this->stocks[$index]['id'] ?? null) {
            $this->stocks[$index]['delete'] = true;

            return;
        }

        unset($this->stocks[$index]);
        $this->stocks = array_values($this->stocks);

        if ($this->stocks === []) {
            $this->addStock();
        }
    }

    public function restoreStock(int $index): void
    {
        if (isset($this->stocks[$index])) {
            $this->stocks[$index]['delete'] = false;
        }
    }

    public function delete()
    {
        $row = Item::query()->find($this->itemId);
        $name = $row?->code ?? 'Unknown';

        if (! $row) {
            session()->flash('notify', [
                'content' => 'Barang tidak ditemukan',
                'type' => 'error',
            ]);

            return;
        }

        try {
            DB::transaction(function () use ($row): void {
                $row->stocks()->delete();
                $row->delete();
            });

            session()->flash('notify', [
                'content' => 'Barang ' . $name . ' dan stok awalnya berhasil dihapus!',
                'type' => 'success',
            ]);

            return $this->redirectRoute('gseitems', navigate: true);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Barang ini masih dipakai dan tidak dapat dihapus.',
                    'type' => 'warning',
                ]);

                return;
            }

            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.gse-inventory-items.item-form');
    }

    private function validateStockRows(): void
    {
        $messages = [];
        $activeRows = collect($this->stocks)
            ->reject(fn (array $row): bool => (bool) ($row['delete'] ?? false))
            ->filter(fn (array $row): bool => filled($row['branch_id'] ?? null));

        $duplicate = $activeRows
            ->map(fn (array $row): string => (string) $row['branch_id'])
            ->duplicates()
            ->first();

        if ($duplicate) {
            $messages['stocks'] = 'Satu cabang hanya boleh memiliki satu stok untuk barang ini.';
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }
    }

    private function persistStocks(Item $item): void
    {
        foreach ($this->stocks as $row) {
            $id = $row['id'] ?? null;
            $branchId = $row['branch_id'] ?? null;
            $quantity = (int) ($row['quantity'] ?? 0);
            $shouldDelete = (bool) ($row['delete'] ?? false);

            if ($id && $shouldDelete) {
                ItemStock::query()
                    ->where('item_id', $item->getKey())
                    ->whereKey($id)
                    ->delete();

                continue;
            }

            if (! filled($branchId)) {
                continue;
            }

            $duplicateQuery = ItemStock::query()
                ->where('item_id', $item->getKey())
                ->where('branch_id', $branchId);

            if ($id) {
                $duplicateQuery->whereKeyNot($id);
            }

            if ($duplicateQuery->exists()) {
                throw ValidationException::withMessages([
                    'stocks' => 'Cabang yang sama tidak boleh dipakai lebih dari sekali untuk barang ini.',
                ]);
            }

            if ($id) {
                ItemStock::query()
                    ->where('item_id', $item->getKey())
                    ->whereKey($id)
                    ->update([
                        'branch_id' => $branchId,
                        'quantity' => $quantity,
                    ]);
            } else {
                ItemStock::query()->create([
                    'item_id' => $item->getKey(),
                    'branch_id' => $branchId,
                    'quantity' => $quantity,
                ]);
            }
        }
    }
}
