<?php

namespace App\Http\Controllers\Export;

use App\Exports\GseInvoiceRecapExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice_gse as GseInvoice;
use Maatwebsite\Excel\Facades\Excel;

class GseInvoiceRecapExportController extends Controller
{
    public function export(GseInvoice $invoice)
    {
        $invoiceNumber = str($invoice->invoice_number)
            ->replaceMatches('/[^A-Za-z0-9_\-]+/', '-')
            ->trim('-');

        return Excel::download(
            new GseInvoiceRecapExport($invoice),
            'GSE Invoice Recap ' . (string) $invoiceNumber . '.xlsx'
        );
    }
}
