<?php

namespace App\Livewire\GseTypes;

use App\Models\GseType;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('gsetype.edit', ['id' => $id], navigate: true);
    }

    public function render()
    {
        $gseTypes = GseType::query()
            ->withCount(['recaps'])
            ->orderBy('type_name')
            ->paginate($this->perPage);

        return view('livewire.gse-types.index', compact('gseTypes'));
    }
}
