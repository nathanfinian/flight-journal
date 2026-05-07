<?php

namespace App\Exports;

use App\Models\Invoice_gse as GseInvoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GseInvoiceRecapExport implements FromView
{
    public function __construct(private readonly GseInvoice $invoice)
    {
    }

    public function view(): View
    {
        $invoice = $this->invoice->load([
            'gseType:id,service_name',
            'branch:id,name',
            'airline:id,name',
            'recaps' => fn ($query) => $query
                ->with([
                    'gseType:id,service_name',
                    'branch:id,name',
                    'airline:id,name',
                    'equipment:id,registration',
                ])
                ->orderBy('service_date')
                ->orderBy('flight_number'),
        ]);

        return view('exports.gse-invoice-recap', [
            'invoice' => $invoice,
        ]);
    }
}
