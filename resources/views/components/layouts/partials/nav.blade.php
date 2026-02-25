{{-- this navbar used in app pages (when the user authenticated)  --}}
 @php
     $user = Auth::user();
 @endphp

<header 
    x-data="{ open: false }"
    x-on:keydown.window.escape="open = false"
    class="border-b-neutral-100 dark:border-b-neutral-800 dark:bg-neutral-900 bg-neutral-50 fixed inset-x-0 top-0 z-50 border-b"
>
    <x-ui.navbar class="mx-auto flex items-center justify-between px-6 py-3 text-base-100 lg:px-8" aria-label="global">
            <div class="flex items-center pr-7">
                <x-app.logo />
                <div class="flex gap-4 ml-8">
                    <x-ui.navbar.item 
                        wire:navigate.hover
                        icon="chart-pie"
                        icon:class="w-5 h-5" 
                        label="Dashboard" 
                        :href="route('dashboard')" 
                        :active="request()->is('dashboard')"
                    />
                    {{-- <x-ui.navbar.item 
                        wire:navigate.hover
                        icon="ps:notepad"
                        icon:class="w-5 h-5" 
                        label="Flight Journal" 
                        :href="route('flight-journal')"
                        :active="request()->is('flight-journal*')"
                    />
                    <x-ui.navbar.item 
                        wire:navigate.hover
                        icon="ps:calendar-dots"
                        icon:class="w-5 h-5" 
                        label="Scheduling" 
                        :href="route('flight-schedule')"
                        :active="request()->is('flight-schedule*')"
                    />
                    <x-ui.navbar.item 
                        wire:navigate.hover
                        icon="document-text"
                        icon:class="w-5 h-5" 
                        label="History" 
                        :href="route('flight-history')"
                        :active="request()->is('flight-history*')"
                    /> --}}
                    <x-ui.dropdown position="bottom-start">
                        <x-slot:button>
                            <x-ui.navbar.item 
                                wire:navigate.hover
                                icon="globe-asia-australia"
                                icon:class="w-5 h-5" 
                                label="Flights" 
                                :active="request()->is('flight-journal*')"
                            />
                        </x-slot:button>
                        
                        <x-slot:menu>
                            <x-ui.dropdown.group label="Data Penerbangan">
                                <x-ui.dropdown.separator />
                                <x-ui.dropdown.item icon="ps:notepad" :href="route('flight-journal')">
                                    Jurnal
                                </x-ui.dropdown.item>
                                
                                <x-ui.dropdown.item icon="ps:calendar-dots" :href="route('flight-schedule')">
                                    Penjadwalan
                                </x-ui.dropdown.item>

                                <x-ui.dropdown.item icon="document-text" :href="route('flight-history')">
                                    Sejarah
                                </x-ui.dropdown.item>
                            </x-ui.dropdown.group>
                        </x-slot:menu>
                    </x-ui.dropdown>
                    <x-ui.dropdown position="bottom-start">
                        <x-slot:button>
                            <x-ui.navbar.item 
                                wire:navigate.hover
                                icon="ps:newspaper"
                                icon:class="w-5 h-5" 
                                label="Invoicing" 
                                :active="request()->is('invoice*')"
                            />
                        </x-slot:button>
                        
                        <x-slot:menu>
                            <x-ui.dropdown.group label="Sistem Invoice">
                                <x-ui.dropdown.separator />
                                <x-ui.dropdown.item icon="ps:newspaper" :href="route('invoice')">
                                    Regular Invoice
                                </x-ui.dropdown.item>
                                
                                <x-ui.dropdown.item icon="document-plus" :href="route('deposit')">
                                    Deposit/Talangan
                                </x-ui.dropdown.item>

                                <x-ui.dropdown.item disabled icon="document" :href="route('invoice')">
                                    Charter Invoice
                                </x-ui.dropdown.item>

                                <x-ui.dropdown.item disabled icon="document" :href="route('invoice')">
                                    GSE Invoice
                                </x-ui.dropdown.item>
                            </x-ui.dropdown.group>
                        </x-slot:menu>
                    </x-ui.dropdown>
                    <x-ui.navbar.item 
                        wire:navigate.hover
                        icon="cog-6-tooth" 
                        icon:class="w-5 h-5"
                        label="Settings" 
                        :href="route('settings.index')" 
                        :active="request()->is('settings*')"
                    />
                    @role('admin')
                        <x-ui.navbar.item 
                            wire:navigate.hover
                            icon="user-circle" 
                            label="Admin" 
                            :href="route('admin.index')" 
                            :active="request()->is('admin*')"
                        />
                    @endrole
                </div>
            </div>
            <div class="flex lg:hidden gap-4 items-center">
                <form
                    action="{{ route('app.auth.logout') }}"
                    method="post"
                    class="contents"
                >
                    @csrf
                    <x-ui.dropdown.item as="button" type="submit">
                        <x-ui.icon name="arrow-left-start-on-rectangle" variant="solid" class="text-white"/> 
                    </x-ui.dropdown.item>
                </form>
            </div>

            <div class="hidden gap-4 lg:flex lg:items-center lg:justify-end">
                @auth
                    <x-user-dropdown/>
                @endauth

                <x-ui.separator 
                    class="my-2" 
                    vertical
                />

                <x-ui.theme-switcher variant="inline"/>
            </div>
    </x-ui.navbar>

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
