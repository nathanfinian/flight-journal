<?php

namespace App\Livewire\GseInventoryCategories;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CategoryForm extends Component
{
    public ?int $categoryId = null;
    public string $category_name = '';
    public string $status = 'ACTIVE';
    public array $subCategories = [];
    public bool $isEdit = false;

    public function mount(?int $id = null): void
    {
        if ($id === null) {
            // Give create mode one editable child row so users can start typing immediately.
            $this->addSubCategory();

            return;
        }

        $category = Category::query()
            ->with(['subCategories' => fn ($query) => $query->orderBy('sub_category_name')])
            ->findOrFail($id);

        $this->isEdit = true;
        $this->categoryId = $category->getKey();
        $this->category_name = (string) $category->category_name;
        $this->status = (string) $category->status;
        $this->subCategories = $category->subCategories
            ->map(fn (SubCategory $subCategory): array => [
                'id' => $subCategory->getKey(),
                'name' => (string) $subCategory->sub_category_name,
                'status' => (string) $subCategory->status,
                'delete' => false,
            ])
            ->values()
            ->all();

        if ($this->subCategories === []) {
            $this->addSubCategory();
        }
    }

    protected function rules(): array
    {
        return [
            'category_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'category_name')->ignore($this->categoryId, 'category_id'),
            ],
            'status' => ['required', 'string', 'max:20'],
            'subCategories' => ['array'],
            'subCategories.*.id' => ['nullable', 'integer', 'exists:sub_categories,sub_category_id'],
            'subCategories.*.name' => ['nullable', 'string', 'max:100'],
            'subCategories.*.status' => ['required', 'string', 'max:20'],
            'subCategories.*.delete' => ['boolean'],
        ];
    }

    public function saveChanges()
    {
        $payload = $this->validate();
        $this->validateSubCategoryRows();

        // Persist the parent and all child row edits together so the category
        // never saves without its intended sub-category changes.
        DB::transaction(function () use ($payload): void {
            if ($this->categoryId) {
                Category::query()
                    ->whereKey($this->categoryId)
                    ->update([
                        'category_name' => $payload['category_name'],
                        'status' => $payload['status'],
                    ]);

                $category = Category::query()->findOrFail($this->categoryId);
            } else {
                $category = Category::query()->create([
                    'category_name' => $payload['category_name'],
                    'status' => $payload['status'],
                ]);

                $this->categoryId = $category->getKey();
            }

            $this->persistSubCategories($category);
        });

        session()->flash('notify', [
            'content' => 'Kategori berhasil disimpan!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('gsecategories', navigate: true);
    }

    public function addSubCategory(): void
    {
        $this->subCategories[] = [
            'id' => null,
            'name' => '',
            'status' => 'ACTIVE',
            'delete' => false,
        ];
    }

    public function removeSubCategory(int $index): void
    {
        if (! isset($this->subCategories[$index])) {
            return;
        }

        if ($this->subCategories[$index]['id'] ?? null) {
            // Existing rows are deleted on save; this keeps the action reversible.
            $this->subCategories[$index]['delete'] = true;

            return;
        }

        unset($this->subCategories[$index]);
        $this->subCategories = array_values($this->subCategories);

        if ($this->subCategories === []) {
            $this->addSubCategory();
        }
    }

    public function restoreSubCategory(int $index): void
    {
        if (isset($this->subCategories[$index])) {
            $this->subCategories[$index]['delete'] = false;
        }
    }

    public function delete()
    {
        $row = Category::query()->find($this->categoryId);
        $name = $row?->category_name ?? 'Unknown';

        if (! $row) {
            session()->flash('notify', [
                'content' => 'Kategori tidak ditemukan',
                'type' => 'error',
            ]);

            return;
        }

        try {
            // Category deletion cascades as a soft delete for its sub-categories.
            DB::transaction(function () use ($row): void {
                $row->subCategories()->delete();
                $row->delete();
            });

            session()->flash('notify', [
                'content' => 'Kategori ' . $name . ' dan sub kategorinya berhasil dihapus!',
                'type' => 'success',
            ]);

            return $this->redirectRoute('gsecategories', navigate: true);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Kategori ini masih dipakai dan tidak dapat dihapus.',
                    'type' => 'warning',
                ]);

                return;
            }

            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.gse-inventory-categories.category-form');
    }

    private function validateSubCategoryRows(): void
    {
        $messages = [];
        // Empty new rows are allowed; they are ignored during persistence.
        $activeRows = collect($this->subCategories)
            ->reject(fn (array $row): bool => (bool) ($row['delete'] ?? false))
            ->filter(fn (array $row): bool => trim((string) ($row['name'] ?? '')) !== '');

        $duplicate = $activeRows
            ->map(fn (array $row): string => strtolower(trim((string) $row['name'])))
            ->duplicates()
            ->first();

        if ($duplicate) {
            $messages['subCategories'] = 'Nama sub kategori tidak boleh duplikat dalam kategori yang sama.';
        }

        foreach ($this->subCategories as $index => $row) {
            $name = trim((string) ($row['name'] ?? ''));
            $shouldDelete = (bool) ($row['delete'] ?? false);

            if (! $shouldDelete && ($row['id'] ?? null) && $name === '') {
                $messages['subCategories.' . $index . '.name'] = 'Nama sub kategori wajib diisi.';
            }
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }
    }

    private function persistSubCategories(Category $category): void
    {
        foreach ($this->subCategories as $row) {
            $id = $row['id'] ?? null;
            $name = trim((string) ($row['name'] ?? ''));
            $status = (string) ($row['status'] ?? 'ACTIVE');
            $shouldDelete = (bool) ($row['delete'] ?? false);

            if ($id && $shouldDelete) {
                $subCategory = SubCategory::query()
                    ->where('category_id', $category->getKey())
                    ->findOrFail($id);

                // Keep inventory item history intact when a sub-category is already used.
                if (DB::table('items')->where('sub_category_id', $subCategory->getKey())->exists()) {
                    throw ValidationException::withMessages([
                        'subCategories' => 'Sub kategori ' . $subCategory->sub_category_name . ' masih dipakai oleh item dan tidak dapat dihapus.',
                    ]);
                }

                $subCategory->delete();

                continue;
            }

            if ($name === '') {
                continue;
            }

            $duplicateQuery = SubCategory::query()
                ->where('category_id', $category->getKey())
                ->where('sub_category_name', $name);

            if ($id) {
                $duplicateQuery->whereKeyNot($id);
            }

            if ($duplicateQuery->exists()) {
                throw ValidationException::withMessages([
                    'subCategories' => 'Sub kategori ' . $name . ' sudah ada pada kategori ini.',
                ]);
            }

            if ($id) {
                SubCategory::query()
                    ->where('category_id', $category->getKey())
                    ->whereKey($id)
                    ->update([
                        'sub_category_name' => $name,
                        'status' => $status,
                    ]);
            } else {
                SubCategory::query()->create([
                    'category_id' => $category->getKey(),
                    'sub_category_name' => $name,
                    'status' => $status,
                ]);
            }
        }
    }
}
