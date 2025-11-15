<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\User;
use Livewire\Attributes\Validate;

class UsersForm extends Form
{
    #[Validate('required|string|min:3|max:80|alpha_num')]
    public string $username = '';

    #[Validate('required|string|min:3|max:80')]
    public string $name = '';

    #[Validate('nullable|string|min:6|confirmed')]
    public string $password = '';  // nullable on edit but required on create

    #[Validate('same:password')]
    public string $password_confirmation = '';

    #[Validate('required|exists:roles,id')]
    public string $role = '';

    #[Validate('required|exists:branches,id')]
    public string $branch = '';

    public function fillFromModel(User $user)
    {
        $this->username = $user->username;
        $this->name     = $user->name;
        $this->role     = $user->role_id ?? '1';
        $this->branch   = $user->branch_id ?? '1';
    }

    protected function messages()
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'role.exists'       => 'Role tidak valid.',
        ];
    }

    public function toModelArray(): array
    {
        return [
            'username'  => $this->username,
            'name'      => $this->name,
            'role_id'   => $this->role,
            'branch_id' => $this->branch
        ];
    }

    public function applyPassword(array $data): array
    {
        if ($this->password !== '') {
            $data['password'] = bcrypt($this->password);
        }

        return $data;
    }
}
