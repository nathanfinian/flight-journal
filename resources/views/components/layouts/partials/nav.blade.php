{{-- this navbar used in app pages (when the user authenticated)  --}}
 @php
     $user = Auth::user();
 @endphp

<header 
    class="border-b-neutral-100 dark:border-b-neutral-800 dark:bg-neutral-900 bg-neutral-50 fixed inset-x-0 top-0 z-50 border-b"
>
    <x-ui.navbar class="mx-auto flex items-center justify-between px-6 py-3 text-base-100 lg:px-8" aria-label="global">
            <div class="flex items-center pr-7">
                <x-app.logo />
                <div class="ml-8 hidden gap-4 lg:flex">
                    <x-ui.navbar.item 
                        wire:navigate.hover
                        icon="chart-pie"
                        icon:class="w-5 h-5" 
                        label="Dashboard" 
                        :href="route('dashboard')" 
                        :active="request()->is('dashboard')"
                    />
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
                                icon="ps:car-simple"
                                icon:class="w-5 h-5" 
                                label="GSE" 
                                :active="request()->is('gse*')"
                            />
                        </x-slot:button>
                        
                        <x-slot:menu>
                            <x-ui.dropdown.group label="Data GSE">
                                <x-ui.dropdown.separator />
                                <x-ui.dropdown.item icon="ps:notepad" :href="route('rekapgse')">
                                    Rekap
                                </x-ui.dropdown.item>

                                <x-ui.dropdown.item icon="document-text" :href="route('rategse')">
                                    Biaya
                                </x-ui.dropdown.item>
                                <x-ui.dropdown.separator />
                                {{-- Sub Menu For GSE Equipments Registration --}}
                                <x-ui.dropdown.submenu label="Equipments" icon="ps:car-profile">
                                    <x-ui.dropdown.item icon="ps:files">
                                        Tipe
                                    </x-ui.dropdown.item>
                                    
                                    <x-ui.dropdown.item icon="ps:tire">
                                        List Equipment
                                    </x-ui.dropdown.item>
                                </x-ui.dropdown.submenu>
                                {{-- Sub Menu for GSE Inventory inputs --}}
                                <x-ui.dropdown.submenu label="Inventory" icon="ps:warehouse">
                                    <x-ui.dropdown.item icon="ps:file">
                                        Kategori
                                    </x-ui.dropdown.item>
                                    <x-ui.dropdown.item icon="ps:cube">
                                        List Barang
                                    </x-ui.dropdown.item>
                                    <x-ui.dropdown.item icon="ps:ruler" disabled>
                                        Tipe Satuan
                                    </x-ui.dropdown.item>
                                </x-ui.dropdown.submenu>

                                <x-ui.dropdown.separator />
                                {{-- Transaksi Inventory GSE --}}
                                <x-ui.dropdown.item icon="ps:arrows-left-right" :href="route('rategse')">
                                    Transaksi
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
                                 <x-ui.dropdown.item icon="document-plus" :href="route('deposit')">
                                    Deposit/Talangan
                                </x-ui.dropdown.item>
                                @role('admin', 'finance')
                                    <x-ui.dropdown.item icon="ps:newspaper" :href="route('invoice')">
                                        Regular Invoice
                                    </x-ui.dropdown.item>
                                    <x-ui.dropdown.item icon="document" :href="route('invoicegse')">
                                        GSE Invoice
                                    </x-ui.dropdown.item>
                                    <x-ui.dropdown.item disabled icon="document" :href="route('invoice')">
                                        Charter Invoice
                                    </x-ui.dropdown.item>
                                @endrole
                                
                                @role('operation')
                                <x-ui.dropdown.item disabled icon="ps:newspaper" :href="route('invoice')">
                                    Regular Invoice
                                </x-ui.dropdown.item>

                                <x-ui.dropdown.item disabled icon="document" :href="route('invoice')">
                                    Charter Invoice
                                </x-ui.dropdown.item>

                                <x-ui.dropdown.item disabled icon="document" :href="route('invoice')">
                                    GSE Invoice
                                </x-ui.dropdown.item>
                                @endrole
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
            <div class="flex items-center gap-2 lg:hidden">
                <x-ui.modal.trigger id="mobile-nav-menu">
                    <button type="button"
                            class="inline-flex items-center justify-center rounded-field p-2 text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-white/5"
                            aria-label="Open navigation menu">
                        <svg class="h-6 w-6"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="1.5"
                             stroke="currentColor"
                             aria-hidden="true">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </x-ui.modal.trigger>
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

    <x-ui.modal
        id="mobile-nav-menu"
        slideover
        width="sm"
        backdrop="dark"
        class="lg:hidden"
        :close-button="true"
    >
        <div class="mb-6 flex items-center">
            <x-app.logo />
        </div>

        <div class="-my-4 divide-y divide-neutral-200 dark:divide-white/10">
            @auth
                <div class="py-4">
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Signed in as</p>
                    <p class="mt-1 text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $user->name }}</p>
                    <a wire:navigate.hover
                       href="{{ route('settings.account') }}"
                       x-on:click="$modal.close('mobile-nav-menu')"
                       class="mt-3 inline-flex rounded-field bg-neutral-100 px-3 py-1.5 text-sm text-neutral-700 dark:bg-white/10 dark:text-neutral-200">
                        Account
                    </a>
                </div>

                <div class="space-y-1 py-4">
                    <a wire:navigate.hover
                       href="{{ route('dashboard') }}"
                       x-on:click="$modal.close('mobile-nav-menu')"
                       class="block rounded-field px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-white/5">
                        Dashboard
                    </a>

                    <div class="rounded-field" x-data="{ expanded: false }">
                        <button
                            type="button"
                            x-on:click="expanded = ! expanded"
                            class="flex w-full items-center justify-between rounded-field px-3 py-2 text-left text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-white/5"
                        >
                            <span>Flights</span>
                            <x-ui.icon
                                name="chevron-down"
                                class="h-4 w-4 transition-transform duration-200"
                                x-bind:class="expanded ? 'rotate-180' : ''"
                            />
                        </button>
                        <div
                            x-show="expanded"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            x-cloak
                            class="mt-1 space-y-1 pl-4"
                        >
                            <a wire:navigate.hover href="{{ route('flight-journal') }}" x-on:click="$modal.close('mobile-nav-menu')" class="block rounded-field px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5">Jurnal</a>
                            <a wire:navigate.hover href="{{ route('flight-schedule') }}" x-on:click="$modal.close('mobile-nav-menu')" class="block rounded-field px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5">Penjadwalan</a>
                            <a wire:navigate.hover href="{{ route('flight-history') }}" x-on:click="$modal.close('mobile-nav-menu')" class="block rounded-field px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5">Sejarah</a>
                        </div>
                    </div>

                    <div class="rounded-field" x-data="{ expanded: false }">
                        <button
                            type="button"
                            x-on:click="expanded = ! expanded"
                            class="flex w-full items-center justify-between rounded-field px-3 py-2 text-left text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-white/5"
                        >
                            <span>GSE</span>
                            <x-ui.icon
                                name="chevron-down"
                                class="h-4 w-4 transition-transform duration-200"
                                x-bind:class="expanded ? 'rotate-180' : ''"
                            />
                        </button>
                        <div
                            x-show="expanded"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            x-cloak
                            class="mt-1 space-y-1 pl-4"
                        >
                            <a wire:navigate.hover href="{{ route('rekapgse') }}" x-on:click="$modal.close('mobile-nav-menu')" class="block rounded-field px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5">Rekap</a>
                            <a wire:navigate.hover href="{{ route('rategse') }}" x-on:click="$modal.close('mobile-nav-menu')" class="block rounded-field px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5">Biaya</a>
                        </div>
                    </div>

                    <div class="rounded-field" x-data="{ expanded: false }">
                        <button
                            type="button"
                            x-on:click="expanded = ! expanded"
                            class="flex w-full items-center justify-between rounded-field px-3 py-2 text-left text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-white/5"
                        >
                            <span>Invoicing</span>
                            <x-ui.icon
                                name="chevron-down"
                                class="h-4 w-4 transition-transform duration-200"
                                x-bind:class="expanded ? 'rotate-180' : ''"
                            />
                        </button>
                        <div
                            x-show="expanded"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            x-cloak
                            class="mt-1 space-y-1 pl-4"
                        >
                            <a wire:navigate.hover href="{{ route('deposit') }}" x-on:click="$modal.close('mobile-nav-menu')" class="block rounded-field px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5">Deposit/Talangan</a>
                            @role('admin', 'finance')
                                <a wire:navigate.hover href="{{ route('invoice') }}" x-on:click="$modal.close('mobile-nav-menu')" class="block rounded-field px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5">Regular Invoice</a>
                                <a wire:navigate.hover href="{{ route('invoicegse') }}" x-on:click="$modal.close('mobile-nav-menu')" class="block rounded-field px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5">GSE Invoice</a>
                                <span class="block rounded-field px-3 py-2 text-sm text-neutral-400 dark:text-neutral-500">Charter Invoice (Soon)</span>
                            @endrole
                            @role('operation')
                                <span class="block rounded-field px-3 py-2 text-sm text-neutral-400 dark:text-neutral-500">Regular Invoice</span>
                                <span class="block rounded-field px-3 py-2 text-sm text-neutral-400 dark:text-neutral-500">Charter Invoice</span>
                                <span class="block rounded-field px-3 py-2 text-sm text-neutral-400 dark:text-neutral-500">GSE Invoice</span>
                            @endrole
                        </div>
                    </div>

                    <a wire:navigate.hover
                       href="{{ route('settings.index') }}"
                       x-on:click="$modal.close('mobile-nav-menu')"
                       class="block rounded-field px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-white/5">
                        Settings
                    </a>

                    @role('admin')
                        <a wire:navigate.hover
                           href="{{ route('admin.index') }}"
                           x-on:click="$modal.close('mobile-nav-menu')"
                           class="block rounded-field px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-white/5">
                            Admin
                        </a>
                    @endrole
                </div>

                <div class="space-y-3 py-4">
                    <x-ui.theme-switcher variant="inline"/>
                    <form action="{{ route('app.auth.logout') }}"
                          method="post">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-field bg-neutral-900 px-3 py-2 text-sm font-medium text-white dark:bg-white dark:text-neutral-900">
                            Sign Out
                        </button>
                    </form>
                </div>
            @endauth

            @guest
                <div class="py-4">
                    <a href="{{ route('login') }}"
                       x-on:click="$modal.close('mobile-nav-menu')"
                       class="-mx-3 block rounded-field px-3 py-2.5 text-base font-semibold leading-7 text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-white/5">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       x-on:click="$modal.close('mobile-nav-menu')"
                       class="mt-2 block rounded-md border border-neutral-300 bg-neutral-100 px-3 py-2.5 text-base font-semibold text-neutral-800 dark:border-white/20 dark:bg-white/10 dark:text-neutral-100">
                        Register
                    </a>
                </div>
            @endguest
        </div>
    </x-ui.modal>
</header>
