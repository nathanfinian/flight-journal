<?php

namespace App\Livewire\Deposit;

use App\Livewire\Forms\DepositForm;
use App\Models\Branch;
use App\Models\Deposit;
use App\Traits\GenerateDepositNumber;
use Livewire\Component;

class Create extends Component
{
    use GenerateDepositNumber;

    public DepositForm $form;

    public $deposit;
    public $branches;

    public bool $isEdit = false;

    /* =======================
     | Lifecycle
     ======================= */
    public function mount(?int $id = null)
    {
        if (session()->has('deposit')) {
            $this->form->branch_id  = session('deposit.branch', 1);

            if (empty($this->form->receipt_number) && $this->form->branch_id != null) {
                $this->form->receipt_number = $this->generateDepositNumber((int) $this->form->branch_id);
            }

            //Clear session after extraction
            session()->forget('deposit');
        }

        $this->branches = Branch::query()
            ->where('status', 'ACTIVE')
            ->orderBy('name')
            ->get();

        

        if ($id == null) {
            $this->form->receipt_date = now()->format('Y-m-d');

            return; // go to create, when no data retrieved
        } 
        else{
            $this->isEdit = true;
            $this->deposit = Deposit::findOrFail($id);

            $this->form->setRecord($this->deposit);
        }
    }

    /* =======================
     | Persist
     ======================= */
    public function saveChanges()
    {
        if ($this->form->record) {
            $deposit = $this->form->update()->receipt_number;
        } else {
            $deposit = $this->form->store()->receipt_number;
        }

        session()->flash('notify', [
            'content' => $deposit . ' berhasil disave',
            'type'    => 'success',
        ]);

        return $this->redirectRoute('deposit', navigate: true);
    }

    public function delete()
    {
        $this->form->delete();

        session()->flash('notify', [
            'content' => 'Deposit berhasil dihapus!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('deposit');
    }

    public function updatedFormBranchId($value)
    {
        $this->form->receipt_number = $this->generateDepositNumber((int) $this->form->branch_id);
    }

    public function render()
    {
        return view('livewire.deposit.create');
    }
}
