<x-slot:title>
    Login to Flight Journal
</x-slot>

<form 
    wire:submit="login"
    class="mx-auto w-full max-w-md space-y-4"
>

    <div class="space-y-4">
        <x-ui.field>
            <x-ui.label>username</x-ui.label>
            <x-ui.input
                wire:model="form.username"
            />
            <x-ui.error name="form.username" />
        </x-ui.field>

        <x-ui.field>
            <x-ui.label>password</x-ui.label>
            <x-ui.input
                wire:model="form.password"
                type='password'
                revealable
            />
            <x-ui.error name="form.password" />
        </x-ui.field>
    </div>

    <x-ui.button 
        class="w-full"
        type="submit"
    >
        Log in
    </x-ui.button>
</form>
