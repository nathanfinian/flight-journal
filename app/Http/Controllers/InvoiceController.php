<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function print(string $invoice)
    {
        $invoice = Invoice::where('invoice_number', $invoice)
            ->with([
                'rate',
                'airline',
                'branch',
            ])
            ->firstOrFail();

        return view('print.invoice', compact('invoice'));
    }
}
