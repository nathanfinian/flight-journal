<?php

namespace App\Livewire\GseInventoryCategories;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    private int $perPage = 10;

    public function openCategoryEdit(int $id)
    {
        return $this->redirectRoute('gsecategories.edit', ['id' => $id], navigate: true);
    }

    public function render()
    {
        $categories = Category::query()
            ->with(['subCategories' => fn ($query) => $query->orderBy('sub_category_name')])
            ->orderBy('category_name')
            ->paginate($this->perPage, ['*'], 'categoriesPage');

        return view('livewire.gse-inventory-categories.index', compact('categories'));
    }
}
