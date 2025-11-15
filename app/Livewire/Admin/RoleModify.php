<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Livewire\Forms\RoleForm;
use App\Models\Role;

class RoleModify extends Component
{
    public RoleForm $form;

    public $roles;
    public $branches;

    public bool $isEdit = false;
    public Role $role;

    /**
     * Mount for both create & edit.
     */
    public function mount(?int $roleid = null)
    {
        if ($roleid) {
            $this->isEdit = true;
            $this->role   = Role::findOrFail($roleid);

            // Fill form from existing user
            $this->form->fillFromModel($this->role);
        }
    }

    /**
     * Save or update user.
     */
    public function saveChanges()
    {
        $this->form->validate();

        // Base data
        $data = $this->form->toModelArray();

        if ($this->isEdit) {
            $name = $this->role->label;
            $this->role->update($data);
            session()->flash('notify', [
                'content' => $name . ' sukses diupdate',
                'type'    => 'success',
            ]);
        } else {
            Role::create($data);
            session()->flash('notify', [
                'content' => 'Role sukses dibuat',
                'type'    => 'success',
            ]);
        }

        return $this->redirectRoute('admin.roles', navigate: true);
    }

    /**
     * Delete user.
     */
    public function delete()
    {
        if ($this->isEdit) {
            $nama = $this->role->label;

            $this->role->delete();
            session()->flash('notify', [
                'content' => $nama . ' telah dihapus',
                'type'    => 'error',
            ]);
        }

        return $this->redirectRoute('admin.roles', navigate: true);
    }
    public function render()
    {
        return view('livewire.admin.role-modify');
    }
}
