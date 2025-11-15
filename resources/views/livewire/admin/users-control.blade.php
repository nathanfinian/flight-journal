<div class="mx-auto max-w-2xl space-y-20 mt-20">
  <div>
    <x-ui.heading level="h1" size="xl">Users</x-ui.heading>
    <x-ui.text class="opacity-50">Update data user di sini</x-ui.text>

    <x-ui.separator class="my-2"/>
    <x-ui.breadcrumbs>
      <x-ui.breadcrumbs.item wire:navigate.hover :href="route('admin.index')">Admin</x-ui.breadcrumbs.item>
      <x-ui.breadcrumbs.item wire:navigate.hover :href="route('admin.users')">Users</x-ui.breadcrumbs.item>
      <x-ui.breadcrumbs.item>Modify User</x-ui.breadcrumbs.item>
    </x-ui.breadcrumbs>

    <div class="grow">
      <div class="w-full max-w-xl mx-auto space-y-4">
        <form
          wire:submit="saveChanges"
          class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
        >
            @csrf

            {{-- Username --}}
            <x-ui.field required>
                <x-ui.label>Username</x-ui.label>
                <x-ui.input
                wire:model.defer="form.username"
                maxlength="80"
                clearable
                />
                <x-ui.error name="form.username" />
            </x-ui.field>

            {{-- Name --}}
            <x-ui.field required>
                <x-ui.label>Name</x-ui.label>
                <x-ui.input
                wire:model.defer="form.name"
                maxlength="80"
                placeholder="Budi Hartono"
                clearable
                />
                <x-ui.error name="form.name" />
            </x-ui.field>

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-6">
                        <x-ui.field required>
                        <x-ui.label>Password</x-ui.label>
                        <x-ui.input 
                            wire:model="form.password" 
                            placeholder="Password"
                            type="password"
                            revealable
                        />
                        <x-ui.error name="form.password" />
                    </x-ui.field>
                </div>
                <div class="col-span-6">
                    <x-ui.field>
                        <x-ui.label>Confirm</x-ui.label>
                        <x-ui.input 
                            wire:model="form.password_confirmation" 
                            placeholder="Password"
                            type="password"
                            revealable
                        />
                        <x-ui.error name="form.password_confirmation" />
                    </x-ui.field>
                </div>
            </div>

            {{-- Role --}}
            <x-ui.field required>
                <x-ui.label>Role</x-ui.label>
                <x-ui.select 
                    placeholder="Pilih role..."
                    icon="flag"
                    wire:model="form.role">
                        @foreach ($roles as $role)
                            <x-ui.select.option value="{{ $role->id }}">{{ $role->label }}</x-ui.select.option>
                        @endforeach
                </x-ui.select>
                <x-ui.error name="form.role" />
            </x-ui.field>
            
            
            {{-- Branch --}}
            <x-ui.field required>
                <x-ui.label>Branch</x-ui.label>
                <x-ui.select 
                    placeholder="Pilih cabang..."
                    icon="flag"
                    wire:model="form.branch">
                        @foreach ($branches as $branch)
                            <x-ui.select.option value="{{ $branch->id }}">{{ $branch->name }}</x-ui.select.option>
                        @endforeach
                </x-ui.select>
                <x-ui.error name="form.branch" />
            </x-ui.field>

            <div class="flex items-center justify-between mt-6">
                <x-ui.button type="submit">Save changes</x-ui.button>
                @if ($this->isEdit)
                    <x-ui.modal.trigger id="delete-modal">
                        <x-ui.button variant="ghost">
                        <x-ui.icon name="ps:trash" variant="thin" class="text-red-600 dark:text-red-500"/>
                        </x-ui.button>
                    </x-ui.modal.trigger>
                @endif
            </div>
        </form>
      </div>
    </div>
  </div>

<x-ui.modal 
    id="delete-modal"
    position="center"
    heading="Delete User"
    description="Yakin ingin menghapus user ini?"
>
    <div class="mt-4 flex justify-end space-x-2">
        <x-ui.button 
            variant="outline" 
            x-on:click="$data.close();"
        >
            Cancel
        </x-ui.button>
        <x-ui.button 
            variant="danger" 
            wire:click.stop="delete"
        >
            Delete
        </x-ui.button>
    </div>
</x-ui.modal>
</div>
