<div class="flex items-center gap-2">
    <a data-slot="button"
       class="dark:hover:bg-white/6 text-primary-brand bg-base-200/6 relative inline-flex h-[32px] w-[32px] items-center justify-center gap-2 whitespace-nowrap rounded-[3px] text-sm font-medium hover:bg-neutral-200"
       href="/dashboard">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-10 w-auto" />
    </a>
    <a class="inline-flex items-center"
       href="{{ route('dashboard') }}"
       wire:navigate>
        <h1 class='text-primary-brand text-lg font-bold leading-8'>MCA | Journal</h1>
    </a>
</div>
