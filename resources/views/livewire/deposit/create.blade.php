<div class="mx-auto space-y-20 mt-20">
    <div>
        <x-ui.heading>Charter Deposit</x-ui.heading>
        <x-ui.text class="opacity-50">Generate deposit receipt untuk charter flights</x-ui.text>

        <x-ui.separator class="my-2"/>
     
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item wire:navigate.hover :href="route('deposit')">List Deposit</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Receipt</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <div class="grow">
            <form 
                wire:submit="saveChanges"
                class="mt-8 space-y-4 rounded-lg bg-white p-6 dark:bg-neutral-800/10"
            >
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Tanggal Tagihan</x-ui.label>
                            <x-ui.input
                                type="date"
                                wire:model="form.receipt_date"
                                placeholder="Dari"
                                class="
                                    dark:[color-scheme:dark]
                                    dark:[&::-webkit-calendar-picker-indicator]:invert
                                "
                            />
                            <x-ui.error name="form.receipt_date" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Receipt Number</x-ui.label>
                            <x-ui.input
                            disabled
                            wire:model.defer="form.receipt_number"
                            maxlength="80"
                            />
                            <x-ui.error name="form.receipt_number" />
                        </x-ui.field>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Perusahaan Ditagih</x-ui.label>
                            <x-ui.input
                            wire:model.defer="form.company"
                            maxlength="80"
                            placeholder="PT Avia"
                            />
                            <x-ui.error name="form.company" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Penanda Tangan</x-ui.label>
                            <x-ui.input
                            wire:model.defer="form.signer"
                            maxlength="80"
                            placeholder="ANTONIUS"
                            />
                            <x-ui.error name="form.signer" />
                        </x-ui.field>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Cabang</x-ui.label>
                            <x-ui.select 
                                placeholder="Select branch..."
                                icon="ps:airplane-takeoff"
                                wire:model.live="form.branch_id"
                                >
                                @foreach($branches as $branch)
                                    <x-ui.select.option value="{{ $branch->id }}">
                                    {{ $branch->name }}
                                    </x-ui.select.option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.error name="form.branch_id" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Fee</x-ui.label>
                            <x-ui.input 
                                wire:model="form.value"
                                placeholder="0.00">
                                <x-slot name="prefix">Rp</x-slot>
                            </x-ui.input>
                        </x-ui.field>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <x-ui.field required>
                            <x-ui.label>Deskripsi</x-ui.label>
                            <x-ui.textarea 
                                wire:model="form.description" 
                                placeholder="Deskripsi tagihan"
                            />
                            <x-ui.error name="form.description" />
                        </x-ui.field>
                    </div>
                    <div class="col-span-6">
                        {{-- nan --}}
                    </div>
                </div>
                
                <div class="flex items-center justify-between mt-6">
                    <x-ui.button type="submit">Generate Invoice</x-ui.button>
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
    <x-ui.modal 
        id="delete-modal"
        position="center"
        heading="Delete Branch"
        description="Yakin ingin menghapus bukti deposit ini?"
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