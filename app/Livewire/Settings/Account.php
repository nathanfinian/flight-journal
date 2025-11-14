<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use App\Livewire\Concerns\HasToast;

class Account extends Component
{
    use HasToast;

    #[Validate('required|string|min:3|max:12')]
    public string $name = '';

    #[Validate('required|string|username')]
    public string $username = '';

    #[Validate('required|string|current_password')]
    public string $current_password = '';

    #[Validate('required|string|confirmed|min:8')]
    public string $password = '';

    public string $password_confirmation = '';

    public function mount(#[CurrentUser] User $user)
    {
        $this->name = $user->name;
        $this->username = $user->username;
    }

    public function saveChanges(#[CurrentUser] User $user)
    {
        $this->username = trim($this->username);
        $this->name = trim($this->name);

        $validated = $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:12'],
            'username' => [
                'required',
                'string',
                'username',
                'min:3',
                'max:20',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ]);

        $user->fill($validated);

        $user->save();

        $this->toastSuccess('Your account has been updated.');
    }

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(#[CurrentUser] $user): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->toastSuccess('Your password has been updated.');
    }
    public function render()
    {
        return view('livewire.settings.account');
    }
}
