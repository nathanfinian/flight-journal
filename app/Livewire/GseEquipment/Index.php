<?php

namespace App\Livewire\GseEquipment;

use App\Models\Branch;
use App\Models\GseEquipment;
use App\Models\GseType;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public string $search = '';
    public string $branch_id = '';
    public string $gse_type_id = '';

    public function openEdit(int $id)
    {
        return $this->redirectRoute('gseequipment.edit', ['id' => $id], navigate: true);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedBranchId(): void
    {
        $this->resetPage();
    }

    public function updatedGseTypeId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'branch_id', 'gse_type_id']);
        $this->resetPage();
    }

    public function render()
    {
        $equipment = GseEquipment::query()
            ->with([
                'gseType:id,type_name',
                'branch:id,name',
            ])
            ->withCount('stockMovements')
            ->when(filled($this->search), fn ($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->when(filled($this->branch_id), fn ($query) => $query->where('branch_id', $this->branch_id))
            ->when(filled($this->gse_type_id), fn ($query) => $query->where('gse_type_id', $this->gse_type_id))
            ->orderBy('equipment_code')
            ->paginate($this->perPage);

        return view('livewire.gse-equipment.index', [
            'equipment' => $equipment,
            'branches' => $this->branches(),
            'gseTypes' => $this->gseTypes(),
        ]);
    }

    private function branches(): Collection
    {
        return Branch::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function gseTypes(): Collection
    {
        return GseType::query()
            ->orderBy('type_name')
            ->get(['id', 'type_name']);
    }
}
