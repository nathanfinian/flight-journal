<?php

namespace App\Livewire\Forms;

use App\Models\Deposit;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DepositForm extends Form
{
    public ?Deposit $record = null;

    #[Validate('required|date')]
    public string $receipt_date = '';

    #[Validate('required|string|min:3|max:80')]
    public string $receipt_number = '';

    #[Validate('required|string|min:3|max:80')]
    public string $company = '';

    #[Validate('required|string|min:3|max:80')]
    public string $signer = '';

    #[Validate('required|exists:branches,id')]
    public string $branch_id = '';

    #[Validate('required|numeric|min:0')]
    public string $value = '';

    #[Validate('required|string|max:1000')]
    public string $description = '';

    /* =======================
    | Set record (Edit Mode)
    ======================= */
    public function setRecord(Deposit $deposit): void
    {
        $this->record = $deposit;

        $this->receipt_number = $deposit->receipt_number;
        $this->receipt_date   = $deposit->receipt_date?->format('Y-m-d');
        $this->company        = $deposit->received_from_company;
        $this->signer         = $deposit->signer_name;
        $this->branch_id      = (string) $deposit->branch_id;
        $this->description    = $deposit->description;
        $this->value          = (string) $deposit->value;
    }

    /* =======================
     | Create invoice
     ======================= */
    public function store(): Deposit
    {
        $this->validate();

        return Deposit::create([
            'receipt_number'        => $this->receipt_number,
            'branch_id'             => $this->branch_id,
            'received_from_company' => $this->company,
            'signer_name'           => $this->signer,
            'receipt_date'          => $this->receipt_date,
            'description'           => $this->description,
            'value'                 => $this->value ?: 0,
        ]);
    }

    /* =======================
    | Update record
    ======================= */
    public function update(): Deposit
    {
        if (!$this->record) {
            throw new \Exception('No record selected for update.');
        }

        $this->validate([
            'receipt_date'   => 'required|date',
            'receipt_number' => 'required|string|min:3|max:80|unique:deposit_receipts,receipt_number,' . $this->record->id,
            'company'        => 'required|string|min:3|max:80',
            'signer'         => 'required|string|min:3|max:80',
            'branch_id'      => 'required|exists:branches,id',
            'value'          => 'required|numeric|min:0',
            'description'    => 'required|string|max:1000',
        ]);

        $this->record->update([
            'receipt_number'        => $this->receipt_number,
            'branch_id'             => $this->branch_id,
            'received_from_company' => $this->company,
            'signer_name'           => $this->signer,
            'receipt_date'          => $this->receipt_date,
            'description'           => $this->description,
            'value'                 => $this->value ?: 0,
        ]);

        return $this->record;
    }

    /* =======================
    | Delete record
    ======================= */
    public function delete(): void
    {
        if (!$this->record) {
            throw new \Exception('No record selected for deletion.');
        }

        $this->record->delete();

        $this->reset();
    }
}
