<x-slot:title>
    {{ $title ?? 'Sheaf UI' }}
</x-slot:title>

<x-layouts.base>
    <x-layouts.partials.nav />

    <div class="mx-auto max-w-7xl mt-30">
        {{ $slot }}
    </div>
</x-layouts.base>
