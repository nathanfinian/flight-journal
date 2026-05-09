<?php

namespace App\Livewire\GseRates;

use App\Models\GseTypeRate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('rategse.edit', ['id' => $id], navigate: true);
    }

    public function render()
    {
        $gseRates = GseTypeRate::query()
            ->with('gseType:id,type_name')
            ->orderByDesc('effective_from')
            ->paginate($this->perPage);

        return view('livewire.gse-rates.index', compact('gseRates'));
    }
}
