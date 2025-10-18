<header 
    x-data="{ open: false }"
    x-on:keydown.window.escape="open = false"
    class="border-b-neutral-100 dark:border-b-neutral-800 dark:bg-neutral-900 bg-neutral-50 fixed inset-x-0 top-0 z-50 border-b"
>
    <nav 
        class="mx-auto flex items-center justify-between p-6 text-base-100 lg:px-8"
        aria-label="global"
    >
        <div class="flex pr-7">
                <x-app.logo />
        </div>

        <div class="hidden gap-4 lg:flex lg:items-center lg:justify-end">
            @guest
                <div class="space-x-4">
                    <a href="{{ route('login') }}"
                       class="text-sm font-semibold leading-6 text-neutral-900 dark:text-neutral-200">Login</a>
                    <a href="{{ route('register') }}"
                       class="rounded-md border border-neutral-300/40 bg-neutral-50 px-3 py-1 text-sm font-semibold text-neutral-900 dark:border-white/20 dark:bg-white/5 dark:text-white">Register</a>
                </div>
            @endguest

            @auth
                <x-user-dropdown/>
            @endauth

            <x-ui.separator 
                class="my-2" 
                vertical
            />

            <x-ui.theme-switcher variant="inline"/>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div x-show="open"
         class="lg:hidden"
         x-ref="dialog"
         x-cloak=""
         aria-modal="true">
        <div class="fixed inset-0 z-50 bg-background"></div>
        <div class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-background px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-base-200/10"
             x-on:click.away="open = false">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <x-app.logo />
                </div>

                <div class="flex items-center">
                <button type="button"
                        class="-m-2.5 rounded-md p-2.5 text-base-100"
                        x-on:click="open = false">
                    <span class="sr-only">Close menu</span>
                    <svg class="h-6 w-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="1.5"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                </div>
            </div>
            <div class="mt-6 flow-root">
                <div class="-my-6 divide-y divide-base-200/10">
                    <div class="py-6">
                        @guest
                            <a href="{{ route('login') }}"
                               class="-mx-3 block rounded-field px-3 py-2.5 text-base font-semibold leading-7 text hover:bg-base-200/10 bg-base-100/6">Login</a>
                            <a href="{{ route('register') }}"
                               class="mt-2 block rounded-md border ring-1 ring-base-200/10 bg-base-200/20 px-3 py-2.5 text-base font-semibold text-base-100">Register</a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
