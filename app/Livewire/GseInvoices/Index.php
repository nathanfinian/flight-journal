<?php

namespace App\Livewire\GseInvoices;

use App\Models\Invoice_gse as GseInvoice;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    private int $perPage = 30;

    public function openEdit(int $id)
    {
        return $this->redirectRoute('invoicegse.edit', ['id' => $id], navigate: true);
    }

    public function render()
    {
        $invoices = GseInvoice::query()
            ->with([
                'gseType:id,type_name',
                'branch:id,name',
                'airline:id,name',
                'recaps.gseType:id,type_name',
            ])
            ->withCount('recaps')
            ->withSum('invoiceRecaps', 'amount')
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('livewire.gse-invoices.index', compact('invoices'));
    }
}
