<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use Livewire\Component;
use App\Livewire\Forms\UsersForm;

class UsersControl extends Component
{
    public UsersForm $form;
    
    public $roles;
    public $branches;

    public bool $isEdit = false;
    public User $user;

    /**
     * Mount for both create & edit.
     */
    public function mount(?int $userid = null)
    {
        $this->roles = Role::orderBy('name')->get(['id', 'label']);
        $this->branches = Branch::orderBy('name')->get(['id', 'name']);

        if ($userid) {
            $this->isEdit = true;
            $this->user   = User::findOrFail($userid);

            // Fill form from existing user
            $this->form->fillFromModel($this->user);
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

        // Handle password hashing if provided
        $data = $this->form->applyPassword($data);

        if ($this->isEdit) {
            $name = $this->user->name;
            $this->user->update($data);
            session()->flash('notify', [
                'content' => $name . ' sukses diupdate',
                'type'    => 'success',
            ]);
        } else {
            User::create($data);
            session()->flash('notify', [
                'content' => 'User sukses dibuat',
                'type'    => 'success',
            ]);
        }

        return $this->redirectRoute('admin.users', navigate: true);
    }

    /**
     * Delete user.
     */
    public function delete()
    {
        if ($this->isEdit) {
            $nama = $this->user->name;

            $this->user->delete();
            session()->flash('notify', [
                'content' => $nama . ' telah dihapus',
                'type'    => 'error',
            ]);
        }

        return $this->redirectRoute('admin.users', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.users-control');
    }
}
