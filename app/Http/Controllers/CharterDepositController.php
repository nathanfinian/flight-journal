<?php

namespace App\Http\Controllers;

use App\Models\Deposit;

class CharterDepositController extends Controller
{
    public function print(string $deposit)
    {
        $deposit = Deposit::where('receipt_number', $deposit)
            ->with([
                'branch',
            ])
            ->firstOrFail();

        return view('print.deposit', compact('deposit'));
    }
}
