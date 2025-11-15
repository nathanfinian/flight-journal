<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    private int $perPage = 5;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('admin.users.edit', ['userid' => $id], navigate: true);
    }

    public function render()
    {
        $users = User::query()
        ->with([
            'role:id,label',
            'branch:id,name'
        ])
        ->orderBy('name', 'asc')
        ->paginate($this->perPage); // change to ->get() to disable pagination

        return view('livewire.admin.users', compact('users'));
    }
}
