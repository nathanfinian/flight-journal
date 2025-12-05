<?php

namespace App\Livewire\Settings;

use App\Models\Branch as BranchModel;
use Livewire\Component;
use Livewire\WithPagination;

class Branch extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('settings.branch.edit', ['branch' => $id], navigate: true);
    }

    public function render()
    {
        $branches = BranchModel::query()
            ->orderBy('name', 'asc')
            ->paginate($this->perPage); // change to ->get() to disable pagination
            
        return view('livewire.settings.branch', compact('branches'));
    }
}
