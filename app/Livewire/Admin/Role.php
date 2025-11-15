<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role as ModelsRole;

class Role extends Component
{
    use WithPagination;

    private int $perPage = 5;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('admin.roles.edit', ['roleid' => $id], navigate: true);
    }

    public function render()
    {
        $roles = ModelsRole::query()
            ->orderBy('name', 'asc')
            ->paginate($this->perPage); // change to ->get() to disable pagination

        return view('livewire.admin.role', compact('roles'));
    }
}
