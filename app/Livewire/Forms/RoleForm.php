<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Role;
use Livewire\Attributes\Validate;

class RoleForm extends Form
{
    #[Validate('required|string|min:3|max:80')]
    public string $name = '';

    #[Validate('required|string|min:3|max:80')]
    public string $label = '';

    public function fillFromModel(Role $role)
    {
        $this->name     = $role->name;
        $this->label     = $role->label;
    }

    protected function messages()
    {
        return [
            'name.required' => 'Nama role wajib diisi.',
            'label.required' => 'Label role wajib diisi.',
        ];
    }

    public function toModelArray(): array
    {
        return [
            'name'      => $this->name,
            'label'      => $this->label,
        ];
    }
}
